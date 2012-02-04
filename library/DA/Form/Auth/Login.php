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

class DA_Form_Auth_Login extends Zend_Form
{
    public function init()
    {

        $this->addPrefixPath('DA_Form_Element', 'DA/Form/Element', 'element'); // Prefixo para 'elements' personalizados
        
        $this->addElement('text', 'username',
            array(
                'label' => 'form_user',
                'required' => true,
            )
         );
         
         $this->addElement('password', 'password',
            array(
                'label' => 'form_pwd',
                'required' => true,
            )
         );
         
         $this->addElement('button', 'send',
            array(
                'label' => 'form_login',
                'type'  => 'submit',
                'ignore' => true
            )
         );
         
         $this->addElement('checkbox', 'remember',
            array(
                'label'   => 'form_rememberme',
                'class'   => 'indent',
                'checked' => true
            )
         );

         $this->getElement('remember')->setDecorators(array(
                     'ViewHelper',
                     array('Label', array('placement' => 'APPEND', 'class' => 'indent')),
                     
         ));
         
         // Adiciona um campo de valor único para evitar ataques CSRF
         $this->addElement('hash', 'id', array(
            'ignore'    => true,
         ));
         
         // Envolve os botões em um elemento div
         $this->addDisplayGroup(array('send', 'remember'), 'buttons')
              ->getDisplayGroup('buttons')
              ->setDecorators(array(
                 'FormElements', array('HtmlTag', array('tag' => 'div', 'class' => 'form_buttons'))
         ));
    }
}   
