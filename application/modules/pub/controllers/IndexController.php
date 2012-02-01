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
 * @package      DA_Pub
 * @subpackage   Index
 * @file         IndexController.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 * 
 */

/**
 * Controlador para a página principal
 *
 * @package      DA_Pub
 * @subpackage   Index
 *
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 *
 */
class Pub_IndexController extends Zend_Controller_Action
{

    /**
     * @var string|DA_Form_Acl_Login
     */
    private $_form;
    
    /** 
     * Inicialização dos recursos
     * 
     * @see Zend_Controller_Action::init()
     */
    public function init ()
    {
        
    }
    
    /**
     * Página inicial 
     */
    public function indexAction ()
    {

        
    }
    
    /**
     * Apaga a identidade do cliente, se ela existir 
     */
    public function logoutAction ()
    {
        if(DA_Plugin_Auth::logout()){
            $this->_helper->flashMessenger('Você saiu do sistema com sucesso!');
        }
        // Redireciona para o formulário de login
        return $this->_redirect('/index/login');
        
    }
    /**
     * Prepara o formulário de login 
     */
    private function _prepareForm ()
    {
        $this->_form = new DA_Form_Acl_Login();
    }
    
    /**
     * Login 
     */
    public function loginAction ()
    {
        /* Ações gerais */
       // $this->view->headTitle()->prepend('Login');
        $auth = Zend_Auth::getInstance();
        
        // Verifica se o cliente está autenticado
        if($auth->hasIdentity()){
            
            echo 'Você já está autenticado desde ' . date('d/m/Y H:i:s', $auth->getIdentity()->login_time);

        //    $this->view->title = 'Você já está autenticado';
            
        }else{ // Caso contrário exibe o formulário de login
            
            
            /* Prepara o formulário de login */
            $this->_prepareForm();
            $this->view->form = $this->_form;
            
            // Obtém as mensagens se existirem
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            
            // Obtém a requição
            $request = $this->getRequest();

            $session = Zend_Registry::get('Zend_Session');
            $cache   = Zend_Registry::get('cache');
            
            // Define os dados vindos de um post diretamente ou de um PRG
            if($request->isPost()){
                
                $data = $request->getPost();
                
            }elseif(isset($session->login_post)){ // Os dados vem de um redirecinamento
                
                
            }
            
            if (isset($data)){ // Caso tenha algum dado para verificar...
                
                if($this->_form->isValid($data)){ // ... valida-o com os validadores e filtros definidos no form
                    
                    /* Limita o intervalo de tempo entre as tentativas tornando inviável ataques de força-bruta simples */
                    if($cache->test('lastTry')){                        
                        $interval = 2; // 2 segundos de intervalo é o suficiente para inviabilizar ataques de força-bruta
                        $timeLast = time() - $cache->load('lastTry');
                        
                        if($timeLast < $interval){
                            $this->_helper->flashMessenger("Você está digitando muito rápido! Aguarde ".($interval-$timeLast)."s");
                            return $this->_redirect('/index/login/e/1');
                        }
                    }
                    // Salva o momento da última tentativa de login (system-wide)
                    $cache->save(time(), 'lastTry');
                    
                    /* Valores sanitizados */
                    $username = $this->_form->getValue('username');  // Usuário
                    $password = $this->_form->getValue('password');  // Senha
                    $remember = $this->_form->getValue('remember');  // Estado persistente?
                    
                    
                    // Executa o processo de autenticação
                    if($userId = DA_Plugin_Auth::doAuth(
                            'user',
                            array(
                                'column' => 'username',
                                'value'  => $username,
                            ), array(
                                'column' => 'password',
                                'value'  => $password,
                            )
                    )){
                        
                        // Persiste a sessão em um cookie
                        if($remember){
                            DA_Plugin_Auth::doPersist($userId, 30);
                        }
                                                
                        if ($urlRedir = $session->url_redir) {
                            unset($session->url_redir);
                            return $this->_redirect($urlRedir);
                            
                        } else {
                        // Redireciona para a ação adequada
                            return $this->_helper->redirector->goToRoute( 
                                    array(
                                            'module'     => 'equipe',
                                            'controller' => 'index',
                                            'action'     => 'index',
                                    ), null, true);
                            
                        }
                    }else{
                      
                        $this->_helper->flashMessenger('Usuário ou senha inválidos!');
                        return $this->_redirect('/index/login');
                    }
                    
                }elseif($request->isPost()){
                    /* Implementa a abordagem PRG para os dados inválidos */
//                     $session->login_post = serialize($data); // Armazena o POST em uma sessão :(yes, it's bad!)
//                     $this->_redirect('/index/login'); // Redireciona de volta, agora sem o POST
                }
            }
        }
    }
}

