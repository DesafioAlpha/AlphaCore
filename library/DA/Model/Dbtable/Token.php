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
 * @file         Token.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */

class DA_Model_Dbtable_Token extends Zend_Db_Table_Abstract
{
    protected $_name    = 'token';
    protected $_primary = 'token_id';
    protected $_staticSalt;
    
    public function init()
    {
        $constants = Zend_Registry::get('constants');
        $this->_staticSalt = $constants['staticSalt'];
        
    }
    public function addToken ($user_id)
    {
        $token = md5($user_id . time());
        $tokenSalt = DA_Lib_Math::hash('md5');
        
        $this->insert(array(
                'user_id'          => $user_id,
                'token'            => md5($token . $tokenSalt . $this->_staticSalt),
                'token_salt'       => $tokenSalt,
                'creation_time'    => new Zend_Db_Expr('NOW()')
        ));
        
        return $token;
    }
    
    public function getUser ($tokenData)
    {
        $select = $this->select();
        
        $select->from($this, 'user_id')
//                ->where('user_id = ?', $userId)
               ->where("token = MD5(CONCAT(?, token_salt, '{$this->_staticSalt}'))", $tokenData);
        
        if($response = $this->fetchRow($select)){
            $response = $response->toArray();
            return $response['user_id'];
        }
        
        return false;
    }
}
