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
 * @file         questions.js
 * @encoding     UTF-8
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 * 
 */
$(function() {
	
	$(window).bind("beforeunload", function(){
	    if(question_list.hasUnsaved > 0){
	        return "Você tem dados não salvos!";
	    }
    });
	
	questionContainers = $(".question_container");
	questionContainers.each(function(){
		question.prepare($(this));
	});
	
	
	searchBar = $("#search_bar");
	
	searchBar.find('a.last').each(function(){
	    $(this).bind('click', function(){
    	    if(question_list.loadList( {
                'limit'  : $(this).attr('rel')
            }, $(this))){
    	    }
    	    return false;
	    });
	});
	
	searchBar.find('a.status').each(function(){
	    
	    $(this).bind('click', function(){
            if(question_list.loadList( {
                'status'  : $(this).attr('rel')
            }, $(this))){
            }
            return false;
	    });
    });
	
	searchBar.find('a').bind('click', function(){
	    
	    $(this).addClass("selected").siblings("a").removeClass("selected");
        
	}).filter('.selected').click();
	
	$("#new_question").bind('click', function(){
	    question.newQuestion();
	    return false;
	});
	
	$("#save_all").hide().bind('click', function(){
        question_list.saveAll();
        $(this).fadeOut();
        return false;
    });
	
	searchBar.find('a.new_questions').bind('click', function(){
	    $("#question_list").hide();
	    $("#question_list_new").show();
	    return false;
	});
	
});

/**
 * Abstração da lista de desafios.
 * 
 * Contém métodos para manipulação de listas de desafios.
 * 
 * @package      DA_Admin
 * @subpackage   Questions
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */
question_list = {
    'hasUnsaved'   : 0,
    'newUnsaved'   : 0,
    
    'checkUnsaved' : function(){
        if((question_list.hasUnsaved - question_list.newUnsaved) > 0){
            if(confirm("Existem alterações não salvas, tem certeza de que deseja prosseguir?")){
                question_list.hasUnsaved = 0;
                return true;
            }
            return false;
        }
        
        return true;
    },
    
    'prepare'      : function() {
        $("#question_list").find(".question").find('.header').bind('click', function(){
            
            question_list.loadQuestion($(this));
            return false;
            
        }).next('button.reset').bind('click', function(){
            
            if(confirm("Tem certeza de que deseja descartar todas as alterações?")){
                $(this).next().slideUp(function(){
                    question.saved($(this).find(".question_container").attr('id'));
                    question_list.loadQuestion($(this));
                    
                });
            }       
        });
    },
    
    'loadList'     : function(searchParams, where){
     
                if(where.data('is_active') != 1){
            
            if(!question_list.checkUnsaved()){
                return false;
            }
            
            searchParams.search = 1;
            admin.sheet_notify.open('Carregando desafios...', {});
            
            $.ajax({
                'url'      : '/questions/view',
                'type'     : 'GET',
                'dataType' : 'json',
                'data'     : searchParams,
                'success'  : function(data) {
                    if(data.length == 0){
                        $("#question_list").html('<div class="errors">Não há desafios nesta categoria</span>');
                    }else{
                        question_list.renderList(data);
                        question_list.prepare();
                    }
                    admin.sheet_notify.close();
                }
            });
            
            where.siblings('a').data('is_active', 0);
            where.data('is_active', 1);
        }
        
        $("#question_list").show();
        $("#question_list_new").hide();
            
        return true;
        
    },
    
    'loadQuestion' : function( header ){
        var where = header.parent('.question');
        
        admin.sheet_notify.open('Carregando desafio...', {});
        
        $.ajax({
            'url'     : '/questions/view/id/' +  where.find('input.question_id').val(),
            'type'    : 'GET',
            'dataType': 'html',
            'success' : function(data){
                
                admin.sheet_notify.close();
                
                var questionLoad = where.find('.question_load');
                    questionLoad.html(data);
                
                where.find('.header').unbind('click').bind('click', function(){
                    where.find('.question_load').slideToggle();
                    return false;
                }).addClass('loaded');
                
                questionLoad.slideDown();
                question.prepare(questionLoad.find(".question_container"));
                
                
                
            }
        });
    },
    'renderRow'   : function (data) {
        var html = $("#question_template").clone();
        
        html.find('span.flag').addClass(
                (data.status == 1)?'yellow':((data.status == 2)?'red':'')
        );
        html.find('input.question_id').val(data.question_id);
        html.find('span.question_id').text(data.question_id);
        html.find('span.edit_username').text(data.edit_username);
        html.find('span.edit_lasttime').text(data.edit_lasttime);
        
        return html.html();
    },
    
    'renderList'  : function (data) {
        var dataLength = data.length;
        var html = [];
        
        for(var i = 0; i < dataLength; i++){
            html[i] = question_list.renderRow(data[i]);
        }
        
        
        $("#question_list").html(html.join(''));
    },
    
    'saveAll'   : function () {
        $(".question_container.unsaved").each(function(){
            question.save($(this).attr('id')); 
        });
    }
};

