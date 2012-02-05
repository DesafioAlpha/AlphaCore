<?php
/**
 * Desafio Alpha - AlphaCore
 * 
 * LICENÇA:
 * 
 * Este software é distribuído sob a licença GNU General Public License - Versão 3. 
 * O conteúdo desta licença está disponível no arquivo LICENSE.txt e nos endereços web 
 * http://doc.desafioalpha.com.br/legal e http://www.gnu.org/licenses/gpl.html
 * 
 * Qualquer dúvida sobre este arquivo fonte e como usá-lo refira-se à 
 * documentação anexada à este pacote de software ou envie um email 
 * para dev@desafioalpha.com.br
 * 
 * ESTE SOFTWARE É DISPONIBILIZADO 'NA FORMA COMO ESTÁ', O AUTOR NÃO OFERECE 
 * NENHUMA GARANTIA, EXPLÍCITA OU IMPLÍCITA, SOBRE PRECISÃO E CONFIABILIDADE.
 * SINTA-SE LIVRE PARA VER, MODIFICAR E REDISTRIBUIR, SOB AS CONDIÇÕES DA 
 * LICENÇA APLICADA. 
 *
 * @category     Desafio Alpha
 * @package      DA_Plugins
 * @file         Auth.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */

/**
 * Plugin para ações de autenticação 
 *
 * @package      DA_Plugins
 * @subpackage   Auth
 *
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 *
 */
