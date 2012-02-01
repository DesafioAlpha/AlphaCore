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
 * @package      DA_Lib
 * @file         Math.php
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */

/**
 * Métodos para operações matemáticas complexas não implementadas em funções padrões PHP 
 *
 * @package      DA_Lib
 * @subpackage   Math
 *
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 *
 */
class DA_Lib_Math
{
    /**
     * Calcula o digest para a mensagem ou para um valor aleatório através o algoritmo
     * passado
     * 
     * @param string $algorithm Algoritmo usado para o cálculo
     * @param string $message (optional) Mensagem 
     * 
     * @throws Exception Caso o algoritmo não seja suportado
     * @return string
     */
    public static function hash($algorithm, $message = null)
    {
        if(false === array_search($algorithm, hash_algos())){
            throw new Exception('Hash algorithm not supported!');
        }
        
        if(!$message){
            $message = time() . rand(0, 100000);    
        }
        
        return hash($algorithm, $message);
    } 
}