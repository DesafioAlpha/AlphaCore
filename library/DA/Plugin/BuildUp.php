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
 * @package      DA_Plugins
 * @file         BuildUp.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */

/**
 * Tarefas-padrão para montagem das páginas 
 *
 * @package      DA_Plugins
 * @subpackage   BuildUp
 *
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 *
 */
class DA_Plugin_BuildUp extends Zend_Controller_Plugin_Abstract
{
    /** 
     * @see Zend_Controller_Plugin_Abstract::routeShutdown()
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        
        /* Define o layout específico para o módulo, se houver */
        $module = $request->getModuleName();
        $layout = Zend_Layout::getMvcInstance();

        /* Verifica se o arquivo de layout existe, caso contrário será carregado o padrão */
        if(file_exists($layout->getLayoutPath() . "/$module.phtml")) {
            $layout->setLayout($module);
        }
        
        // Verifica se o view helper está ativo e obtém sua instância
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        $constants = Zend_Registry::get("constants");
        
        $cache = Zend_Registry::get('cache');
        /* Define a navegação para o módulo, se houver */
        if(!($navigation = $cache->load("{$module}_nav") )){
        
            if(file_exists(APPLICATION_PATH . "/configs/{$module}_nav.xml")){
                $navFileName = "{$module}_nav";
            }else{
                $navFileName = "nav";
            }
        
            $config = new Zend_Config_Xml(APPLICATION_PATH . "/configs/$navFileName.xml", 'nav'); // Interpreta as configurações do arquivo Xml em um arra
            $navigation = new Zend_Navigation($config); // Cria uma instância do Zend_Navigation, com as configurações acima
            $cache->save($navigation, "{$module}_nav");
        
        }
        $view->navigation($navigation); // Expõe a navegação para as views
        
        /* Passa o título para a página definido no arquivo de configuração de navegação */
        if($thisPage = $view->navigation()->findOneBy('active', true)){ // Obtem a entrada ativa do menu
        
            $view->title = new stdClass();
            
            $view->title->title = ((isset($thisPage->page_title))?$thisPage->page_title:$thisPage->getLabel());
        
            // Define o título da página para as tags <title> e <h1>
            $view->title->show = (($thisPage->get('title_show') == 'false')?false:true);
            
            $view->headTitle()->setSeparator(' | ') // Separador das seções do título das páginas
                 ->headTitle($constants['projectName']); // Define o título padrão para todas as páginas
        
        }
        
        /* Ícones para as páginas */
        $view->headLink(array('rel' => 'favicon', 'href' => STATIC_URL . '/media/icons/favicon_default.ico', 'type' => 'image/x-icon' ))
             ->headLink(array('rel' => 'shortcut icon', 'href' => STATIC_URL . '/media/icons/favicon_default.ico', 'type' => 'image/x-icon' ));
        
       
        $view->projectName = $constants['projectName'];
        $view->dateFormat = $constants['dateFormat'];
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8'); // Inclui a meta tag 'Content-type' em todas as páginas
        
        
        
    }
}
