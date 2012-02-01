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
 * @package      DA_
 * @file         Equipe.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */

class DA_Form_Cadastro_Equipe extends Zend_Form_SubForm
{
    public function init()
    {
        
        // Adiciona o campo 'Nome da equipe'
        $this->addElement('text', 'name', array(
        
            'label'      => "team_name", // Label para I18n
            'title'      => "Escolha um nome para a equipe", // Descrição do campo
            'size'       => 40, // Tamanho grande para nome da equipe
        
            //'class'      => 'validate[required]', // Campo obrigatório
            'filters'    => array(
                'StringTrim', // Remove os espaços antes e depois do valor
            ),  
        ));
                
        // Adiciona o campo 'Email da equipe'
        $this->addElement('text', 'email', array(
            'label'      => "team_email",
            'size'       => 40, // Tamanho grande para o email da equipe
            'escape'    => false,
            
           // 'class'      => 'validate[required,custom[email]]',
            
            'filters'    => array(
                'StringTrim', // Remove os espaços antes e depois do valor
            ),
            'validators' => array(
                'EmailAddress', // Dever ter o formato de um endereço de email válido
            ),
        ));
        
        // Adiciona o campo 'Usuário da equipe'
        $this->addElement('text', 'user', array(
        'label'      => "form_user",
        'autocomplete' => 'off',        
        'class'      => 'validate[required]',
        
        'filters'    => array(
        
        ),
        'validators' => array(
        ),
        ));
        // Adiciona o campo 'Senha da equipe'
        $this->addElement('password', 'pwd', array(
        'label'      => "form_pwd",
        'autocomplete' => 'off',        
        'class'      => 'validate[required]',
        
        'filters'    => array(
        
        ),
        'validators' => array(
        array("stringLength", false, array(6, ))
        ),
        ));
        
        // Adiciona o campo 'Confirmação de senha'
        $this->addElement('password', 'pwd_check', array(
        'label'      => "form_pwd_chk",
        'autocomplete' => 'off',        
        'class'      => 'validate[required, equals[team-pwd]',
        
        'filters'    => array(
        ),
        'validators' => array(
            array('identical', false, array('token' => 'pwd')),
        ),
        ));
        
        foreach ($this->getElements() as $element){
            $element->setRequired(true); // Todos os campos desse subform são obrigatórios
            
        }
        
    }
}
