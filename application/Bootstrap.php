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
 * @package      DA
 * @file         Bootstrap.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 * 
 */

/** 
 * Inicializa os parâmetros de toda a aplicação 
 * 
 * @package      DA
 * @subpackage   Bootstrap
 *
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 *
 */

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    private $_cache;
    
    /**
     * Inicia todos os recursos a serem usados durante o bootstrap 
     */
    protected function _initResources ()
    {
        $this->bootstrap('frontController'); // Carrega o recurso frontController
        $this->bootstrap('view'); // Inicia o recurso view
        
//         $frontController = Zend_Controller_Front::getInstance();
//         $router = $frontController->getRouter();
        
//         $router->removeDefaultRoutes();
        
//         $www = new Zend_Controller_Router_Route_Hostname(
//                     'desafio_alpha', 
                        
//                     array('module' => 'pub', 'controller' => 'index', 'action' => 'index')
                         
//                 )  ;
        
//         $pub = new Zend_Controller_Router_Route(":@controller/:@action/*",
//             array('module' => 'pub', 'controller' => 'index', 'action' => 'index')
//         );
        
//         $teste = new Zend_Controller_Router_Route("equipe/:@controller/:@action/*", 
//             array('module' => 'equipe', 'controller' => 'index', 'action' => 'index')
//         );
        
//         $www->chain($teste);

//         $router->addRoute('defaultt', $pub);
//         $router->addRoute('teste', $teste);
        
        // Registra o namespace padrão para sessões
        Zend_Registry::set('Zend_Session', new Zend_Session_Namespace());
        
        // Carrega o arquivo de configurações extra usadas na aplicação
        Zend_Registry::set('constants', $this->getOption('constants'));
        
        $host = $this->getOption('host'); //
        
        define('STATIC_URL', "http://static.$host"); // Define o caminho raíz para entrega de conteudo estático
    }
    
    /**
     * Define os namespaces para carregamento automático de classes.
     *
     * @return Bootstrap
     */
    protected function _initAddnamespace ()
    {
        $this->getApplication()->setAutoloaderNamespaces(array('DA_'));
        return $this;
    }
    protected function _initCache ()
    {
        // Opções do frontend
        $frontendOptions = array(
            'lifetime' => 1, // 60*60*2, // o cache é válido por 2 horas
            'automatic_serialization' => true
        );
        
        // Opções do backend
        $backendOptions = array(
            'cache_dir' => APPLICATION_PATH . '/../temp/cache/'
        );
        
        // Instancia o cache
        $this->_cache = Zend_Cache::factory('core',
            'File',
            $frontendOptions,
            $backendOptions
        );
        
        // Registra o cache
        Zend_Registry::set('cache', $this->_cache);
    }
    
    /**
     * Executa as tarefas para a internacionalização (I18n) do sistema 
     */
    protected function _initI18n ()
    {
    
        // Obtém o locale padrão definido no arquivo de configuração
        $locale_default = $this->getOption('locale');
               
        try {
           
            // Instancia o Zend_Locale
            $locale = new Zend_Locale($locale_default);
            
            // Instancia um objeto de Zend_Translate e adiciona o dicionário em português brasileiro (pt_BR)
            $translate = new Zend_Translate(array(
                'adapter' => 'Array',
                'locale'    => 'pt_BR',
                'content' => realpath(APPLICATION_PATH . '/language/pt_BR/'),
            ));
            
            // Adiciona o dicionário em inglês americano (en_US)
            $translate->addTranslation(array(
                'locale'    => 'en_US',
                'content' => realpath(APPLICATION_PATH . '/language/en_US/'),
            ));

            // Define o 'locale' atual
            $translate->setLocale($locale);
            
            // Adiciona estas duas instâncias ao Zend_Registry para que possam ser acessadas por toda a aplicação
            Zend_Registry::set('Zend_Locale', $locale);
            Zend_Registry::set('Zend_Translate', $translate);
                        
        } catch (Exception $e){
        }

    }
    
    /**
     * Carrega os plugins na inicializacao.
     */
    protected function _initPlugins ()
    {
        /* Registra os plugins */
        $this->frontController->registerPlugin(new DA_Plugin_BuildUp()); // Registra o plugin BuildUp
        $this->frontController->registerPlugin(new DA_Plugin_Auth()); // Registra o plugin Auth
        
    }
    
    /**
     * Adiciona o prefixo para carregamento dos Helpers 
     */
    protected function _initHelpers ()
    {
        
        ///$this->_view->addHelperPath(APPLICATION_PATH . 'Pub/views/helpers/Header', 'Pub_View_Helper_Header');
        
        $resourceLoader = new DA_Helper_ResourceLoader();
        $this->view->registerHelper($resourceLoader, 'resourceLoader');
        
        Zend_Controller_Action_HelperBroker::addPrefix('DA_Helper');
//         Zend_Controller_Action_HelperBroker::addHelper(new DA_Helper_ResourceLoader);
        
    }
    
    /**
     * Inicializa a configurações de conteúdo para toda a aplicação.
     *
     * Define as configurações de codificação, meta tags, includes e aparência das páginas para navegadores e
     * search engines e muito mais.
     */
    protected function _initContents ()
    {
        
        if(!($navigation = $this->_cache->load('menu') )){
            $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/nav.xml', 'nav'); // Interpreta as configurações do arquivo Xml em um arra
            $navigation = new Zend_Navigation($config); // Cria uma instância do Zend_Navigation, com as configurações acima
            
            $this->_cache->save($navigation, 'menu');
        }
        $this->view->navigation($navigation); // Expõe a navegação para as views
        
        
        /* Ícones para as páginas */
        $this->view->headLink(array('rel' => 'favicon', 'href' => STATIC_URL . '/media/icons/favicon_default.ico', 'type' => 'image/x-icon' ))
             ->headLink(array('rel' => 'shortcut icon', 'href' => STATIC_URL . '/media/icons/favicon_default.ico', 'type' => 'image/x-icon' ))
//              ->headLink(array('rel' => 'icon', 'href' => STATIC_URL . '/media/icons/da_icon_32.png', 'type' => 'image/png', 'sizes' => '32x32'))
//              ->headLink(array('rel' => 'icon', 'href' => STATIC_URL . '/media/icons/da_icon_48.png', 'type' => 'image/png', 'sizes' => '48x48'));
        ;
        
        
        /* Define as tags do cabeçalho */
        $this->view->headTitle('Desafio Alpha'); // Define o título padrão para todas as páginas
        $this->view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8'); // Inclui a meta tag 'Content-type' em todas as páginas
        $this->view->headTitle()->setSeparator(' | '); // Separador das seções do título das páginas
                
    }
    
    
    
}