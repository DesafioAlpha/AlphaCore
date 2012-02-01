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
 * @package      DA_Form
 * @file         Phone.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */

/**
 * Classe para validação de números de telefone.
 * 
 * Atualmente verifica apenas o formato brasileiro (XX) XXXX-XXXX.
 *
 * @package      DA_Form
 * @subpackage   Validator_Phone
 *
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 *
 * @todo         Estendê-la para aceitar formatos personalizados e L10n
 */
class DA_Form_Validator_Phone extends Zend_Validate_Date
{
    /**
     * @var string Placeholder para a mensagem de erro.
     */
    const INVALID = 'invalidPhone';
    
    /**
     * @var array Padrões de formatos para cada locale
     * 
     * @todo Incluir mais locales
     */
    protected $_phoneRegex = array(
        'pt_BR'    =>  '/^\([1-9][1-9]\) [0-9]{4}\-[0-9]{4}$/',
        
    );
    
    /**
     * @var array Modelos de mensagens a serem exibidas
     */
    protected $_messageTemplates = array(
        self::INVALID => "O valor '%value%' não está no formato (XX) XXXX-XXXX ou é inválido",
    );
    
    /**
     * Informa o locale a ser usado para a validação.
     * 
     * @return string
     */
    public function getLocale()
    {
        return "pt_BR";
    }
    
    /**
     * Verifica se o valor passado casa com o formato.
     * 
     * @param string $value Valor a ser verificado.
     * @return boolean
     *  
     * @see Zend_Validate_Interface::isValid()
     */
    public function isValid($value)
    {
        $this->_value = $value;
        
        $locale = $this->getLocale();
        
        if(array_key_exists($locale, $this->_phoneRegex)){
            
            if(!preg_match($this->_phoneRegex[$locale], $value)){
            
                $this->_error(self::INVALID);
                return false;
                 
            }
        }
        
        return true;
    }
    
}