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
 * @file         application.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */
return array_replace_recursive(array(
     
    /* Configurações iniciais do PHP */
    'phpSettings' => array(
        'session' => array(
            'save_path'        => APPLICATION_PATH. "/../temp/session",
            'cookie_httponly'  => true,
            'use_only_cookies' => true,
            'name'             => 'SID' // Session ID
        )
    ),
    
    /* Caminhos-padrão */
    'includePaths' => array(
        'library' => APPLICATION_PATH . "/../library"
    ), 
    
    /* Informações da aplicação */
    'appnamespace' => "Application", 
    'bootstrap' => array(
        'class' => "Bootstrap", 
        'path' => APPLICATION_PATH . "/Bootstrap.php"
    ), 
    
    /* Configurações dos recursos*/
    'resources' => array(
        
        /* Configurações padrão dos controladores */ 
        'frontController' => array(
        
            'prefixDefaultModule' => true, 
            'defaultModule'       => 'pub', 
            'moduleDirectory'     => APPLICATION_PATH . "/modules",
            'params'              => array(
                'disableOutputBuffering'    => false,
                'displayExceptions'         => 0

            )
        ), 
        
        /* Tipo dos documentos */
        'view' => array(
            'doctype' => "HTML5", 
            'encoding' => "UTF-8"
        ), 
        
        /* Configurações do layout */
        'layout' => array(
            'layoutPath' => APPLICATION_PATH . "/layouts/scripts/"
        ), 
        
        /* Rotas */
        'router' => array(
            'routes' => array(
                'subdomains' => array(
                    'type'     => "Zend_Controller_Router_Route_Hostname",
                    'defaults' => array(
                        'module' => 'admin',
                    ), 
                    'chains' => array(
                        'index' => array(
                            'type'     => "Zend_Controller_Router_Route", 
                            'route'    => ":@controller/:@action/*", // suporte a tradução da rota
                            'defaults' => array(
                                'module'     => 'admin',
                                'controller' => 'index', 
                                'action'     => 'index'
                            )
                        )
                    )
                ) ,
                 
            )
        )
    )
), include dirname(__FILE__) . '/' . APPLICATION_ENV . '_config.php',  // Mescla com as configurações específicas do ambiente
   include dirname(__FILE__) . '/' . 'config.php');                    // Mescla com as configurações personalizadas
