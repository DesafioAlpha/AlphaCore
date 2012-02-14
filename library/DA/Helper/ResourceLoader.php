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
 * Encapsula métodos para carregamento de scripts, folhas de estilo, entre outros, durante 
 * a pilha da aplicação.
 * Para evitar prejuízos ao desempenho, estes métodos não verificam se o recurso a ser carregado
 * realmente existe, portanto seja cuidadoso ao adicioná-los.
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
    private $_cssCount = 10000;
    private $_jsPrepared;
    private $_moduleName;
    
    private $_inlineScripts = array();
    private $_inlineScriptsFiles = array();
    
    public function resourceLoader()
    {
        $this->init();
        return $this;
    }
    
    public function init()
    {
        $this->_moduleName =  Zend_Controller_Front::getInstance()->getRequest()->getModuleName();        
        $this->_controllerName =  Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        
        $this->_prepareCss()
             ->_prepareJs(Zend_Registry::get('DA_Config')->loadFromCdn);
        
    }    
    
    /**
     * Carrega as dependências globais de folhas de estilos
     * 
     * @return DA_Helper_ResourceLoader
     */
    private function _prepareCss()
    {    
        if(!$this->_cssPrepared){
            $this->appendCss('base.css', 2, 'all');
            $this->appendCss("modules/{$this->_moduleName}/{$this->_controllerName}.css", 4, 'all');
            $this->appendCss('print.css', null, 'print');
            
            $this->_cssPrepared = true;
        }
        
        return $this;
    } 
    
    /**
     * Carrega as dependências globais de scripts
     * 
     * @return DA_Helper_ResourceLoader
     */
    private function _prepareJs($fromCDN = false)
    {   
         if(!$this->_jsPrepared){             
             if($fromCDN){
                 // Carrega a última versão da biblioteca jQuery do Google CDN 
                 $this->view->headScript()->appendFile('http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js');
             }else{
                 $this->appendScript("3rd_party/jquery/jquery.min.js");
             }             
             $this->appendScript('base.js');
             
             // Exporta configurações para o cliente
             $this->appendInlineScript("da.static_url = '" . STATIC_URL . "'");
             $this->appendInlineScript("da.maindomain = '" . Zend_Registry::get('DA_Config')->host . "'");
             
             $this->_jsPrepared = true;
         }   
         
         return $this;
    }
    
    /**
     * Adiciona os scripts e arquivos de script ao helper inlineScript e o retorna.
     */
    public function inlineScript(){
            
        // Cada elemento de script deve incluir
        $this->_inlineScripts && $this->view->inlineScript()->appendScript(join(PHP_EOL, $this->_inlineScripts));
    
        foreach ($this->_inlineScriptsFiles as $inlineScript){
            $this->view->inlineScript()->appendFile($inlineScript);
        }
    
        return $this->view->inlineScript();
    }
    
    /**
     * Anexa um script inline ao armazenamento.
     * 
     * @param string $script
     */
    public function appendInlineScript($script)
    {
        // Adiciona o terminador de linha Js caso não exista.
        (substr($script, -1) != ';') && $script .= ';';
        
        $this->_inlineScripts[] = $script;
            
    }
    
	/**
     * Adiciona um script ao headScript a partir do caminho correspondente.
     * 
     * @param string scriptPathh Caminho do script a ser adicionado
     * @return DA_Helper_ResourceLoader
     */
    public function appendScript($scriptPath, $inline = false){
        
        $path = APP_URL . "/js/" . $scriptPath;
        
        if($inline){
            $this->_inlineScriptsFiles[] = $path;
        }else{
            $this->view->headScript()->appendFile($path);
        }
        
        return $this;
        
    }
    
    /**
     * Anexa uma folha de estilos
     * 
     * @param array|string $css_path
     * @return DA_Helper_ResourceLoader
     */
    public function appendCss($css_path, $index = null, $media = 'screen'){
        
        if(!$index){
            $index = $this->_cssCount;
        }
        
        if(is_array($css_path)){
            
            foreach ($css_path as $path){
                $this->appendCss($path); // Chamada recursiva para cada arquivo encontrado
            }
            
        }else{
            
            $this->view->headLink()->offsetSetStylesheet($index, STATIC_URL . "css/" . $css_path, $media);
            
        }
        
        $this->_cssCount++;
        return $this;
        
    }
    
    /**
     * Adiciona o(s) plugin(s) jQuery passados ao headScript do view.
     * 
     * @param string|array $plugin Nome ou lista de nomes dos plugins jQuery a serem adicionados ao view.
     * @return DA_Helper_ResourceLoader
     */
    public function appendJqueryPlugin($plugin, $inline = false){
        
        if(is_array($plugin)){
            
            foreach ($plugin as $plugin_name){
                $this->appendScript("3rd_party/jquery/plugins/$plugin_name.js", $inline);
            }
            
        }elseif(is_string($plugin)){
            
            $this->appendScript("3rd_party/jquery/plugins/$plugin.js", $inline);
                        
        }
        
        return $this;
    }
}