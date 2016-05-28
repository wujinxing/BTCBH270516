/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 16-09-2014 03:09:14 
* Descripcion : persona.js
* ---------------------------------------
*/
var persona_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idPersona = 0;
    _private.mapaSitio = '';
    
    _private.config = {
        modulo: "usuarios/persona/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : Persona*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.REPER,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                persona.getContenido();
            }
        });
    };
    
    /*contenido de tab: Persona*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.REPER+"_CONTAINER").html(data);
                persona.getGridPersona();
            }
        });
    };
    
    this.publico.getGridPersona = function (){
     var oTable = $('#'+diccionario.tabs.REPER+'gridPersona').dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "5%", sClass: "center"},      
                {sTitle: lang.generico.FOTO, sWidth: "5%", bSortable: false},
                {sTitle: lang.generico.NOMBREAPELLIDO, sWidth: "30%"},
                {sTitle: lang.generico.EMAIL, sWidth: "20%"},
                {sTitle: lang.generico.TELEFONO, sWidth: "10%"},  
                {sTitle: lang.generico.CIUDAD , sWidth: "25%"}, 
                {sTitle: lang.generico.ESTADO, sWidth: "9%",  sClass: "center", bSortable: false},            
                {sTitle: lang.generico.ACCIONES, sWidth: "15%", sClass: "center", bSortable: false} 
            ],
            aaSorting: [[2, 'asc']],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+'getGridPersona',
            fnDrawCallback: function() {
                $('#'+diccionario.tabs.REPER+'gridPersona_filter').find('input').attr('placeholder',lang.busqueda.REPER).css('width','300px');
                simpleScript.enterSearch("#"+diccionario.tabs.REPER+'gridPersona',oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: '#widget_'+diccionario.tabs.REPER, //widget del datagrid
                    typeElement: 'button, #'+diccionario.tabs.REPER+'chk_all'
                });               
           }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewPersona = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewPersona",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.REPER+"formNewPersona").modal("show");
            }
        });
    };
    
    this.publico.getDatosPersonales = function(id, tt){
        simpleAjax.send({
            gifProcess: true,
            dataType: "html",
            root: _private.config.modulo + "getFormDatosPersonales",
            data: '&_idPersona='+id+'&_tt='+tt,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.REPER+"formDatosPersonales").modal("show");
            }
        });
    };
    
    //Ventana Editar Persona
    this.publico.getFormEditPersona = function(btn,id){
        _private.idPersona = id;
       
        simpleAjax.send({
            element: btn,
            dataType: 'html',
            gifProcess: true,
            data: '&_idPersona='+_private.idPersona,
            root: _private.config.modulo + 'getFormEditPersona',
            fnCallback: function(data){
                $('#cont-modal').append(data);
                $('#'+diccionario.tabs.REPER+'formEditPersona').modal('show');
            }
        });
    };        
    
    this.publico.postNewPersona = function(){
        simpleAjax.send({
            flag: 1,
            element: '#'+diccionario.tabs.REPER+'btnGpersona',
            root: _private.config.modulo + 'postNewPersona',
            form: '#'+diccionario.tabs.REPER+'formNewPersona',
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                           persona.getGridPersona(); 
                           $("#"+diccionario.tabs.REPER+"lst_pais").select2("val", "");
                           $("#"+diccionario.tabs.REPER+"lst_departamento").select2("val", "");
                           $("#"+diccionario.tabs.REPER+"lst_provincia").select2("val", "");
                           $("#"+diccionario.tabs.REPER+"lst_ubigeo").select2("val", "");
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.mensajes.MSG_23
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.error({
                        content:  lang.mensajes.MSG_22
                    });
                }
            }
        });
    };
    
 this.publico.postEditarPersona = function(){
        simpleAjax.send({
            flag: 2,
            element: '#'+diccionario.tabs.REPER+'btnEpersona',
            root: _private.config.modulo + 'postEditPersona',
            form: '#'+diccionario.tabs.REPER+'formEditPersona',
            data: '&_idPersona='+_private.idPersona,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            _private.idPersona = 0;
                            simpleScript.reloadGrid('#'+diccionario.tabs.REPER+'gridPersona'); 
                            simpleScript.closeModal('#'+diccionario.tabs.REPER+'formEditPersona');
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.mensajes.MSG_23
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.error({
                        content: lang.mensajes.MSG_22
                    });
                }
            }
        });
    };    
    
    this.publico.postDeletePersonaAll = function(btn){
        simpleScript.validaCheckBox({
            id: "#"+diccionario.tabs.REPER+"gridPersona",
            msn: lang.mensajes.MSG_9,
            fnCallback: function(){
                simpleScript.notify.confirm({
                    content: lang.mensajes.MSG_7,
                    callbackSI: function(){
                        simpleAjax.send({
                            element: btn,
                            form: "#"+diccionario.tabs.REPER+"formGridPersona",
                            root: _private.config.modulo + "postDeletePersonaAll",
                            fnCallback: function(data) {
                                if(!isNaN(data.result) && parseInt(data.result) === 1){
                                    simpleScript.notify.ok({
                                        content: lang.mensajes.MSG_8,
                                        callback: function(){
                                            persona.getGridPersona();
                                        }
                                    });
                                }
                            }
                        });
                    }
                });
            }
        });
    };
    
    this.publico.postDesactivarPersona = function(btn,id){
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postDesactivar',
            data: '&_idPersona='+id,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_24,
                        callback: function(){
                           simpleScript.reloadGrid('#'+diccionario.tabs.REPER+'gridPersona'); 
                        }
                    });
                }
            }
        });
    };
    
    this.publico.postActivarPersona = function(btn,id){
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postActivar',
            data: '&_idPersona='+id,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_25,
                        callback: function(){
                            simpleScript.reloadGrid('#'+diccionario.tabs.REPER+'gridPersona'); 
                        }
                    });
                }
            }
        });
    };
    
    return this.publico;
    
};
var persona = new persona_();