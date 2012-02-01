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
 * @package      DA_Forms
 * @file         Integrante.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */

/**
 * 
 *
 * @package      DA_Forms
 * @subpackage   Cadastro_Integrante
 *
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 *
 * @todo         Tornar este formulário configurável
 */
class DA_Form_Cadastro_Integrante extends Zend_Form_SubForm
{   
    
    /**
     * Permite a adição de validadores aos campos que necessitam de 
     * um nome definido.
     * 
     * Adaptado de http://www.jeremykendall.net/2008/12/24/conditional-form-validation-with-zend_form/
     * 
     * @param array $data Dados enviados na requisição
     * @return array
     */
    public function preValidation($data = null)
    {
        if(!empty($data['name'])){ // Caso o nome do integrante foi preenchido, faça as outras validações
            
            $this->defineValidators();
            
            return true;
        }
        return false;
    }
    /**
     * Atribui os validadores necessários aos campos 
     */
    public function defineValidators ()
    {
        $this->getElement('name')
             ->setRequired(true);
        
        $this->getElement("gender")
             ->setRequired(true);
        
        $this->getElement("email")
             ->addValidator('EmailAddress')
             ->setRequired(true);
        
        $this->getElement("phone")
             ->addValidator('Phone')
        ;
        $this->getElement("birthday")
             ->addValidator('date', false, array('dd/MM/yyyy'))
             ->setRequired(true);
        
//         $this->getElement("postcode")
//              ->addValidator(new Zend_Validate_PostCode('pt_BR'));
        
    }
    
    
    /**
     * @see Zend_Form::init()
     */
    public function init () 
    {
        
       $cache = Zend_Registry::get('cache');
        
       if(!($stateList = $cache->load('states'))){
        
            $state = DA_Model_Dbtable_State::getInstance();
            $stateList[] = 'Selecione um estado';
            $stateList = array_merge($stateList, $state->getPairs(1));
            
            $cache->save($stateList, 'states');
        
       }
       
       $this->addElementPrefixPath('DA_Form_Validator', 'DA/Form/Validator', 'validate');
        
     /* Dados Pessoais do integrante */

        // Nome do integrante
        $this->addElement('text', 'name', array(
            'label'      => 'form_name',
            'title'      => 'Digite seu nome completo',
            'size'       => 40,
            'filters'    => array(
                'StringTrim'
            ),
            'validators' => array(
            ),
        ));
        
        
        // Gênero
        $this->addElement('radio', 'gender', array(
            'label'      => 'form_gender',
            'separator'  => '',
            'multiOptions' => array(0 => 'form_gender_male', 1 => "form_gender_female"),
            'filters'    => array(
            
            ),
            'validators' => array(
                
            ),
        ));
        
        // Data de nascimento
        $this->addElement('text', 'birthday', array(
            'label'      => 'form_birthday',
            'title'      => 'Digite sua data de nascimento - No formato DD/MM/AAAA',
            'class'      => 'date',
            'filters'    => array(
                'StringTrim'
            ),
        ));
        
     
     /* Dados de contato */
        
        // Email
        $this->addElement('text', 'email', array(
            'label'      => 'form_email',
            'title'      => 'Informe seu endereço de email',
            'size'       => 40,
           // 'class'      => 'validate[required,custom[email]]',
            'filters'    => array(
                'StringTrim'
            )
        ));
        // Telefone
        $this->addElement('text', 'phone', array(
            'label'      => 'form_phone',
            'title'      => 'Informe um telefone de contato <br> No formato (XX) XXXX-XXXX',
            'class'      => 'phone',
            'filters'    => array(
                'StringTrim'
            ),
        ));
        
        // CEP
        /*
        $this->addElement('text', 'postcode', array(
            'label'      => 'form_postcode',
            'class'      => 'postcode',
            'filters'    => array(
                'StringTrim'
            ),
        ));
        */
        // Endereço
        $this->addElement('text', 'street', array(
            'label'      => 'form_addr',
            'size'       => 40,
            'filters'    => array(
                'StringTrim'
            ),
        ));
        // UF
        $this->addElement('select', 'state', array(
            'label'      => 'form_state',
            'multiOptions' => $stateList, 
        ));
        // City
        $this->addElement('select', 'city', array(
            'label'      => 'form_city',
        ));
        
        
    }
}
