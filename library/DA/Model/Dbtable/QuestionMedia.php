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
 * @file         QuestionMedia.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */

/**
 * Modelo para manipulação de recursos de mídia de desafios. 
 *
 * @package      DA_Models
 * @subpackage   QuestionMedia
 *
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 *
 */
class DA_Model_Dbtable_QuestionMedia extends Zend_Db_Table_Abstract
{
    protected $_name = 'question_media';
    protected $_primary = 'question_media_id';
    
    /**
     * @var DA_Model_Dbtable_Question
     */
    private $_question;
    
    public function init()
    {
        $this->setDefaultMetadataCache('cache');
    }
    
    /**
     * Inclui um recurso de mídia para o desafio
     * 
     * @param int $type Tipo de mídia
     * @param string $filename Nome do arquivo
     * @param string $questionId Id do desafio
     * 
     * @return string|boolean Retorna o nome do arquivo ou false em caso de erro
     */
    public function addMedia($type, $filename, $questionId)
    {
        $this->_question = new DA_Model_Dbtable_Question();
        
        if($this->_question->questionExist($questionId)){
            
            $data['question_id'] = $questionId;
            $data['path']        = $filename;
            $data['media_type']  = $type;
            
            $this->insert($data);
            
            return $filename;
        }
  
        return false;
    }
    
    /**
     * Retorna uma lista com todos os recursos de mídia de um desafio,
     * filtra pelo segundo parâmetro.
     * 
     * @param string $questionId Id do desafio
     * @param int $type (optional) Tipo da mídia
     * 
     * @return array
     */
    public function getMedia($questionId, $type = null)
    {
            
        $select = $this->select();
        $select->from($this, array('question_media_id', 'path', 'media_type'))
               ->where('question_id = ?', $questionId)
               ->order('media_type ASC');
        
        if($type){
            $select->where('media_type = ?', $type);
        }    
        
        return $this->fetchAll($select)->toArray();
        
    }
    
    /**
     * Apaga todas ou uma entrada de mídia (se passado) de um desafio
     * 
     * @param string $questionId         Id do desafio
     * @param int    $mediaId (optional) Id da mídia
     * 
     * @return boolean
     */
    public function deleteMedia($questionId, $mediaId = null)
    {
        $where = $this->getAdapter()->quoteInto('question_id = ?', $questionId);
        if($mediaId){
            $where .= $this->getAdapter()->quoteInto(' AND question_media_id = ?', $mediaId);
        }
        
        $select = $this->select();
        $select->where($where)
               ->from($this, array('path', 'media_type'));
        
        // Lista dos arquivos
        $result = $this->fetchAll($select)->toArray();
        
        // Apaga as entradas no BD
        if($this->delete($where) == count($result)){
            // Apaga os recursos do sistema de arquivo
            $files = DA_Lib_Files::deleteQuestionMedia($result);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Verifica se o desafio possui algum recurso de mídia registrado.
     * 
     * @param  string $questionId Id do desafio
     * 
     * @return boolean
     */
    public function hasMedia($questionId) {
        $select = $this->select();
        
        $select->from($this, 'COUNT(question_media_id) as media_count')
               ->where('question_id = ?', $questionId);
        
        $result = $this->fetchRow($select)->toArray();
        
        return ($result['media_count'] > 0);
        
    }
}