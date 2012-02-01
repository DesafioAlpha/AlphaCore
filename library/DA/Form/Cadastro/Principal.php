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
 * @file         Principal.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 * 
 */

/**
 * Formulário de inscrição das equipes
 *
 * Define e adiciona valição e filtros aos campos do formulário.
 *
 * @package      DA_Forms
 * @subpackage   Cadastro_Principal
 *
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 *
 * @todo         Tornar este formulário configurável
 */
class DA_Form_Cadastro_Principal extends Zend_Form
{
    public function init()
    {
        
        
        $this->addElementPrefixPath('DA_Form_Decorator', 'DA/Form/Decorator', 'decorator'); // Prefixo para 'decorators' personalizados
        $this->addPrefixPath('DA_Form_Element', 'DA/Form/Element', 'element'); // Prefixo para 'elements' personalizados
                
        // O formulário deve ser enviado pelo método POST
        $this->setMethod('POST');
        $this->setName('pub_signup');
                
        $subform = new DA_Form_Cadastro_Equipe(); // Instancia um novo sub form
        $subform->setLegend("Dados da equipe"); // Adiciona a legenda ao fieldset
        $this->addSubForm($subform, 'team'); // Adiciona o subform dos dados da equipe ao formulário
        
        $num_int = 3; // Número máximo permitido de integrantes em uma equipe
        
        for ($i = 1; $i <= $num_int; $i++) {
            
            $subform = new DA_Form_Cadastro_Integrante(); // Instancia um novo sub form
            $subform->setLegend("{$i}º Integrante"); // Adiciona a legenda ao fieldset
            if($i == 1){
                $subform->defineValidators();
            }
            $this->addSubForm($subform, 'Int_'.$i); // Adiciona o subform ao formulário de cadastro
            
        }
        
        // Adiciona o botão de redefinir o formulário
        $this->addElement('button', 'reset', array(
            'type'      => 'reset',
            'ignore'    => true,
            'label'     => 'form_reset',
            'escape' => false,
        ));
        
        // Adiciona o botão de enviar
        $this->addElement('button', 'submit', array(
            'type'      => 'submit',
            'ignore'    => true,
            'label'     => 'form_send',
        ));

        // Envolve os botões em um elemento div
        $this->addDisplayGroup(array('reset', 'submit'), 'buttons')
             ->getDisplayGroup('buttons')
             ->setDecorators(array(
                    'FormElements', array('HtmlTag', array('tag' => 'div', 'class' => 'form_buttons'))        
        ));
        
        // Adiciona um campo de valor único para evitar ataques CSRF
        $this->addElement('hash', 'id', array(
            'ignore'    => true,
        ));
    }
}