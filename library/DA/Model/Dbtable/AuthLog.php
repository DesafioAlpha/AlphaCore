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
 * @package      DA_Models
 * @file         AuthLog.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */

class DA_Model_Dbtable_AuthLog extends Zend_Db_Table_Abstract
{
    protected $_name = 'auth_log';
    protected $_primary = 'auth_log_id';
    
    /**
     * Adiciona um registro de tentativa de login
     * 
     * @param string $postIdentity
     * @param int $postRememberme
     * @param string $urlAction
     * @param string $urlReferer
     * @param int $fromCookie
     * @param int $isSuccessfully
     * @param string $userId
     * 
     * @return mixed
     * 
     */
    public function addLog( $urlAction, $urlReferer, $logout, $userId = '', $postIdentity = '', $postRememberme = '', $fromCookie = 0, $isSuccessfully = 0)
    {
        $data = array();
        
        $data['logout']          = $logout;
        $data['post_identity']   = $postIdentity;
        $data['post_rememberme'] = $postRememberme;
        
        $data['url_action']      = $urlAction;
        $data['url_referer']     = $urlReferer;
        
        $data['from_cookie']     = $fromCookie;
        $data['user_id']         = $userId;
        $data['is_successfully'] = $isSuccessfully;
        $data['user_id']         = $userId;
        
        $data['client_ip']       = $_SERVER['REMOTE_ADDR'];
        $data['client_ua']       = $_SERVER['HTTP_USER_AGENT'];
        
        $data['time']            = new Zend_Db_Expr('NOW()');
        
        return $this->insert($data);
        
    }
}