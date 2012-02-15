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
 * @file         Question.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */

/**
 * Modelo para manipulação de desafios 
 *
 * @package      DA_Models
 * @subpackage   Question
 *
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 *
 */
class DA_Model_Dbtable_Question extends Zend_Db_Table_Abstract
{
    protected $_name = 'question';
    protected $_primary = 'question_id';
    
    /**
     * @var array
     */
    private   $_questionData = array();
    
    /**
     * @var boolean
     */
    private   $_isLoaded = false;
    
    public function init ()
    {
        $this->setDefaultMetadataCache('cache');
    }
    
    /**
     * Verifica se o desafio existe.
     * 
     * @param  string  $questionId Id do desafio
     * @return boolean
     */
    public function questionExist($questionId)
    {
        return ($this->find($questionId)->count() > 0);
    }
    
    /**
     * Salva os dados passados no BD.
     * 
     * @param  array   $questionData
     * @return boolean
     */
    public function saveQuestion ($questionData)
    {
        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $username = $auth->getIdentity()->username; 
        }
        
        $data['text']            = $questionData['text'];
        $data['answer']          = $questionData['answer'];
        $data['edit_lasttime']   = new Zend_Db_Expr('NOW()');
        $data['edit_username']   = $username;
        $data['value']           = $questionData['value'];
        $data['status']          = $questionData['status'];
        
        $where = $this->getAdapter()->quoteInto('question_id = ?', $questionData['id']);
        
        if($this->update($data, $where) > 0){
            return true;
        }
    
        return false;
        
    }
    
    /**
     * Número de desafios registrados.
     * 
     * @return int
     */
    public function countQuestions ()
    {
        $select = $this->select();        
        $select->from($this, 'COUNT(question_id) as question_no');
        $result = $this->fetchRow($select);
        
        return (int) $result['question_no'];
    }
    
    /**
     * Armazena os dados do desafio no objeto para desempenho.
     * 
     * @param string $questionId
     * @return boolean|DA_Model_Dbtable_Question
     */
    public function loadQuestionData ($questionId)
    {
        $select = $this->select();
        $select->from($this, array('question_id', 'text', 'answer', 'value', 'status'))
               ->where('question_id = ?', $questionId);
        
        if($result = $this->fetchRow($select)->toArray()){
            $this->_questionData = $result;
            $this->_isLoaded = true;
        } else {
            throw new Exception('No records were found for specified Question ID!');
        }
        
        return $this;
    }
    
    /**
     * Lista os desafios filtrados de acordo com os parâmetros.
     * 
     * @param  array  $params
     * @return array
     */
    public function listAll(array $params)
    {
        
        $select = $this->select();
        $select->from($this, array('question_id', 'status', 'edit_username', 'edit_lasttime'))
               ->order('edit_lasttime DESC');
        
        if(isset($params['limit'])){
            $select->limit((int) $params['limit']);
        }
        
        if(isset($params['status'])){
            $select->where('status = ?', (int) $params['status']);
        }
        
        return $this->fetchAll($select)->toArray();
        
    }
    
    /**
     * Cria um novo desafio e envia resposta ao cliente.
     * 
     * @return string
     */
    public function newQuestion()
    {
        if(Zend_Auth::getInstance()->hasIdentity()){ // Usuário que criou
            $username = Zend_Auth::getInstance()->getIdentity()->username;
        }
        
        $questionId = DA_Lib_Math::hash(); // Identificador do novo desafio
        
        /* Valores padrão */
        $data['question_id']   = $questionId;
        $data['edit_username'] = $username;
        $data['edit_lasttime'] = new Zend_Db_Expr('NOW()');
        $data['status'] = 1;
        $data['value']  = 0;
        $data['text']   = '';
        $data['answer'] = '';
         
        $this->insert($data);
        
        $data['new']    = true;
        
        return $data;
        
    }
    
    /**
     * Exclui o desafio e todos os recursos associados a ele.
     * 
     * @param  string    $questionId Id do desafio
     * @return boolean
     */
    public function deleteQuestion($questionId) {
        
        $questionMedia = new DA_Model_Dbtable_QuestionMedia();
        if($questionMedia->deleteMedia($questionId)){
        
            $where = $this->getAdapter()->quoteInto('question_id = ?', $questionId);
        
            return ($this->delete($where) !== false);
        }
        
        return false;
    }
    
    /**
     * Getter para os dados do desafio.
     * 
     * @return array
     */
    public function getData()
    {
        return $this->_questionData;
    }
    
    /**
     * Implementa sobrecarga para os getters.
     *
     * @param string     $method
     * @param mixed      $arguments
     * @throws Exception Caso o descritor não exista ou não exista dados no objeto.
     */
    public function __call($method, $arguments){
        
        if($this->_isLoaded){
            $getters = array(
                'Id'     => 'question_id',
                'Text'   => 'text',
                'Answer' => 'answer',
                'Value'  => 'value',
                "Status" => 'status'
            );
            
            if(preg_match('/^get([a-z]*)$/i', $method, $getter) && key_exists($getter[1], $getters)){
                return $this->_questionData[$getters[$getter[1]]];
            }else{
                throw new Exception("The property doesn't exist!");
            }
            
        } else {
            throw new Exception("You must call 'loadQuestionData' first!");
        }
        
    } 
}