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
 * @subpackage   Inscricoes
 * @file         InscricoesController.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */
/**
 * Controlador da seção 'Inscrições'
 *
 * @package      DA_Pub
 * @subpackage   Inscricoes
 *            
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 *         
 */
class Pub_InscricoesController extends Zend_Controller_Action
{
    /**
     * @var string|DA_Form_Cadastro_Principal
     */
    private $_form;
    
    /**
     * Carrega o formulário para a página, com ou sem procura no cache
     * 
     * @param boolean $getFromCache (optional) Define se a chamada buscará ou não o formulário cacheado
     */
    private function _prepareForm ($getFromCache = false)
    {
        if($getFromCache){ // Procura no cache
            $cache = Zend_Registry::get('cache'); // Obtém a referência para o cache, do registro
            if(!($this->_form = $cache->load('inscricoes_form'))){ // Se encontrado, não faz mais nada
                
                $this->_prepareForm(); // Chamada recursiva para inicializar o formulário
                $this->_form = $this->_form->__toString(); // Obtém a marcação html do form
                $cache->save($this->_form, 'inscricoes_form'); // Salva no cache
            
            }
        }else{ 
            $this->_form = new DA_Form_Cadastro_Principal(); // Inicializa um novo formulário 
            $this->_form->setAction($this->view->url()); // Define a action do formulário
        }
        
    }
    /**
     * Processa os elementos da página
     */
    public function indexAction ()
    {
        $request = $this->getRequest();
        $noCache = false;
        
        if ($request->isPost()) {
            
            $noCache = true;
            $this->_prepareForm();
                
            $data = $request->getPost();
                        
            $i = 2;
            
            while( key_exists('Int_'. $i , $data) ){
                
                $this->_form->getSubForm('Int_'. $i)->preValidation($data['Int_'. $i]);
                
                $i++;
            }
            
            if($this->_form->isValid($data)){
                // TODO
            }
           
        }

        if(!$noCache){
            $this->_prepareForm(true);
        }
        $this->view->form = $this->_form;
        
        
        $this->view->resourceLoader()
                    ->appendJqueryPlugin(array(
                          'jquery.tipsy',
                          'jquery.validationEngine',
                          'jquery.validationEngine-pt_BR',
                          'jquery.maskedinput',
                    ))
                    ->appendScript('modules/pub/inscricoes.js')
                    ->appendCss(array(
                            '3rd_party/tipsy.css',
                            '3rd_party/validationEngine.jquery.css'
                    ));
    }
}
