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
    
    public function init ()
    {
        $constants = Zend_Registry::get('constants');
        $this->_staticSalt = $constants['staticSalt'];
        
    }
    /**
     * Remove um token específico
     * 
     * @param string $tokenData
     * 
     * @return DA_Model_Dbtable_Token
     */
    public function removeToken ($tokenData)
    {
        $where = $this->getAdapter()->quoteInto("token = MD5(CONCAT(?, token_salt, '{$this->_staticSalt}'))", $tokenData);
        $this->delete($where);
        
        return $this;
    }
    
    /**
     * Remove todos os tokens atribuídos a este usuário
     * 
     * @param string $user_id
     * 
     * @return DA_Model_Dbtable_Token
     */
    public function removeAllFromUser ($user_id)
    {
        $where = $this->getAdapter()->quoteInto('user_id = ?', $user_id);
        $this->delete($where);
        
        return $this;
    }
    
    /**
     * Remove os tokens vencidos
     *  
     * @return DA_Model_Dbtable_Token
     */
    public function removeOld ()
    {
        $this->delete('expiration_time <= NOW()');
        
        return $this;
    }
    
    /**
     * Cria um token para o user_id especificado
     * 
     * @param string $user_id
     * @param int $expiration Timestamp para o momento de vencimento do token
     * 
     * @return string
     */
    public function addToken ($user_id, $expiration)
    {
        $token = md5($user_id . time());
        $tokenSalt = DA_Lib_Math::hash('md5');
        
        $expiration_time = $this->getAdapter()->quoteInto('FROM_UNIXTIME(?)', $expiration, 'INTEGER');
        
        $this->insert(array(
                'user_id'          => $user_id,
                'token'            => md5($token . $tokenSalt . $this->_staticSalt),
                'token_salt'       => $tokenSalt,
                'expiration_time'  => new Zend_Db_Expr($expiration_time)
        ));
        
        return $token;
    }
    
    /**
     * Retorna o user_id correspondente ao token, se válido
     * 
     * @param string $tokenData
     * 
     * @return string|boolean Retorna false caso não encontre o token especificado ou ele está vencido
     */
    public function getUser ($tokenData)
    {
        $select = $this->select();
        
        $select->from($this, 'user_id')
               ->where("token = MD5(CONCAT(?, token_salt, '{$this->_staticSalt}')) AND expiration_time >= NOW()", $tokenData);
        
        if($response = $this->fetchRow($select)){
            $response = $response->toArray();
            return $response['user_id'];
        }
        
        return false;
    }
}