class DA_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var array
     */
    private $_roles;
    
    /**
     * @var Zend_Session_Namespace
     */
    private $_session;
    
    /**
     * @var array Campos a serem buscados no BD e gravados na sessão
     */
    public static $_userFields = array('user_id', 'role_id', 'last_login');
    
    /**
     * @see Zend_Controller_Plugin_Abstract::preDispatch()
     */
    public function preDispatch($request)
    {
        $auth = Zend_Auth::getInstance();
        $acl  = new Zend_Acl();
        
        $this->_session = Zend_Registry::get('Zend_Session');
        
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        
        $this->_roles = array(
            array('name' => 'guest',  'parents' => null),    
            array('name' => 'team',   'parents' => 'guest'),
            array('name' => 'person', 'parents' => 'team'),
            array('name' => 'school', 'parents' => null),
            array('name' => 'admin',  'parents' => array('team', 'school')),
        );
        
        $role = 'guest'; // Role padrão
        
        // Expõe a ACL para o navigation
        $view->navigation()->setAcl($acl)
                           ->setRole($role);
        
        /* Define as permissões */
        foreach ($this->_roles as $roleData){
            $acl->addRole($roleData['name'], $roleData['parents'])
                ->addResource($roleData['name'])
                ->allow($roleData['name'], $roleData['name']);
        }
                
        if(!$auth->hasIdentity() && isset($_COOKIE['RID'])){ // Verifica por um cookie de persistência
            self::doCookieAuth($_COOKIE['RID']);
        }
        
        /* Define restrições para os links */
        if($auth->hasIdentity() && $role = $this->_roles[$auth->getIdentity()->role_id]['name']){ //
           $view->navigation()->setRole($role);                
        }
        
        /* Verifica se o cliente está autorizado para o recurso em questão */
        if($thisPage = $view->navigation()->findBy('active', true)){
            if($thisPage->getResource() && !$acl->isAllowed($role, $thisPage->getResource())){ // Proibido!
            
                // Passa a URL requisitada para a próxima autenticação
                if(!$auth->hasIdentity()){
                    $this->_session->url_redir = $_SERVER['REQUEST_URI'];
                }
                
                // Exibe o erro de autorização
                $request->setControllerName('error')->setActionName('forbidden');
            }
        }
    }
    
    /**
     * Autentica o cliente a partir de um token de persistência
     * 
     * @param  string $tokenData
     * 
     * @return boolean
     */
    public static function doCookieAuth($tokenData)
    {
        $token = new DA_Model_Dbtable_Token();
        if($userId = $token->getUser($tokenData)){
        
            $user    = new DA_Model_Dbtable_User();
            $authLog = new DA_Model_Dbtable_AuthLog();
        
            if($userData = $user->getUserData($userId, self::$_userFields)){
                /* Remove o token anterior e cria um novo */
                $token->removeToken($tokenData);
                self::doPersist($userId, 30);
        
                Zend_Auth::getInstance()->getStorage()->write($userData);
        
                // Registra a ação
                $authLog->addLog($_SERVER['REQUEST_URI'], null, 0, $userId, $tokenData, 0, 1, 1);
            }
            
            return true;
            
        }else{
            // Tarefa de manutenção: remove todos os tokens vencidos
            // Esta ação foi desativada pois aumentaria a efetividade de um ataque DoS
            //                     $token->removeOld ();
        
            // Apaga o cookie e toda informação de autenticação
            self::logout(true);
            
            return false;
            
        }
    }
    
    /**
     * Executa as operações para autenticação de clientes
     * 
     * @param string  $table                Tabela do BD a ser consultada
     * @param array   $identityData         Array associativo para coluna e valor de identidade
     * @param array   $credentialData       Array associativo para coluna e valor de credencial
     * @param boolean $ambiguity (optional) Determina se deve ser permitida a busca ambígua no BD
     * 
     * return boolean
     */
    public static function doAuth($username, $password, $referer = null, $remember = 0, $days = 30){

        // Adaptador de BD a ser usado pelo componente de autenticação
        $authDb = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        
        $table = "user";
        
        $identityData = array(
            'column' => 'username', 'value' => $username
        );
        $credentialData = array(
            'column' => 'password', 'value' => $password
        );

        /* Sal estático para uso na digestão da senha */
        $constants = Zend_Registry::get('constants');
        $staticSalt = $constants['staticSalt'];
        
        // Parâmetros para a consulta
        $authDb->setTableName($table)
               ->setIdentityColumn($identityData['column'])
               ->setIdentity($identityData['value'])
               
               ->setCredentialColumn($credentialData['column'])
               // A credencial é digerida com o algoritmo MD5 (pode não estar disponível em todos os DBMS suportados)
               ->setCredentialTreatment("MD5(CONCAT(?, {$credentialData['column']}_salt, '$staticSalt'))")
               ->setCredential($credentialData['value']);
        
        /* Instancia o componente de autenticação */
        $auth   = Zend_Auth::getInstance();
        $result = $auth->authenticate($authDb);
        
        /* Parâmetros para registro no logger */
        $authLog = new DA_Model_Dbtable_AuthLog();
        $urlAction = $_SERVER['REQUEST_URI'];
        
        if($result->isValid()){
            
            $identity = $authDb->getResultRowObject(self::$_userFields); // Salva os dados da identidade ignorando a senha
            $identity->login_time = time();
            
            $user = new DA_Model_Dbtable_User();
            $user->setLastLogin($identity->user_id);
            
            $auth->getStorage()->write($identity); // Armazena os dados do usuário retirando a senha
            
            // Persiste a sessão em um cookie
            if($remember){
                self::doPersist($identity->user_id, $days);
            }
            
            // Registra o login
            $authLog->addLog($urlAction, $urlRedir, 0, $identity->user_id, $username, $remember, 0, 1);
            
            return $identity->user_id;
            
        }else{
            // Registra a tentativa sem sucesso
            $authLog->addLog($urlAction, $urlRedir, 0, null, $username, $remember, 0, 0);
        }
        
        return false;
    }
    
    /**
     * @param string $userId   ID do usuário no BD
     * @param float  $days     Número de dias que o cookie de persistência deve permanecer válido 
     */
    public static function doPersist($userId, $days)
    {
        $authToken = new DA_Model_Dbtable_Token();
        $expiration = time() + 3600*24*$days;
        
        $token = $authToken->addToken($userId, $expiration); // Cria um token para este user_id
        
        // Esta data é definida a partir do horário do servidor, podendo ou não ser condizente com o horário do cliente
        setcookie('RID', "$token", $expiration, '/');
    }
    
    /**
     * Elimina qualquer autenticação do cliente
     * 
     * @param  boolean $ignoreDelete Decide se deve ignorar a exclusão do token no BD (proteção contra ataques DoS)
     * 
     * @return boolean
     */
    public static function logout($ignoreDelete = false)
    {
        $auth = Zend_Auth::getInstance();   
          
        /* Remove o token do BD, se houver um */
        if(!$ignoreDelete && $tokenData = $_COOKIE['RID']){
            $token = new DA_Model_Dbtable_Token();
            $token->removeToken($tokenData);
        }
           
        setcookie('RID', FALSE, 1, '/'); // Apaga o cookie de Persistência
        
        if($auth->hasIdentity()){ // Caso exista alguma identidade, apaga-a e exibe uma mensagem de sucesso

            /* Registra a saída */
            $authLog = new DA_Model_Dbtable_AuthLog();
            $urlAction = $_SERVER['REQUEST_URI'];
            $urlReferer = $_SERVER['HTTP_REFERER'];
            $authLog->addLog($urlAction, $urlReferer, 1, $auth->getIdentity()->user_id);
            
            // Apaga os dados armazenados
            $auth->clearIdentity();
            
            return true;
        }
        return false;
    }
}