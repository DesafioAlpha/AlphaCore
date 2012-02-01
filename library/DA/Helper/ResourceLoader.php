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
 * @package      DA_Helpers
 * @file         ResourceLoader.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */

/**
 * Permite o carregamento de recursos dinamicamente.
 * 
 * Encapsula métodos para carregamento de scripts, folhas de estilo, entre outros durante 
 * toda a pilha da aplicação.
 *
 * @package      DA_Helpers
 * @subpackage   ResourceLoader
 *
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 *
 */
class DA_Helper_ResourceLoader extends Zend_View_Helper_Abstract
{
    private $_cssPrepared;
    private $_jsPrepared;
    private $_moduleName;
    
    public function resourceLoader()
    {
        $this->init();
        return $this;
    }
    
    /**
     * @see Zend_Controller_Action_Helper_Abstract::preDispatch()
     */
    public function init()
    {
        $this->_moduleName =  Zend_Controller_Front::getInstance()->getRequest()->getModuleName();        
        $this->_controllerName =  Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        $this->_prepareCss();
        $this->_prepareJs();
        
    }    

    /**
     * 
     */
    private function _prepareCss()
    {    
        if(!$this->_cssPrepared){
            
            $this->appendCss('base.css', 'all');
            $this->appendCss("modules/{$this->_moduleName}/{$this->_controllerName}.css", 'all');
            $this->appendCss('print.css', 'print');
            //$this->appendCss('3rd_party/jquery-ui.css');
            
            $this->_cssPrepared = true;
        }
    } 
    
    /**
     * 
     */
    private function _prepareJs()
    {   
         if(!$this->_jsPrepared){             
             $this->appendScript("3rd_party/jquery/jquery.min.js");             
             $this->appendScript('base.js');
             //$this->appendJqueryPlugin('jquery-ui.min');
             
             $this->_jsPrepared = true;
         }   
    }
    
	/**
     * Adiciona um script ao headScript a partir do caminho correspondente.
     * 
     * @param string $script_path Caminho do script a ser adicionado
     * @return DA_Helper_ResourceLoader
     */
    public function appendScript($script_path){
        
        $this->view->headScript()->appendFile(STATIC_URL . "/js/" . $script_path);
        
        return $this; // Retorna o objeto para métodos sequenciais
        
    }
    
    /**
     * @param array|string $css_path
     * @return DA_Helper_ResourceLoader
     */
    public function appendCss($css_path, $media = 'screen'){
        
        if(is_array($css_path)){
            
            foreach ($css_path as $path){
                $this->appendCss($path);
            }
            
        }else{
            
            $this->view->headLink()->appendStylesheet(STATIC_URL . "/css/" . $css_path, $media);
            
        }
        
        return $this;
        
    }
    
    /**
     * Adiciona o(s) plugin(s) jQuery passados ao headScript do view.
     * 
     * @param string|array $plugin Nome ou lista de nomes dos plugins jQuery a serem adicionados ao view.
     * @return DA_Helper_ResourceLoader
     */
    public function appendJqueryPlugin($plugin){
        
        if(is_array($plugin)){
            
            foreach ($plugin as $plugin_name){
                $this->appendScript("3rd_party/jquery/plugins/$plugin_name.js");
            }
            
        }elseif(is_string($plugin)){
            
            $this->appendScript("3rd_party/jquery/plugins/$plugin.js");
                        
        }
        
        return $this;
    }
}