/**
 * Abstração de um desafio.
 * 
 * Contém métodos para manipulação de caixas de desafios.
 * 
 * @package      DA_Admin
 * @subpackage   Questions
 * 
 * @author       Desafio Alpha Dev Team <dev@desafioalpha.com.br>
 * @copyright    Copyright (c) 2007-2012 Desafio Alpha (http://desafioalpha.com.br)
 * @license      http://doc.desafioalpha.com.br/legal/gplv3 GPLv3
 */
question = {
    
    /**
     * Prepara o elemento DOM para as funções de edição de desafio.
     * 
     * @param {Object} where
     */
    'prepare'   	 : function( where ) {
    
        var questionId = where.attr('id'); 
        
        where.find('form'  ).bind('submit', function(){return false;});
        where.find(".value").bind('click', function(){question.editValue(questionId);}).addClass('hand');
        where.find(".flag" ).bind('click', function(){question.changeStatus(questionId);}).addClass('hand');
        
        where.find(".sheetDialog").find("a.button.ok").bind('click', function(){question.toggleSheet(questionId);});
        
        var text = where.find(".text");
    
        question.highlight(text).bind('click', function(){question.editText(questionId);});
        
        if($.trim(text.find('textarea').text()) == ''){
            text.click();
        }
    
        var answer = where.find(".answer");
        question.highlight(answer).bind('click', function(){question.editAnswer(questionId);});
        
        if($.trim(answer.find('input').val()) == ''){
            answer.click();
        }
        
        where.find("button.add_media").bind('click', function(){question.addMedia(questionId); return false;});
        where.find("button.save").bind('click', function(){question.save(questionId); return false;});
        where.find("button.delete").bind('click', function(){question.deleteQuestion(questionId); return false;});
        
        
        where.find('.attachment').bind('click', function(){
           
            question.loadMedia( questionId );
            $(this).unbind('click');
            return false;
        });
    },
    
    /**
     * Atribui eventos de clique em caixas.
     * 
     * @param   {Object} where
     * @returns {Object}
     */
    'highlight'  	 : function ( where ) {
        
        where.append('<div class="content">' + where.find('input, textarea').hide().val() + '</div>');
        return where.attr('title', 'Clique para editar')
                    .bind('mouseover', function(){$(this).addClass('highlight'); })
                    .bind('mouseout', function(){$(this).removeClass('highlight'); });
    },
    
    /**
     * Carrega novo desafio.
     * 
     * @param {Object} where  
     */
    'newQuestion'    : function ( where ) {
        
        var questionListNew = $("#question_list_new").show();
        
        $.ajax({
            'url'      : '/questions/new',
            'type'     : 'POST',
            'dataType' : 'html',
            'data'     : {
                'new'  : 1
            },
            'success'  : function (data) {
                var newQuestion = $(data);
                
                questionListNew.append(newQuestion);                    
                question.prepare(newQuestion);
                question_list.newUnsaved++;
                searchBar.find('a.new_questions').show().click();
            }
        });
    },
    
    /**
     * Salva o desafio com o ID passado.
     * 
     * @param {String} questionId
     */
    'save'		     : function ( questionId ) {
    	var where = $("#" + questionId);
    	    where.find(".save").unbind().attr('disabled', 1);
    	
    	var data = {};
    		data.id 	= where.find('input.question_id').val();
    		data.text   = $.trim(where.find('.text textarea.question_edit').val());
    		data.answer = where.find('.answer input.question_edit').val();
    		data.value  = where.find('.value input').val();
    		data.status = where.find('.flag input').val();
    	
    	admin.sheet_notify.open('Salvando...', {});
    	
    	$.ajax({
    		'url'      : '/questions/save/',
    		'dataType' : 'json',
    		'data'	   : data,
    		'type'	   : 'POST',
    		'success'  : function(data){
    		    try {
        			if(!data === true){
        			    admin.sheet_notify.ajaxError();
        			}else{
        				question.saved(questionId);
        				admin.sheet_notify.open('Alterações salvas', {'class' : 'green', 'timeout' : 2000});
        			}
    			} catch (e) {
    			    admin.sheet_notify.ajaxError();
    			}
    		},
    		'complete' : function(){
    		    where.find(".save").bind('click', function(){question.save(questionId); return false;}).removeAttr('disabled');
    		}
    	});
    		
    },
    
    /**
     * Envia pedido da exclusão para o servidor.
     * 
     * @param {String} questionId
     */
    'deleteQuestion' : function ( questionId ) {
        
        if(confirm("Tem certeza de que deseja excluir este desafio e todos os recursos atribuídos à ele?")){
            var where = $("#" + questionId);
            
            $.ajax({
                'url'  : '/questions/delete/',
                'type' : 'POST',
                'data' : {
                    'question_id'   : where.find('input.question_id').val()
                },
                'dataType' : 'json',
                'success' : function ( data ) {
                    try {
                        if(!data === true){
                            admin.sheet_notify.ajaxError();
                        }else{
                            where.parent('.question_load').parent('.question').remove();
                            where.remove();
                            admin.sheet_notify.open('Desafio removido com sucesso!', {'class' : 'green', 'timeout' : 2000});   
                        }
                        
                    } catch (e) {
                        admin.sheet_notify.ajaxError();
                    }
                }    
            });    
        }
    },
    
    /**
     * Marca o desafio como não salvo e incrementa contadar.
     * 
     * @param {String} questionId
     */
    'unsaved' 	     : function ( questionId ) {
    	 var where = $("#" + questionId);
    	 
    	 if(where.data('is_unsaved') != 1){
    		 
    		 var name = where.find('.name');
    		 
    		 if(!name.find('span.unsaved').show().is('span')){
    			 name.prepend('<span class="unsaved">(*) </span>');
    		 }
    		 
    		 var question = where.parent('.question_load').parent('.question');
    		     question.find('button.reset').fadeIn();    
    		     question.find('.header').addClass('unsaved');
    		 
    		 where.data('is_unsaved', 1);
    		 where.addClass('unsaved');
    		 
    		 question_list.hasUnsaved++;
    		 
    		 $("#save_all").fadeIn();
    	 }
    },
    
    /**
     * Marca o desafio como salvo e decrementa o contador.
     * 
     * @param {String} questionId
     */
    'saved'		     : function ( questionId ) {
        var where = $("#" + questionId);
        
        if(where.data('is_unsaved') == 1){
    		
    		var name = where.find('.name');
    			name.find('span.unsaved').hide();
    		
    		var question = where.parent('.question_load').parent('.question');
                question.find('button.reset').fadeOut();    
                question.find('.header').removeClass('unsaved');
                
    			where.data('is_unsaved', 0);
    			question_list.hasUnsaved = Math.max(0, --question_list.hasUnsaved);
        }
    },
    
    /**
     * Permite a edição da resposta do desafio.
     *  
     * @param {String} questionId 
     */
    'editAnswer'     : function ( questionId ) {
    
    	var where = $("#" + questionId).find(".answer");			
    	var status = where.data('editable');
    
    	switch (status) { // Fecha edição
    	case 1:
    		/* Passa o conteúdo do input para a div, exibindo-a */
    	    var value = where.find('input.question_edit').val();
    	    if($.trim(value) == ''){
    	        return false;
    	    }
    	    
    	    where.find('input.question_edit').hide();
    		where.find('div.content').show().text(value);
    		where.data('editable', 0).bind('click', function(){question.editAnswer(questionId);});
    		break;
    
    	default: // Abrir edição
    		
    		where.find('div.content').hide();
    		where.find('input.question_edit').show().unbind().bind('keydown', function(event){
    			if(event.keyCode == '13' || event.keyCode == '27'){ // Enter (13), Esc (27)
    				question.editAnswer( questionId );
                }else{
    				question.unsaved(questionId);
    			}
    		}).bind('change', function(){
    			question.unsaved(questionId);
    		});
    		
    		where.data('editable', 1).unbind('click');
    		break;
    	}			
    },
    
    /**
     * Permite a edição do enunciado do desafio.
     * 
     * @param   {String} questionId
     * @returns {Boolean}
     */
    'editText'       : function( questionId ) {
    		
    	var where = $("#" + questionId).find(".text");
    
    	var status = where.data('editable');
    
    	switch (status) {
    	case 1:
    		
    	    value = where.find('textarea.question_edit').text();
    	    if(($.trim(value).replace(/\n|\r|&nbsp;/g, '')) == ''){
    	        return false;
    	    }
    	    
    	    where.find('textarea.question_edit').tinymce().hide();
    		where.find('div.content').show().html(where.find('textarea.question_edit').hide().val());
    		
    		where.data('editable', 0).bind('click', function(){question.editText(questionId);});
    		
    		break;
    
    	default:
    	    where.find('div.content').hide();
    	    if(edit = arguments[1]){
    	        edit.show();
    	    }
    		
    	    document.domain = da.maindomain;
    	    
    	    where.find('textarea.question_edit').show().tinymce({
                theme : "advanced",
                plugins : 'inlinepopups',
                theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,forecolor,backcolor,fontsizeselect,|,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,undo,redo,unlink",
                theme_advanced_buttons2 : "",
                theme_advanced_buttons3 : "",
                theme_advanced_toolbar_location : "top",
                theme_advanced_toolbar_align : "left",
                
                setup: function (ed) {
                  ed.onKeyDown.add(function(ed, event){
                      if(event.keyCode == '27'){ // Esc (27)
                          question.editText( questionId);
                      }else{
                          question.unsaved(questionId);
                      }
                  });
                  
                  ed.onChange.add(function(ed, event){
                      question.unsaved(questionId);
                  });
                }
                
    		});
    		
    		where.data('editable', 1).unbind('click');
    		break;
    	}							
    },
    
    /**
     * Permite a alteração do valor atribuído ao desafio.
     * 
     * @param  {String} questionId
     */
    'editValue'      : function( questionId ) {
        var where = $("#"+ questionId);
        
        if(where.data('is_value_edit') != 1){
    	       
            var contents = $('<input type="text" value="'+where.find(".value input").val()+'">');
    	    
    	    var ok_callback = function(contents_div){
    	        var value = contents_div.find('input').val();
    	        
    	        /* Aceita apenas valores inteiros positivos */
    	        if(isNaN(value) || value < 0 || value % 1 != 0){
    	            alert("O valor deve ser um número inteiro positivo!");
    	            return false;
    	        }
    	        
    	        where.find(".value input").val(value);
                where.find(".value span").text(value);
                where.data('is_value_edit', 0);
                
                question.unsaved(questionId);
                
                return true;
    	    };
    	    
    	    var cancel_callback = function(){
    	        where.data('is_value_edit', 0);
    	    };
    	    
    	    
    	    admin.sheet_dialog.open('Valor da questão', contents, {'ok' : ok_callback, 'cancel' : cancel_callback});
    	    
    	    where.data('is_value_edit', 1);
        }
    },
    
    /**
     * Marca desafio como Final/Revisão/Excluir.
     * 
     * @param  {String} questionId
     */
    'changeStatus'   : function ( questionId ) {
        var where = $("#"+ questionId);
        var status = parseInt( where.find('.flag input').val(), 10);
        
        where.find('.flag').removeClass("yellow red").addClass(
                (status == 0)?"yellow":((status == 1)?'red':'')
        ).find('input').val(
                (status < 2)?(++status):0
        );
        
        question.unsaved(questionId);
    }, 
    
    /**
     * Abre caixa de diálogo para envio de mídia.
     * 
     * @param  {String} questionId
     */
    'addMedia'       : function ( questionId ) {
        var where = $("#" + questionId);
        if(where.data('is_adding_media') != 1){
            
            where.data('is_adding_media', 1);
    	        
	        var uploader = $($('#uploader_template').clone().html());
    	        
	        /* Switchs de contexto */
            var context_switch = uploader.find('.context_switch');
    	        context_switch.find('a').bind('click', function(){
    	            context_switch.find('a').removeClass('selected');
    	            $(this).addClass('selected');
    	            var context = $(this).attr('rel');
    	            
    	            uploader.find('.uploader').slideUp(300, function(){
    	                uploader.find('.file_queue').empty();
    	                uploader.find('.progress_bar').hide();
    	                buildUploader(context, where.find('input.question_id').val(), uploader.find('input'));
    	                $(this).slideDown();
    	            });
    	            
    	            return false;
    	        });
    	        
    	        uploader.find('input').fileupload({
    	            'url'     : '/upload/',
                    'sequentialUploads' : true,
                    'dataType' : 'json',
    	            'success' : function(data){
                        try {
                            if(data[0] === false){
                                admin.sheet_notify.ajaxError();
                            }else{
                                admin.sheet_notify.open('Arquivo enviado!', {'class' : 'green', 'timeout': 2000});   
                                question.loadMedia(questionId);
                            }
                        }catch (e) {
                            admin.sheet_notify.ajaxError();
                        }
                    },
    	            'start'  : function(){
                        uploader.find('.progress_bar').width(0).show();
                        admin.sheet_notify.open('Enviando arquivo...', {});
                    },
                    'progress' : function (e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        uploader.find('.progress_bar').animate({'width' : progress + "%"});
                    },
                    'add'     : function(e, data) {
                        uploader.find('button.start').bind('click', function(){
                            data.submit(); 
                        });
                        $.each(data.files, function(index, file){
                           
                            uploader.find('.file_queue').append('<li>'+file.name+'</li>');
                        });
                    }
    	        });
    	        
            var buildUploader = function(context, question_id, input){
               console.log(question_id);
                input.fileupload({
    	            'formData'    : {
    	                'question_id' : question_id,
    	                'context'     : context
    	            }
    	        });
            };
            
    	    var cancel_callback = function(){
                where.data('is_adding_media', 0);
            };
            
    	    admin.sheet_dialog.open('Adicionar mídia', uploader, {'cancel' : cancel_callback});
    	}
    },
    
    /**
     * Atualiza container de mídia.
     * 
     * @param {String} questionId
     */
    'mediaUpdate'    : function ( questionId ) {
        var where = $("#" + questionId);
        var hasMedia = 0;
        where.find('.media').find('ul').each(function(){
            hasMedia++;
            if($(this).find('li').length == 0){
                hasMedia--;
                $(this).hide();
            }
        });
        
        if(hasMedia == 0){
            where.find('.attachment').hide();
        }else{
            where.find('.attachment').show();
        }
    },
    
    /**
     * Carrega os recursos de mídia associados ao desafio.
     * 
     * @param {String} questionId
     */
    'loadMedia'      : function ( questionId ) {
        var where = $("#" + questionId);
        $.ajax({
            'url'      : '/questions/view/media/1/id/' + where.find('input.question_id').val(),
            'type'     : 'POST',
            'dataType' : 'html',
            'success'  : function ( data ) {
                where.find('.media').html(data);

                question.mediaUpdate(questionId);
                
                where.find('.media').find('ul.image').find('a').fancybox({
                    'transitionIn'  :   'elastic',
                    'transitionOut' :   'elastic'
                });
                
                where.find('.media').find('li').each(function(){
                    var mediaId = $(this).find('a').attr('id');
                    var deleteButton = $('<div class="delete"></div>').bind('click', function(){
                        question.deleteMedia(questionId, mediaId);
                    }); 
                    $(this).prepend(deleteButton);
                    
                    $(this).bind('mouseover', function(){
                        $(this).find('.delete').show(); 
                    });
                    $(this).bind('mouseout', function(){
                        $(this).find('.delete').hide(); 
                    });
                    
                });
                
                where.find('.media').find('ul.video').find('li').each(function(){
                               
                   $f($(this).addClass('video').find('a').attr('id'), {src: da.static_url + "/js/3rd_party/jquery/plugins/flowplayer/flowplayer-3.2.7.swf", wmode: 'transparent'}, {
                        plugins: {
                            controls: {
                                fullscreen: false,
                                autoHide: false
                            }
                        },
                        clip: {
                            autoPlay: false,
                            onBeforeBegin: function() {
                            }
                        }
                     });
                   
                });
            }
        });
    },
    
    /**
     * Envia solicitação de exlusão de recurso de mídia.
     *  
     * @param {String} questionId
     * @param {String} mediaId
     */
    'deleteMedia'    : function ( questionId, mediaId ) {
        var where = $("#" + questionId);
        var media_id = mediaId.split('_');
        
        if(confirm('Você tem certeza de que deseja exluír este recurso?')){
            $.ajax({
                'url'   : '/questions/delete/',
                'type'  : 'POST',
                'dataType' : 'json',
                'data'  : {
                    'question_id' : where.find('input.question_id').val(),
                    'media_id'    : media_id[1]
                },
                'success' : function ( data ) {
                    if(data === false) {
                        admin.sheet_notify.ajaxError();  
                    } else {
                        $("#" + mediaId).parent('li').remove();
                        question.mediaUpdate( questionId );
                        admin.sheet_notify.open('Recurso de mídia excluído', {'class' : 'green', 'timeout' : 4000 });
                    }
                }
            });
        }
    }
};
