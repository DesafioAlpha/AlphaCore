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
        
        // Registra o namespace padrão para sessões
        Zend_Registry::set('Zend_Session', new Zend_Session_Namespace());
        
        // Registra as configurações da aplicação em um objeto para chamada fluente
        $daConfig = (object) $this->getOption('da_config');
        Zend_Registry::set('DA_Config', $daConfig);
        
        $host = $daConfig->host;
        
        define('STATIC_URL', "http://static.$host/"); // Define o caminho raíz para entrega de conteudo estático
        define('STATIC_PATH', APPLICATION_PATH . '/data/static/'); // Diretório para armazenamento de conteúdo estático
        define('APP_URL',    "http://app.$host"); // Define o caminho raíz para entrega de scripts, webservices...
        
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
    
    /**
     * Configura o cache da aplicação 
     */
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
     * Executa as tarefas para a internacionalização (I18n) e localização (L10n) do sistema 
     */
    protected function _initI18n ()
    {
    
        // Obtém o locale padrão definido no arquivo de configuração
        $locale_default = Zend_Registry::get('DA_Config')->locale;

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
        
        // Adiciona estas duas instâncias ao registro para que possam ser acessadas por toda a aplicação
        Zend_Registry::set('Zend_Locale', $locale);
        Zend_Registry::set('Zend_Translate', $translate);
        
    }
    
    /**
     * Carrega os plugins na inicializacao.
     */
    protected function _initPlugins ()
    {
        /* Registra os plugins */
        $this->frontController->registerPlugin(new DA_Plugin_BuildUp()); // Registra o plugin BuildUp
        $this->frontController->registerPlugin(new DA_Plugin_Auth());    // Registra o plugin Auth
        
    }
    
    /**
     * Adiciona o prefixo para carregamento dos Helpers 
     */
    protected function _initHelpers ()
    {
        $this->view->setHelperPath('DA/Helper/', "DA_Helper_");
        
    }
}