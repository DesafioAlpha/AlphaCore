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
        // Verifica se o view helper está ativo e obtém sua instância
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        
        // Obtem a entrada ativa do menu
        if($thisPage = $view->navigation()->findOneBy('active', true)){
        
            $title = $thisPage->get('page_title');
            $title = ((!$title)?$thisPage->get('label'):$title);
            
            // Define o título da página para as tags <title> e <h1>
            $view->title = (($thisPage->get('title_show') == 'false')?'':$title);
            $view->headTitle()->prepend($title);
            
        }
    }
}
