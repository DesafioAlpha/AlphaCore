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
 * @subpackage   Agenda
 * @file         AgendaController.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 * 
 */

/**
 * Controlador da seção 'Agenda'
 *
 * @package      DA_Pub
 * @subpackage   AgendaController
 *
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 *
 */
class Pub_AgendaController extends Zend_Controller_Action
{

    /* Inicializa as definições para a seção 'Agenda'.
     * 
     * Define as variáveis, constantes e demais providências iniciais.

     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
       
    }

    /**
     * Action index para a seção 'Agenda'
     *  
     */
    public function indexAction()
    {                
        
        
        $cache = Zend_Registry::get('cache');
        
        // Instancia e registra o helper para a renderização de calendários html  
        // Adicionamos ao cache pois é uma operação custosa      
        if(!($this->view->calendar = $cache->load('calendar'))){
            
            $calendar = new DA_Helper_Calendar();
            $events = new DA_Model_Dbtable_Event();
            
            // Obtém os eventos do BD
            $events = $events->getEvents();
            
            foreach ($events as $event){
                // Adiciona ao calendário todos os eventos obtidos
                $calendar->addEvent($event['start_date'], $event['end_date'], $event['title'], $event['description']);
            }
            
            // Obtém o HTML do View Helper
            $this->view->calendar = $calendar->calendar();
            
            // Salva no cache
            $cache->save($this->view->calendar,'calendar');
        }
    }
}

