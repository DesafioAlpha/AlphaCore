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
 * @file         Calendar.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 * 
 */

/**
 * Provê métodos para a renderização de calendários, baseado num intervalo de datas. 
 *
 * @package      DA_Helpers
 * @subpackage   Calendar
 *
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 * 
 * @todo         Estender para incluir outras opções
 * @todo         Otimizar lógica
 */
class DA_Helper_Calendar extends Zend_View_Helper_Abstract
{
    /**
     * Data em cujo mês o calendário começa.
     * 
     * @var DateTime
     */
    private $_startDate;
    
    /**
     * Data em cujo mês o calendário termina.
     * 
     * @var DateTime
     */
    private $_endDate;
    
    /**
     * Armazena a saída para os calendários.
     * 
     * @var string
     */
    private $_html = '';
    
    /**
     * Armazena a saída para as descrições de eventos.
     *
     * @var string
     */
    private $_html_descrs = '';
    
    /** Lista de eventos.
     * 
     * @var array
     */
    private $_events = array();
    
    
    /**
     * Contador de eventoss que permitirá indexação das classes de estilo
     * 
     * @var int
     */
    private $_countEvents = 0;
    
    /**
     * Abreviações dos nomes de dias da semana
     * 
     * @var array
     */
    private $_weekDayNames;
    
    /**
     * @var Zend_Translate
     */
    private $_translator;
        
    public function __construct(){
        
        $this->_weekDayNames = Zend_Registry::get('DA_Config')->weekDayNames;
        // Instância do translator
        $this->_translator = Zend_Registry::get('Zend_Translate');
        
    }
    
    /**
     * Constrói calendários para o intervalo de eventos. 
     */
    public function buildHtml()
    {
        if($this->getStartDate() instanceof DateTime && $this->getEndDate() instanceof DateTime){
            $this->_html = $this->_html_descrs = ''; // Limpa o buffer
            
            $date = clone $this->getStartDate(); // Clona a primeira data
                
            $oneMonth = new DateInterval('P1M'); // Intervalo de um mês
            $date->setDate($date->format('Y'), $date->format('m'), 1); // Vai para o 1º dia do mês
        
            while ($date <= $this->getEndDate()){ // Gera calendários até o mês do último evento 
        
                $this->_buildCalendar($date); // Gera o calendário para o mês 
                $date->add($oneMonth); // Adiciona um mês 
        
            }
        }
    }
    
    /**
     * Adiciona ao buffer do objeto a marcação html para o calendário da data passada com os respectivos 
     * eventos marcados.
     * 
     * @param DateTime $date Data para cujo mês deve ser montado o calendário
     * @param boolean $header Define se o método deve criar o cabeçalho com o nome do mês e ano no calendário
     */
    private function _buildCalendar(DateTime $date, $header = true)
    {
        $dayMax = $date->format('t'); // Último dia do mês
        $firstWeekday = $date->format('w'); // Dia da semana (dia 1)
    
        $year = $date->format("Y"); // Ano
        $month = $date->format("m"); // Mês
        $day = 1; // Dia 1º
    
        while(count($this->_events) > 0 && $this->_events[0][0] < $date && $this->_events[0][1] < $date){
            // Elimina todos os eventos que já passaram
            array_shift($this->_events);
        }
    
        // Lista de eventos para o mês
        $events = array();
        
        // Itera sobre os eventos registrados.
        foreach ($this->_events as $event){ 
             
            $startDay   = 0; // Dia inicial do evento
            $endDay     = 0; // Dia final do evento
    
            /* Evento */
            $eventYear  = array($event[0]->format('Y'), $event[1]->format('Y')); // Ano de início e término do evento
            $eventMonth = array($event[0]->format('m'), $event[1]->format('m')); // Mês do início e término
            $eventDay   = array($event[0]->format('d'), $event[1]->format('d')); // Dia do início e término
            $title      = $event[2]; // Título do evento
            $descr      = $event[3]; // Descrição do evento
    
            // Verifica se a data inicial do evento está nesse mês
            if($eventMonth[0] == $month && $eventYear[0] == $year){
    
                $startDay = $eventDay[0];
                $this->_countEvents++; // Incrementa o contator de eventos
                $this->_html_descrs .= '<div class="event_desc event_'.$this->_countEvents.'"><div></div>'.htmlspecialchars($descr).'</div>'; // Descrição do evento
    
            }
    
            // Verifica se a data final do evento está nesse mês
            if($eventMonth[1] == $month && $eventYear[1] == $year){
    
                $startDay = (!$startDay)?1:$startDay; // Caso o início do evento não está neste mês, cobre o calendário desde o começo
                $endDay   = $eventDay[1];
    
            }elseif($startDay){
                $endDay   = $dayMax; // Caso o início está neste mês, mas o fim não, cobre o calendário até o fim
            }
    
            // Caso o primeiro evento não está nesse mês, para a iteração. Isto requer que os eventos estejam ordenados
            if(!$startDay && !$endDay){
                break;
            }
    
            // Adiciona o evento à lista
            $events[] = array($startDay, $endDay, $title, $descr, $this->_countEvents);
    
        }
        
        // Monta a marcação para o calendário
        $this->_html .= '<table class="calendar">';
        // Cabeçalho para o mês
        if($header){
            $this->_html .= '<tr><td colspan="7" class="header">'  . $this->_translator->translate($date->format("F")) . '/' .$date->format('Y') .'</td></tr>';
        }
        
        $j = 0;

        while($day <= $dayMax) { // Monta o calendário até o último dia do mês
    
            $this->_html .= '<tr>';
    
            for($weekIndex = 0; $weekIndex < 7; $weekIndex++){ // Itera sobre uma semana
                if(!$j){
                    
                    $this->_html .= '<td class="weekDay">' . $this->_translator->translate($this->_weekDayNames[$weekIndex]) . '</td>';
                    
                }else{               
                    // Conteúdo a ser mostrado na célula, vazio para antes e depois do mês.
                    $dayStamp = (($day <= $dayMax && ($day != 1 || $firstWeekday <= $weekIndex))?$day:'');
        
                    // A data pertence a um evento?
                    $eventSpan = false;
                    $title = '';
        
                    // Caso esta data não esteja mais no intervalo, elimina-a da lista
                    if(count($events) > 0 && $events[0][1] < $day){
        
                        array_shift($events);
        
                    }
                    if(count($events) > 0 && $events[0][0] <= $day && $dayStamp){
        
                        // A data pertence a um evento.
                        $eventSpan = true;
        
                        // Titulo do evento
                        $title = $events[0][2];
                        // Índice do evento
                        $index = $events[0][4];
        
                    }
        
                    // Célula com o número do dia e formatada como um evento (se aplicável)
                    $this->_html .= '<td'. (($eventSpan)?' title=" '. htmlspecialchars($title) .' " class="event event_'. $index .'"':'') . '>'. $dayStamp .'</td>';
        
                    // Verifica se o mês já começou
                    if(($day != 1) || $firstWeekday <= $weekIndex){
                        $day++; // Incrementa o número do dia
                    }
                }
            }
            
            $this->_html .= '</tr>';
            $j++;
        }
    
        $this->_html .= '</table>';
        
        
    
    }
    
