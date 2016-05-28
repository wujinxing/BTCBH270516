var configurarUsuarios_ = function(){
    
    var _private = {};
    
    _private.idUsuario = 0;
    
    _private.tab = 0;
    _private.rol = '';
    _private.mapaSitio = '';
    
    _private.config = {
        modulo: 'usuarios/configurarUsuarios/'
    };
    
    this.publico = {};
    
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.T4,
            label: $(element).attr('title'),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                configurarUsuarios.getContenido();
            }
        });
    };
    
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: 'html',
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $('#'+diccionario.tabs.T4+'_CONTAINER').html(data);
                configurarUsuarios.getGridUsuarios();
            }
        });
    };
    
    this.publico.getGridUsuarios = function (){
        var oTable = $('#'+diccionario.tabs.T4+'gridUsuariosx').dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.ULTIMOACCESO, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.USUARIO,sWidth: "20%"},
                {sTitle: lang.generico.NOMBREAPELLIDO, sWidth: "20%"},                
                {sTitle: lang.generico.ROLES, sWidth: "20%", bSortable: false},
                {sTitle: lang.generico.ESTADO, sWidth: "7%",  sClass: "center", bSortable: false},                
                {sTitle: lang.generico.ACCIONES, sWidth: "18%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[0, 'desc']],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+'getUsuarios',
            fnDrawCallback: function() {
                $('#'+diccionario.tabs.T4+'gridUsuariosx_filter').find('input').attr('placeholder',lang.busqueda.T4).css("width","350px");
                simpleScript.enterSearch("#"+diccionario.tabs.T4+'gridUsuariosx',oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: '#widget_'+diccionario.tabs.T4+'usuarios',
                    typeElement: 'button'
                });               
           }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getNuevoUsuario = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: 'html',
            root: _private.config.modulo + 'getNuevoUsuario',
            fnCallback: function(data){
                $('#cont-modal').append(data);  /*los formularios con append*/
                $('#'+diccionario.tabs.T4+'formUsuario').modal('show');
            }
        });
    };
    
    this.publico.getEditUsuario = function(btn,id){
        _private.idUsuario = id;
        
        simpleAjax.send({
            element: btn,
            dataType: 'html',
            root: _private.config.modulo + 'getEditUsuario',
            data: '&_idUsuario='+_private.idUsuario,
            fnCallback: function(data){
                $('#cont-modal').append(data);  /*los formularios con append*/
                $('#'+diccionario.tabs.T4+'formUsuario').modal('show');
            }
        });
    };
    
    this.publico.getMensaje = function(btn,id,usu,mail, idioma){
        simpleAjax.send({
            element: btn,
            dataType: 'html',
            root: _private.config.modulo + 'getViewMensaje',
            data: '&_nombres='+usu+'&_idUsuario='+id+'&_mail='+mail+'&_idioma='+idioma,            
            fnCallback: function(data){
                $('#cont-modal').append(data);  /*los formularios con append*/
                $('#'+diccionario.tabs.T4+'formViewMensaje').modal('show');
            }
        });                               
    };
    
    this.publico.getFormEmpleado = function(btn,tab,rol,label){
        _private.tab = tab;
        _private.rol = rol;
        simpleAjax.send({
            element: btn,
            dataType: 'html',
            root: _private.config.modulo + 'getFormEmpleado',            
            fnCallback: function(data){
                $('#cont-modal').append(data);  /*los formularios con append*/                
                $('#'+diccionario.tabs.T4+'formBuscarEmpleado').modal('show');
                $('#'+diccionario.tabs.T4+'formBuscarEmpleado h4.modal-title').html(label);
            }
        });
    };
    
    this.publico.getEmpleados = function(){
        $('#'+diccionario.tabs.T4+'gridEmpleadosFound_filter').remove();
        $('#'+diccionario.tabs.T4+'gridEmpleadosFound').dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sServerMethod: "POST",
            bPaginate: false,
            aoColumns: [
                {sTitle: lang.generico.NRO, sClass: "center",sWidth: "2%",  bSortable: false},
                {sTitle: lang.generico.NOMBREAPELLIDO, sWidth: "88%"}
            ],
            aaSorting: [[1, 'asc']],
            sScrollY: "250px",
            sAjaxSource: _private.config.modulo+'getEmpleados',
            fnServerParams: function(aoData) {
                aoData.push({"name": diccionario.tabs.T4+"_term", "value": $('#'+diccionario.tabs.T4+'txt_search').val()});
                aoData.push({"name": "_tab", "value": _private.tab});
                aoData.push({"name": "_rol", "value": _private.rol});
            },
            fnDrawCallback: function() {
                $('#'+diccionario.tabs.T4+'gridEmpleadosFound_filter').remove();
                $('#'+diccionario.tabs.T4+'gridEmpleadosFound_wrapper').find('.dt-bottom-row').remove();
                $('#'+diccionario.tabs.T4+'gridEmpleadosFound_wrapper').find('.dataTables_scrollBody').css('overflow-x','hidden');
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: '#'+diccionario.tabs.T4+'gridEmpleadosFound',
                    typeElement: 'a'
                });
            }
        });
        $('#'+diccionario.tabs.T4+'gridEmpleadosFound_filter').remove();
    };
    
    this.publico.postNuevoUsuario = function(){
        simpleAjax.send({
            flag: 1,
            element: '#'+diccionario.tabs.T4+'btnGrabaAccion',
            root: _private.config.modulo + 'postNuevoUsuario',
            form: '#'+diccionario.tabs.T4+'formUsuario',
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            simpleScript.reloadGrid('#'+diccionario.tabs.T4+'gridUsuariosx'); 
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.usuario.VAL08
                    });
                }
            }
        });
    };
    
    this.publico.postEditarUsuario = function(){
        simpleAjax.send({
            flag: 3,
            element: '#'+diccionario.tabs.T4+'btnEditAccion',
            root: _private.config.modulo + 'postEditarUsuario',
            form: '#'+diccionario.tabs.T4+'formUsuario',
            data: '&_idUsuario='+_private.idUsuario, 
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            _private.idUsuario = 0;
                            simpleScript.reloadGrid('#'+diccionario.tabs.T4+'gridUsuariosx'); 
                            simpleScript.closeModal('#'+diccionario.tabs.T4+'formUsuario');
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.usuario.VAL01
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.error({
                        content: lang.usuario.VAL02
                    });
                }
            }
        });
    };
    
    this.publico.postDeleteUsuario = function(){
        var id = simpleScript.getParam(arguments[0]);
        
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({
                    flag: 3,
                    gifProcess: true,
                    data: '&_key='+id,
                    root: _private.config.modulo + 'postDeleteUsuario',
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid('#'+diccionario.tabs.T4+'gridUsuariosx'); 
                                }
                            });
                        }
                    }
                });
            }
        });
    };
    
    this.publico.postPass = function(){
        window.history.pushState('data', "Titulo", "../../../");
        simpleAjax.send({
            flag: 1,
            element: '#btnEntrar',
            root: _private.config.modulo + 'postPass',
            form: '#fromchange_pass',
            data: '&_idUsuario='+$('#txtIDUser').val()+'&_pass='+simpleAjax.stringPost($('#txtNewClave').val())+'&_usuario='+$('#txtUser').val(),            
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_34
                    });
                    simpleScript.redirect('index');
                }
            }
        });
    };
    
    /*Envio de E-mail*/
    this.publico.postAcceso = function(id,usu,mail){
        simpleAjax.send({            
            element: '#'+diccionario.tabs.T4+'btnGrMensaje',
            form: '#'+diccionario.tabs.T4+'formViewMensaje',
            root: _private.config.modulo + 'postAcceso',
            data: '&_nombres='+usu+'&_idUsuario='+id+'&_mail='+mail,            
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_15
                    });                    
                }
                simpleScript.reloadGrid('#'+diccionario.tabs.T4+'gridUsuariosx');
                simpleScript.closeModal('#'+diccionario.tabs.T4+'formViewMensaje');
            }
        });
    };
    
    this.publico.postBaja = function(btn,id){
        
         simpleScript.notify.confirm({
            content: lang.mensajes.MSG_36,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    root: _private.config.modulo + 'postBaja',
                    data: '&_idUsuario='+id,
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_35,
                                callback: function(){
                                   simpleScript.reloadGrid('#'+diccionario.tabs.T4+'gridUsuariosx'); 
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
 var configurarUsuarios = new configurarUsuarios_();