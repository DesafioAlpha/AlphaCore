Desafio Alpha - AlphaCore
====
Introdução
----
Este pacote de software, também chamado de *AlphaCore* corresponde à base para
servir clientes a partir de webservices.
Também inclui interfaces básicas para clientes humanos acessando através de um 
navegador web.
Os demais módulos acoplados (denominados *BetaX*) correspondem às implementações 
de clientes que irão consumir os serviços disponibilizados pelo *AlphaCore*.

O que é o projeto Desafio Alpha?
----
O Projeto *Desafio Alpha* surgiu em 2007 como uma competição online baseada na 
resolução de desafios de conhecimento geral voltada à alunos do Ensino Fundamental 
e Médio.
Este pacote de software faz parte da infraestrutura que suporta o andamento do 
projeto pela internet. É uma aplicação web simples, porém com muitos módulos e
recursos, majoritariamente escrita em PHP que foi reescrita recentemente sobre a 
Zend Framework e liberado como OpenSource sob a licença GNU GPL - versão 3.
Agradecemos todas as contribuições para fazer este software melhor.

Requisitos
----
Servidor web configurado e capaz de interpretar PHP 5.2.3 ou superior
Requisitos básicos da biblioteca [Zend Framework](http://framework.zend.com/manual/en/requirements.introduction.html, 'Zend Framework Requirements')
DBMS compatível com a camada de abstração da biblioteca [Doctrine 2](http://www.doctrine-project.org/, 'Doctrine')

### Bibliotecas
[Zend Framework 1.11+](http://framework.zend.com/, 'Zend Framework')
[Doctrine 2](http://www.doctrine-project.org/, 'Doctrine')

Para outras informações sobre requisitos e dependências, vide diretórios `/docs/` e `/library/`

Instalação
----
A instalação é manual, bastando-se três etapas:

1. Execução dos arquivos `*.sql` disponíveis em `/bin/` para a criação e configuração 
da infraestrutura de banco de dados da aplicação.
2. Cópia do conteúdo para um diretório acessível pelo seu servidor web e a devida 
configuração para apontar o diretório `/public/` como raiz.
3. Verificação dos arquivos de configuração no diretório `/application/configs/`.

Futuramente pretendemos disponibilizar um shell script para sistemas baseados em Unix, 
tais como Linux, Mac OSX, BSD, Solaris e um arquivo de comandos em lote para sistemas
MS Windows para a execução automatizada dessas tarefas.
Se você tiver tempo e conhecimento para isso, agradeceremos a contribuição. Basta 
criar um fork do projeto e enviar suas modificações.

Licença
----
Este software é distribuído sob a licença GNU General Public License - Versão 3. 
O conteúdo desta licença está disponível no arquivo LICENSE.txt e nos endereços web 
(http://doc.desafioalpha.com.br/legal) e (http://www.gnu.org/licenses/gpl.html)

Qualquer dúvida sobre este arquivo fonte e como usá-lo refira-se à 
documentação anexada à este pacote de software ou envie um email para (dev@desafioalpha.com.br)

ESTE SOFTWARE É DISPONIBILIZADO 'NA FORMA COMO ESTÁ', O AUTOR NÃO OFERECE 
NENHUMA GARANTIA, EXPLÍCITA OU IMPLÍCITA, SOBRE PRECISÃO E CONFIABILIDADE.
SINTA-SE LIVRE PARA VER, MODIFICAR E REDISTRIBUIR, SOB AS CONDIÇÕES DA 
LICENÇA APLICADA. 

Os demais pacotes de software e bibliotecas distribuídos juntamente com este estão sujeitos 
às suas próprias licenças e disclaimers, para tais informações consulte os diretórios `/docs/`e 
`/library/`

Suporte
----
Temos o prazer de oferecer suporte, porém limitado, à quaisquer assuntos referentes a 
este software.
Para isto, envie um email para dev@desafioalpha.com.br indicando o assunto e descrevendo o
problema e/ou dúvida.
ou através do issue tracker do github.com

Autores
----
Copyright (c) 2007-2012 Desafio Alpha Dev Team (dev@desafioalpha.com.br)

Contato
----
Christian Cândido da Silva (christian@desafioalpha.com.br)
Gilherme Hideo Tubone (guilherme@desafioalpha.com.br)