    /**
     * Adiciona um evento ao calendário e atualiza os limites.
     *
     * Obs: Este método parte do pressuposto de que os eventos são 
     * adicionados em ordem crescente.
     * 
     * @param string|DateTime $startDate Data inicial do evento.
     * @param string|DateTime $endDate Data final do evento.
     * @param string $title Título do evento.
     */
    public function addEvent($startDate, $endDate, $title = '', $descr = '')
    {
        if(!($startDate instanceof DateTime)){
            $startDate = new DateTime($startDate);
        }
        if(!($endDate instanceof DateTime)){
            $endDate = new DateTime($endDate);
        }
    
        /** TODO: Melhorar isso */
        $mask = array(
            '%start_Y%'   => $startDate->format('Y'),
            '%start_F%'   => $this->_translator->translate($startDate->format('F')),
            '%start_m%'   => $startDate->format('m'),
            '%start_d%'   => $startDate->format('d'),
            '%start_H%'   => $startDate->format('H'),
            '%start_i%'   => $startDate->format('i'),
            '%start_s%'   => $startDate->format('s'),
                
            '%end_Y%'     => $endDate->format('Y'),
            '%end_F%'     => $this->_translator->translate($endDate->format('F')),
            '%end_m%'     => $endDate->format('m'),
            '%end_d%'     => $endDate->format('d'),
            '%end_H%'     => $endDate->format('H'),
            '%end_i%'     => $endDate->format('i'),
            '%end_s%'     => $endDate->format('s'),
        );
        
        $descr = strtr($descr, $mask);
        
        // Atualiza os limites do calendário
        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
    
        // Adiciona um novo evento à lista.
        $this->_events[] = array($startDate, $endDate, $title, $descr);
    }
    
    /**
     * @return string
     */
    public function calendar()
    {
        $this->buildHtml();
        return $this->_html_descrs . $this->_html;
    }
    
    /**
     * @return DateTime $_startDate
     */
    public function getStartDate ()
    {
        return $this->_startDate;
    }

	/**
     * @return DateTime $_endDate
     */
    public function getEndDate ()
    {
        return $this->_endDate;
    }

	/**
	 * Atualiza o limite inferior do calendário.
	 * 
     * @param DateTime $_startDate
     */
    public function setStartDate (DateTime $_startDate)
    {
        if(!$this->getStartDate() || $_startDate < $this->getStartDate()){
            $this->_startDate = $_startDate;
        }
    }

    /**
     * Atualiza o limite superior do calendário.
     * 
     * @param DateTime $_endDate
     */
    public function setEndDate (DateTime $_endDate)
    {
        if(!$this->getEndDate() || $_endDate > $this->getEndDate()){
            $this->_endDate = $_endDate;
        }
    }
}
