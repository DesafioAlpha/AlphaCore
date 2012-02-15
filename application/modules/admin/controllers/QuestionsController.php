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
 * @package      DA_Admin
 * @subpackage   Questions
 * @file         QuestionsController.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */

class Admin_QuestionsController extends Zend_Controller_Action
{    
    public function init ()
    {
        // Carrega os recursos utilizados em todas as ações do controlador
        $this->view->resourceLoader()
                   ->appendJqueryPlugin(
                     array(
                         'tinymce/tiny_mce',
                         'tinymce/jquery.tinymce',
                         'jquery.ui.widget',
                         'fileupload/jquery.iframe-transport',
                         'fileupload/jquery.fileupload',    
                         'fancybox/jquery.fancybox-1.3.4.pack',
                         'flowplayer/flowplayer-3.2.6.min'
                     ))
                   ->appendCss('3rd_party/fancybox/jquery.fancybox-1.3.4.css')
                   ->appendScript('modules/admin/questions.js');
        
        if($this->_request->isXmlHttpRequest()){
            $this->getHelper('layout')->disableLayout();
        }
    }
    
    public function indexAction()
    {
        
    }
    
    public function databaseAction()
    {
        $question = new DA_Model_Dbtable_Question();
        $this->view->questionCount = $question->countQuestions();
    }
    
    public function newAction()
    {
        if($this->_request->isPost() && $post = $this->_request->getPost()){
            
            if($post['new']){
                $question = new DA_Model_Dbtable_Question();
               
                $this->_helper->viewRenderer('view');
                
                $this->view->questionData = $question->newQuestion();
            }
        }
    }
    
    public function viewAction()
    {

        if($questionId = $this->_request->getParam('id')){
                
            $questionMedia = new DA_Model_Dbtable_QuestionMedia();
            
            if($this->_request->getParam('media') == 1){
                $this->view->questionMedia = $questionMedia->getMedia($questionId);
                
            }else{
                
                $question = new DA_Model_Dbtable_Question();
                $question->loadQuestionData($questionId);
                $this->view->questionData = $question->getData();
                $this->view->hasMedia = $questionMedia->hasMedia($questionId);
            }
            
        }elseif(($params = $this->_request->getParams()) && key_exists('search', $params)){
            $question = new DA_Model_Dbtable_Question();
            
            $allQuestions = $question->listAll($params);
            header('Content-Type: application/json');
            echo json_encode($allQuestions);
            
            $this->_helper->viewRenderer->setNoRender();
            
        }
    }
    
    public function saveAction ()
    {
       if($this->_request->isPost() && $post = $this->_request->getPost()){
           $question = new DA_Model_Dbtable_Question();
           
           $result = $question->saveQuestion($post);

           if($this->_request->isXmlHttpRequest()){
               
               header('Content-Type: application/json');
               $this->_helper->viewRenderer->setNoRender();
               
               echo json_encode($result);
               
               return true;
                               
           }elseif($id = $post['id']){
               return $this->_redirect('/questions/view/id/'. $id);
           }
              
       }
    }
    
    public function deleteAction () 
    {
        if($this->_request->isPost() && ($post = $this->_request->getPost()) && $questionId = $post['question_id']){
                        
            $this->_helper->viewRenderer->setNoRender();
            
            header('Content-Type: application/json');
            
            if(array_key_exists('media_id', $post)){
                $questionMedia = new DA_Model_Dbtable_QuestionMedia();
                $result = $questionMedia->deleteMedia($questionId, $post['media_id']);
            }else{
                $question = new DA_Model_Dbtable_Question();
                $result = $question->deleteQuestion($questionId);
            }
            
            echo json_encode($result);
        }
    } 
}