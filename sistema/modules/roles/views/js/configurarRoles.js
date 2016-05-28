var configurarRoles_ = function(){
    
    var _private = {};
    _private.mapaSitio = '';
    
    _private.config = {
        modulo: 'roles/configurarRoles/'
    };
    
    _private.idRol = 0;
    
    this.publico = {};
    
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.T1,
            label: $(element).attr('title'),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                configurarRoles.getContenido();
            }
        });
    };
    
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: 'html',
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $('#'+diccionario.tabs.T1+'_CONTAINER').html(data);
                configurarRoles.getGridRoles();
            }
        });
    };
    
    this.publico.getGridRoles = function (){
        var oTable = $('#'+diccionario.tabs.T1+'gridRoles').dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: "Código", sClass: "center",sWidth: "10%"},
                {sTitle: "Rol", sWidth: "50%"},
                {sTitle: "Estado", sWidth: "10%", sClass: "center", bSortable: false},
                {sTitle: "Acciones", sWidth: "15%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[0, 'asc']],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+'getRoles',
            fnDrawCallback: function() {
                $('#'+diccionario.tabs.T1+'gridRoles_filter').find('input').attr('placeholder',lang.accion.BUSCAR).css("width","250px");;
                simpleScript.enterSearch("#"+diccionario.tabs.T1+"gridRoles",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: '#widget_'+diccionario.tabs.T1+'roles',
                    typeElement: 'button'
                });                
           }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getNuevoRol = function(){
        simpleAjax.send({
            element: '#CRDCRbtnNew',
            dataType: 'html',
            root: _private.config.modulo + 'getNuevoRol',
            fnCallback: function(data){
                $('#cont-modal').append(data);  /*los formularios con append*/
                $('#CRDCRformNuevoRol').modal('show');
                configurarRolesScript.validateRol({
                    form: '#CRDCRformNuevoRol', 
                    evento: 'configurarRoles.postRol()'
                });
            }
        });
    };
    
    /*extraer rol para editar*/
    this.publico.getRol = function(){
        _private.idRol = simpleScript.getParam(arguments[0]);
       
        simpleAjax.send({
            dataType: 'html',
            gifProcess: true,
            data: '&_key='+_private.idRol,
            root: _private.config.modulo + 'getEditRol',
            fnCallback: function(data){
                $('#cont-modal').append(data);
                $('#CRDCRformEditRol').modal('show');
                configurarRolesScript.validateRol({
                    form: '#CRDCRformEditRol', 
                    evento: 'configurarRoles.postEditRol()'
                });
            }
        });
    };
    
    /*los accesos del rol*/
    this.publico.getAccesos = function(){
        _private.idRol = simpleScript.getParam(arguments[0]);
        var rol = simpleScript.getParam(arguments[1]);
        
        simpleAjax.send({
            dataType: 'html',
            gifProcess: true,
            data: '&_key='+_private.idRol,
            root: _private.config.modulo + 'getAccesos',
            fnCallback: function(data){
                $('#cont-modal').append(data);
                $('#CRDCRformAccesos').modal('show');
                $('#cont-rol').html(rol);
            }
        });
    };
    
    /*los acciones del rol opcion*/
    this.publico.getAccionesRolOpcion = function(){
        var contAccion  = simpleScript.getParam(arguments[0]);
        var idRolOpcion = simpleScript.getParam(arguments[1]);
        var tt = $(simpleScript.getParam(arguments[2])).data("tt"); 

        simpleAjax.send({
            dataType: 'html',
            element: '#btn_'+contAccion,
            abort: false,
            data: '&_rolOpcion='+idRolOpcion+'&_tt='+tt,
            root: _private.config.modulo + 'getOpcionRolAxions',
            fnCallback: function(data){
                $('.accionesOpcion-cont').fadeOut();
                $('#cont-acciones'+contAccion).html(data);
                $('#cont-acciones'+contAccion).fadeIn();
            }
        });
    };
    
    this.publico.postRol = function(){
        simpleAjax.send({
            flag: 1,
            element: '#CRDCRbtnGrabaRol',
            root: _private.config.modulo + 'postRol',
            form: '#CRDCRformNuevoRol',
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            configurarRoles.getGridRoles();
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.mensajes.MSG_4
                    });
                }
            }
        });
    };
    
    this.publico.postEditRol = function(){
        simpleAjax.send({
            flag: 3,
            element: '#CRDCRbtnEditaRol',
            root: _private.config.modulo + 'postRol',
            form: '#CRDCRformEditRol',
            data: '&_key='+_private.idRol,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            configurarRoles.getGridRoles();
                            simpleScript.reloadGrid('#gridRoles');
                            simpleScript.closeModal('#CRDCRformEditRol');
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.mensajes.MSG_4
                    });
                }
            }
        });
    };
    
    /*eliminar rol*/
    this.publico.postDeleteRol = function(){
        _private.idRol = simpleScript.getParam(arguments[0]);
        
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({
                    flag: 2,
                    gifProcess: true,
                    data: '&_key='+_private.idRol,
                    root: _private.config.modulo + 'postDeleteRol',
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    configurarRoles.getGridRoles();
                                }
                            });
                        }
                    }
                });
            }
        });
    };
    
    /*agregar opcion a rol*/
    this.publico.postOpcion = function(){
        var radio    = simpleScript.getParam(arguments[0]);
        var flag     = simpleScript.getParam(arguments[1]);
        var idRol    = simpleScript.getParam(arguments[2]);
        var idOpcion = simpleScript.getParam(arguments[3]);
        
        simpleAjax.send({
            flag: flag,
            gifProcess: true,
            root: _private.config.modulo + 'postOpcion',
            data: '&_key='+idRol+'&_opcion='+idOpcion,
            fnCallback: function(data) {
                var _btn = '#btn_'+simpleAjax.stringGet(idRol)+simpleAjax.stringGet(idOpcion);
                
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    /*se activa boton acciones y se agrega evento*/
//                    alert(simpleAjax.stringGet(idRol)+simpleAjax.stringGet(idOpcion)+'---'+idOpcion)                                                            
                    var _idMenu = simpleAjax.stringGet(idRol)+simpleAjax.stringGet(idOpcion);
                    var _idRol = simpleAjax.stringPost(data.id_rolopciones);
                    $(_btn).attr('disabled',false);
                    
                    simpleScript.setEvent.click({
                        element: _btn,
                        event: 'configurarRoles.getAccionesRolOpcion(\''+_idMenu+'\',\''+_idRol+'\',\''+_btn+'\');'
                    });
                    /*se cambia ebento para que elimine*/
                    $(radio).attr("onclick","");
                    simpleScript.setEvent.click({
                        element: radio,
                        event: "configurarRoles.postOpcion(this,5,'"+idRol+"','"+idOpcion+"')"
                    });
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.mensajes.MSG_4
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
//                    alert(simpleAjax.stringGet(idRol)+simpleAjax.stringGet(idOpcion)+'---'+idOpcion)
                    /*se desactiva boton acciones y quita evento*/
                    $(_btn).attr('disabled',true).removeAttr('onclick');
                    $(_btn).off('click');
                    /*se cambia evento para que agregue*/
                    $(radio).attr("onclick","");
                    simpleScript.setEvent.click({
                        element: radio,
                        event: "configurarRoles.postOpcion(this,4,'"+idRol+"','"+idOpcion+"')"
                    });
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_6
                    });
                    /*se limpia contenedor de acciones*/
                    $('#cont-acciones'+simpleAjax.stringGet(idRol)+simpleAjax.stringGet(idOpcion)+'').html('');
                }
            }
        });
    };
    
    this.publico.postAccionOpcionRol = function(){
        var flag     = simpleScript.getParam(arguments[0]);
        var rolOpcion= simpleScript.getParam(arguments[1]);
        var accion   = simpleScript.getParam(arguments[2]);
        
        simpleAjax.send({
            flag: flag,
            gifProcess: true,
            abort: false,
            root: _private.config.modulo + 'postAccionOpcionRol',
            data: '&_accion='+accion+'&_opcion='+rolOpcion,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_6
                    });
                }
            }
        });
        
    };
    
    this.publico.postDuplicarRol = function(btn,id){
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postDuplicarRol',
            data: '&_key='+id,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.accion.PUBROL,
                        callback: function(){
                            configurarRoles.getGridRoles();
                        }
                    });
                }
            }
        });
    };
    
    return this.publico;
    
};
 var configurarRoles = new configurarRoles_();