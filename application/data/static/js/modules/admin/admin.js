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
 * @package      DA_Admin
 * @file         admin.js
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 * 
 */
$(function() {
    
    /**
     * Configuração global do ajax.
     */
	$.ajaxSetup({
	    'global' : true,
	    'error'  : function(){
	        admin.sheet_notify.ajaxError();
	    }
	});
	
	/**
	 * Métodos de inicialização.
	 */
	admin.main_menu.build();
	
});

/**
 * Métodos globais para administração
 * 
 * @package      DA_Admin
 * @subpackage   Admin
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */
admin = {};

/**
 * Menu/dock principal.
 */
admin.main_menu    = {
    'build'  : function() {
        var dock = $('#dock');
        
        if(dock.is('div')){
            dock.Fisheye({
                maxWidth  : 40,
                items     : 'a',
                itemsText : 'span',
                container : '.dock_container',
                itemWidth : 60,
                proximity : 40,
                alignment : 'left',
                valign    : 'bottom',
                halign    : 'center'
            }).find('a').bind('mouseover', function() {
                $("#menu_detail").html($(this).attr('title'));
            }).bind('mouseout', function() {
                $("#menu_detail").html('');
            });
        }
    }
};

/**
 * Notificação deslizante.
 */
admin.sheet_notify = {
    'timers' : [],
    'open'    : function(text, options) {
        
        /* Apaga todos os timeouts residuais */
        var timeoutLength = admin.sheet_notify.timers.length;
        for(var i = 0; i < timeoutLength; i++){
            clearTimeout(admin.sheet_notify.timers[i]);
        }
        admin.sheet_notify.timers = [];
        
        this.close(function(){
            var slidenotify = $("#slide_sheet").find('.notify');
            if(options.class){
                slidenotify.addClass(options.class);
            }
                slidenotify.text(text).slideDown(200);
            
            if(!isNaN(options.timeout)){
                /* Adiciona o identificador do timer à lista */
                admin.sheet_notify.timers[admin.sheet_notify.timers.length] = setTimeout(function(){
                    admin.sheet_notify.close();
                }, options.timeout);
            }                    
        });
        
    },
    
    'close' : function() {
        
        closecallback = arguments[0];
        var slidenotify = $("#slide_sheet").find('.notify');
        slidenotify.slideUp(200, function(){
            slidenotify.text('').removeClass().addClass('notify');
            if((typeof closecallback) == 'function'){
                closecallback();
            }
        });                               
    },
    
    'ajaxError' : function (){
        this.open('Ops! Temos problemas', {'class': 'red', 'timeout' : 5000});
    }
};

/**
 * Caixa de diálogo deslizante.
 */
admin.sheet_dialog = {
    'open' : function(title, contents, callbacks){
       
        var slidesheet = $("#slide_sheet").find(".dialog");
        
        open = function(){
                slidesheet.find('p.title').text(title);
            
                
            var contents_div = slidesheet.find('div.contents');
                contents_div.empty().append(contents);
            
            /* Cria o botão OK se houver uma função associada a ele */
            if(typeof callbacks.ok == 'function'){
                ok_button = $('<a href="#" class="button ok"><span>OK</span></a>').bind('click', function(){
                    if(callbacks.ok(contents_div)){ 
                        admin.sheet_dialog.close(); 
                    }
                    return false;
                });
                slidesheet.find("div.form_buttons").append(ok_button);
                slidesheet.unbind('keydown').bind('keydown', function(event){
                    if(event.keyCode == '13'){
                        ok_button.click();
                    }
                });
            }
            
            /* Atribui uma função ao botão 'Close' */
            if(typeof callbacks.cancel == 'function'){
                slidesheet.find('a.close').bind('click', function(){callbacks.cancel(); });
            }
            slidesheet.find("a.close").bind('click', function(){admin.sheet_dialog.close(); return false;});
            
            slidesheet.slideDown(300,function(){
                contents_div.find('input:first').focus();
            });
            
            slidesheet.data('is_opened', 1);
        };
        
        
        if(slidesheet.data('is_opened') == 1){ // Se já estiver aberta, feche, aguarde 1s e reabra-o com novo conteúdo
            slidesheet.find('a.close').click();
            setTimeout(function(){
                open();
            }, 800);
            
        }else{
            open();
        }
        
    },
    'close' : function(){
        var slidesheet = $("#slide_sheet").find(".dialog");
            slidesheet.slideUp(200, function(){
                slidesheet.find('p.title').text('');
                slidesheet.find('div.contents').empty();
                slidesheet.find('a.close').unbind();
                slidesheet.find('a.ok').remove();
                slidesheet.data('is_opened', 0);
            });
        
    }
};