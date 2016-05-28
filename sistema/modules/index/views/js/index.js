var index_ = function(){
    
    var _private = {};
    
    _private.config = {
        modulo: 'index/'
    };
    
    this.publico = {};
    
    this.publico.postLogout = function(){
        simpleAjax.send({
            element: '#CRDACbtnEditaAccion',
            root: _private.config.modulo + 'login/logout',
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.index.EXIT
                    });
                    simpleScript.redirect('index');
                }
            }
        });
    };
    
    this.publico.getChangeRol = function(idRol){
        simpleAjax.send({
            gifProcess: true,
            root: _private.config.modulo + 'index/getChangeRol/',
            data: '&_idRol='+idRol,
            fnCallback: function(){
                simpleScript.redirect('index');
            }
        });
    };
    
    this.publico.getModulos = function(dominio){
        simpleAjax.send({
            dataType: 'html',
            gifProcess: true,
            root: _private.config.modulo + 'index/getModulos/'+dominio,
            fnCallback: function(data){
                $('#nav_modulos').html(data);
                $('nav ul').jarvismenu({
			accordion : true,
			speed : $.menu_speed,
			closedSign : '<em class="fa fa-expand-o"></em>',
			openedSign : '<em class="fa fa-collapse-o"></em>'
		});                                               
            }
        });
    };
    
    this.publico.inactividad = function(){
        simpleAjax.send({
            dataType: 'html',
            gifProcess: true,
            root: _private.config.modulo + 'index/getLock',
            fnCallback: function(data){
                $('#cont-allheader').html('');
                $('#main').html(data);
                $(document).off('mousemove');
            }
        });
    };
    
    this.publico.changeFoto = function(){
        simpleAjax.send({
            dataType: 'html',
            gifProcess: true,
            root: _private.config.modulo + 'index/changeFoto',
            fnCallback: function(data){
                $('#cont-modal').append(data);  /*los formularios con append*/
                $('#'+diccionario.tabs.INDEX+'formChangeFoto').modal('show');
            }
        });
    };
          
    this.publico.getFormViewFoto = function(ruta){
        simpleAjax.send({
            gifProcess: true,
            dataType: 'html',            
            data: '&_ruta='+ruta,
            root: _private.config.modulo + 'index/getFormViewFoto',
            fnCallback: function(data){
                $('#cont-modal').append(data);
                $('#'+diccionario.tabs.INDEX+'formViewFoto').modal('show');
            }
        });
    };
    
    this.publico.deleteImagen = function(btn,doc){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_27,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    root: _private.config.modulo + 'index/deleteImagen',
                    data: '&_doc='+doc,
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_8,
                                callback: function(){
                                    $('#'+diccionario.tabs.INDEX+'dow').attr('onclick','');
                                    $('#'+diccionario.tabs.INDEX+'dow').html(''); 
                                    $('#'+diccionario.tabs.INDEX+'btndow').css('display','none');                                                                        
                                    
                                    if (data.sexo === 'H'){
                                        $('#fotitoUser').attr("src", data.ruta+'avatars/male.png');
                                    }else{
                                        $('#fotitoUser').attr("src", data.ruta+'avatars/female.png');
                                    }                                                                                
                                }
                            });
                        }
                    }
                });
            }
        });
    };
    
    return this.publico;
    
};
 var index = new index_();