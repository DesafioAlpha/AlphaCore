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
 * @file         UploadController.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */

class Admin_UploadController extends Zend_Controller_Action
{
    public function indexAction()
    {
        
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        header('Pragma: no-cache');
        header('Cache-Control: private, no-cache');
        header('X-Content-Type-Options: nosniff');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT');
        header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');
        header('Vary: Accept');
        
        if (isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }
        if($this->_request->isPost()){

            $info = array();
            
            if(!(($context = @$_POST['context']) && ($questionId = @$_POST['question_id']) && $upload = @$_FILES['files'])){
                $info[] = false;
            }else{
    
                if ($upload && is_array($upload['tmp_name'])) {
                    foreach ($upload['tmp_name'] as $index => $tempName) {
                        if(!$upload['error'][$index]){
                            $info[] = DA_Lib_Files::saveQuestionMedia(
                                $questionId,
                                $tempName,
                                isset($_SERVER['HTTP_X_FILE_NAME']) ? $_SERVER['HTTP_X_FILE_NAME'] : $upload['name'][$index],
                                isset($_SERVER['HTTP_X_FILE_SIZE']) ? $_SERVER['HTTP_X_FILE_SIZE'] : $upload['size'][$index],
                                $context
                            );
                        }
                    }
                } elseif ($upload || isset($_SERVER['HTTP_X_FILE_NAME'])) {
                    
                    $info[] = DA_Lib_Files::saveQuestionMedia(
                        $questionId,
                        isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
                        isset($_SERVER['HTTP_X_FILE_NAME']) ? $_SERVER['HTTP_X_FILE_NAME'] : (isset($upload['name']) ? isset($upload['name']) : null),
                        isset($_SERVER['HTTP_X_FILE_SIZE']) ? $_SERVER['HTTP_X_FILE_SIZE'] : (isset($upload['size']) ? isset($upload['size']) : null),
                        $context
                    );
                }
            }
            echo json_encode($info);

        } 
    }
}