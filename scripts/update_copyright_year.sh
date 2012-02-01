#! /bin/sh
#
# Desafio Alpha - AlphaCore
# 
# LICENÇA:
# 
# Este software é distribuído sob a licença GNU General Public License - Versão 3. 
# O conteúdo desta licença está disponível no arquivo LICENSE.txt e nos endereços web 
# http://doc.desafioalpha.com.br/legal e http://www.gnu.org/licenses/gpl.html
# 
# Qualquer dúvida sobre este arquivo fonte e como usá-lo refira-se à 
# documentação anexada à este pacote de software ou envie um email 
# para dev@desafioalpha.com.br
# 
# ESTE SOFTWARE É DISPONIBILIZADO 'NA FORMA COMO ESTÁ', O AUTOR NÃO OFERECE 
# NENHUMA GARANTIA, EXPLÍCITA OU IMPLÍCITA, SOBRE PRECISÃO E CONFIABILIDADE.
# SINTA-SE LIVRE PARA VER, MODIFICAR E REDISTRIBUIR, SOB AS CONDIÇÕES DA 
# LICENÇA APLICADA. 
#
# @category     Desafio Alpha
# @package      DA_Tools
# @file         update_copyright_year.sh
# @encoding     UTF-8
# 
# @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
# @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
# @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
#

cd .. ;

if [ $# -eq 2 ]; then
    echo '--------------------------------------------';
    echo "Atualizando informação de copyright...";
    grep -ilr "Copyright (c) 2007-$1 Desafio Alpha" *| xargs -i@ sed -i "s/Copyright (c) 2007-$1 Desafio Alpha/Copyright (c) 2007-$2 Desafio Alpha/g" @;
    echo '--------------------------------------------';
    echo 'Arquivos atualizados. Pressione ENTER para sair';
    read a;
else
    echo 'Uso: $ ./update_copyright_year.sh ano_inicial ano_atual' ;
fi ;
read a;
