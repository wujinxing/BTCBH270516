/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 06-11-2014 16:11:31 
* Descripcion : vunidadMedida.js
* ---------------------------------------
*/
var vunidadMedida_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idVunidadMedida = 0;
    _private.mapaSitio = '';
    
    _private.callbackData = null;
    
    _private.config = {
        modulo: "ventas/vunidadMedida/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : VunidadMedida*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.VUNID,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                vunidadMedida.getContenido();
            }
        });
    };
    
    /*contenido de tab: VunidadMedida*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.VUNID+"_CONTAINER").html(data);
                vunidadMedida.getGridVunidadMedida();
            }
        });
    };
    
    this.publico.getGridVunidadMedida = function (){
        var oTable = $("#"+diccionario.tabs.VUNID+"gridVunidadMedida").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "5%", sClass: "center"},
                {sTitle: lang.generico.DESCRIPCION, sWidth: "45%"},
                {sTitle: lang.generico.SIGLA, sWidth: "10%"},
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center", bSortable: false},
                {sTitle: lang.generico.ACCIONES, sWidth: "8%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "350px",
            sAjaxSource: _private.config.modulo+"getGridVunidadMedida",
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.VUNID+"gridVunidadMedida_filter").find("input").attr("placeholder",lang.busqueda.VUNID).css("width","350px");
                simpleScript.enterSearch("#"+diccionario.tabs.VUNID+"gridVunidadMedida",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.VUNID,
                    typeElement: "button"
                });           
            }            
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewVunidadMedida = function(btn,callbackData){
         /*para cuando se llama formulario desde otro modulo*/
        _private.callbackData = callbackData;  
        
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewVunidadMedida",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.VUNID+"formNewVunidadMedida").modal("show");
            }
        });
    };
    
    this.publico.getFormEditVunidadMedida = function(btn,id){
        _private.idVunidadMedida = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditVunidadMedida",
            data: "&_idVunidadMedida="+_private.idVunidadMedida,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.VUNID+"formEditVunidadMedida").modal("show");
            }
        });
    };
    
    this.publico.getAddListUnidadMedida = function(){
        simpleAjax.send({
            root: _private.config.modulo + 'getAddListUnidadMedida ',
            fnCallback: function(data){
                simpleScript.closeModal('#'+diccionario.tabs.VUNID+'formNewVunidadMedida');                
                $(_private.callbackData).append('<option value="'+data.id_unidadmedida+'">'+data.nombre+'</option>');
                _private.callbackData = null;
            }
        });
        
    };
    
    this.publico.postNewVunidadMedida = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.VUNID+"btnGrVunidadMedida",
            root: _private.config.modulo + "postNewVunidadMedida",
            form: "#"+diccionario.tabs.VUNID+"formNewVunidadMedida",
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                             /*si se graba desde otro modulo*/                                  
                            if(_private.callbackData.length > 0){                                
                               vunidadMedida.getAddListUnidadMedida();                                
                            }
                            /*se verifica si existe tabb para recargar grid*/
                            if($('#'+diccionario.tabs.VUNID+'_CONTAINER').length > 0){
                               vunidadMedida.getGridVunidadMedida();
                            }                                                        
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: "Unidad Medida ya existe."
                    });
                }
            }
        });
    };    
    
    this.publico.postEditVunidadMedida = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.VUNID+"btnEdVunidadMedida",
            root: _private.config.modulo + "postEditVunidadMedida",
            form: "#"+diccionario.tabs.VUNID+"formEditVunidadMedida",
            data: "&_idVunidadMedida="+_private.idVunidadMedida,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idVunidadMedida = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.VUNID+"formEditVunidadMedida");
                            simpleScript.reloadGrid("#"+diccionario.tabs.VUNID+"gridVunidadMedida");
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: "Unidad Medida ya existe."
                    });
                }
            }
        });
    };
    
    this.publico.postDeleteVunidadMedida = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({                    
                    element: btn,
                    gifProcess: true,
                    data: "&_idVunidadMedida="+id,
                    root: _private.config.modulo + "postDeleteVunidadMedida",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.VUNID+"gridVunidadMedida");
                                }
                            });
                        }
                    }
                });
            }
        });
    };    
               
    this.publico.postDesactivar = function(btn,id){
           simpleAjax.send({
               element: btn,
               root: _private.config.modulo + 'postDesactivar',
               data: '&_idVunidadMedida='+id,
               clear: true,
               fnCallback: function(data) {
                   if(!isNaN(data.result) && parseInt(data.result) === 1){
                       simpleScript.notify.ok({
                           content: 'Unidad de Medida se desactivo correctamente',
                           callback: function(){
                               simpleScript.reloadGrid("#"+diccionario.tabs.VUNID+"gridVunidadMedida");
                           }
                       });
                   }
               }
           });
       };
    
    this.publico.postActivar = function(btn,id){
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postActivar',
            data: '&_idVunidadMedida='+id,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: 'Unidad de Medida se activo correctamente',
                        callback: function(){
                           simpleScript.reloadGrid("#"+diccionario.tabs.VUNID+"gridVunidadMedida");
                        }
                    });
                }
            }
        });
    };         
    
    return this.publico;
    
};
var vunidadMedida = new vunidadMedida_();