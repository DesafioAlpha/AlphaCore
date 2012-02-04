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
 * @package      DA_
 * @file         Login.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */

class DA_Helper_Login extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var string|DA_Form_Auth_Login
     */
    private $_form;
    
    /**
     * @var Zend_View
     */
    public $view;
    
    public function __construct($actionController)
    {
        $this->setActionController($actionController);
        $this->view = $this->_actionController->view;
        $this->getRequest()->setModuleName('pub');
        $this->view->setScriptPath(APPLICATION_PATH . '/modules/pub/views/scripts/');
        $this->flashMessenger = $this->_actionController->getHelper('flashMessenger');
    }
    
    /**
     * Implementa a função de redirecionamento
     * 
     * @return mixed
     */
    private function _redirect()
    {
        return call_user_method_array('gotoUrl', $this->_actionController->getHelper('redirector'), func_get_args());
        
    }
    
    /**
     * Prepara o formulário de login
     */
    private function _prepareForm ()
    {
        $this->_form = new DA_Form_Auth_Login();
    }
    public function doLogout()
    {
        if(DA_Plugin_Auth::logout()){
            $this->flashMessenger->addMessage('Você saiu do sistema com sucesso!');
           
            return true;
        }    
    }
    
    public function doLogin()
    {
        /* Ações gerais */
        // $this->view->headTitle()->prepend('Login');
        $auth = Zend_Auth::getInstance();
        
        // Verifica se o cliente está autenticado
        if($auth->hasIdentity()){
        
            echo 'Você já está autenticado desde ' . date('d/m/Y H:i:s', $auth->getIdentity()->login_time);
                
        }else{ // Caso contrário exibe o formulário de login
                
            /* Prepara o formulário de login */
            $this->_prepareForm();
            $this->view->form = $this->_form;
                        
            // Obtém as mensagens se existirem
            $this->view->messages = $this->flashMessenger->getMessages();
        
            // Obtém a requição
            $request = $this->getRequest();
        
            $session = Zend_Registry::get('Zend_Session');
            $cache   = Zend_Registry::get('cache');
        
            // Define os dados vindos de um post diretamente ou de um PRG
            if($request->isPost()){
        
                $data = $request->getPost();
        
            }
        
            if (isset($data)){ // Caso tenha algum dado para verificar...
        
                if($this->_form->isValid($data)){ // ... valida-o com os validadores e filtros definidos no form
        
                    /* Limita o intervalo de tempo entre as tentativas tornando inviável ataques de força-bruta simples */
                    if($cache->test('lastTry')){
                        $interval = 2; // 2 segundos de intervalo é o suficiente para inviabilizar ataques de força-bruta
                        $timeLast = time() - $cache->load('lastTry');
        
                        if($timeLast < $interval){
                            $this->flashMessenger->addMessage("Você está digitando muito rápido! Aguarde ".($interval-$timeLast)."s");
                            return $this->_redirect($this->view->url());
                        }
                    }
                    // Salva o momento da última tentativa de login (system-wide)
                    $cache->save(time(), 'lastTry');
        
                    /* Valores sanitizados */
                    $username = $this->_form->getValue('username');  // Usuário
                    $password = $this->_form->getValue('password');  // Senha
                    $remember = $this->_form->getValue('remember');  // Estado persistente?
        
                    $urlRedir = $session->url_redir;
                    
                    // Executa o processo de autenticação
                    if($userId = DA_Plugin_Auth::doAuth($username, $password, $urlRedir, $remember)){
       
                        if ($urlRedir) {
                            unset($session->url_redir);
                            return $this->_redirect($urlRedir);
                        }
                        
                        return true;
                        
                    }else{
        
                        $this->flashMessenger->addMessage('Usuário ou senha inválidos!');
                        
                        return $this->_redirect($this->view->url());
                    }
        
                }
            }
        }
    }
}