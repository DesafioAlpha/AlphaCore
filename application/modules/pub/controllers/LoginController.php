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
 * @subpackage   Login
 * @file         LoginController.php
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
 * @subpackage   Login
 *
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 *
 */
class Pub_LoginController extends Zend_Controller_Action
{   
    /** 
     * Inicialização dos recursos
     * 
     * @see Zend_Controller_Action::init()
     */
    public function init ()
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
        return $this->_redirect('/login/');
        
    }

    
    /**
     * Login 
     */
    public function indexAction ()
    {
        $login = new DA_Helper_Login($this);
        if($login->doLogin()){
            // Redireciona para a ação adequada
            return $this->_redirect('/equipe');
        }
    }
}

