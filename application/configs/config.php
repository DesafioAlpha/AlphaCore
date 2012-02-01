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
 * @file         config.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */

return array(
    'constants' => array(
        'weekDayNames' => array(
            'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'
        ),
        'email'      => "dev@desafioalpha.com.br",
        'staticSalt' => 'm2-03jtgjdsp0vm2dj9sa904T$#@',
    ),
    
    'host'        => "desafio_alpha",
   
    'locale'      => 'pt_BR',
    
    'phpSettings' => array(
        'date'     => array(
            'timezone' => 'America/Sao_Paulo',
        ),
    ),
    
    'resources'   => array(
        /* Configuração do banco de dados */
        'db'      => array(
            'adapter'      => 'PDO_MYSQL',
            'params'       => array(
                'host'     => '127.0.0.1',
                'username' => 'desafio_alpha',
                'password' => 'desafio_alpha',
                'dbname'   => 'desafio_alpha_alphacore',
                'charset'  => 'utf8'
            )
        ),
        
        /* Rotas */
        'router'      => array(
            'routes'  => array(
                'www' => array(
                    'route'    => "admin.desafio_alpha",
                )
            )
        )
    ),
    
);
