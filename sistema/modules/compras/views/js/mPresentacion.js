/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 30-05-2016 18:05:19 
* Descripcion : mPresentacion.js
* ---------------------------------------
*/
var mPresentacion_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idMPresentacion = 0;
    _private.mapaSitio = "";
        
    _private.config = {
        modulo: "compras/mPresentacion/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : MPresentacion*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.CPRES,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                mPresentacion.getContenido();
            }
        });
    };
    
    /*contenido de tab: MPresentacion*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: "&_siteMap="+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.CPRES+"_CONTAINER").html(data);
                mPresentacion.getGridMPresentacion();
            }
        });
    };
    
    this.publico.getGridMPresentacion = function (){
        var oTable = $("#"+diccionario.tabs.CPRES+"gridMPresentacion").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "5%", sClass: "center", bSortable: false},
                {sTitle: lang.generico.ID, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.DESCRIPCION, sWidth: "50%"},
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "8%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridMPresentacion",
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.CPRES+"gridMPresentacion_filter").find("input").attr("placeholder",lang.busqueda.CPRES).css("width","250px");
                simpleScript.enterSearch("#"+diccionario.tabs.CPRES+"gridMPresentacion",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.CPRES,
                    typeElement: "button"
                });
            }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewMPresentacion = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewMPresentacion",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.CPRES+"formNewMPresentacion").modal("show");
            }
        });
    };
    
    this.publico.getFormEditMPresentacion = function(btn,id){
        _private.idMPresentacion = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditMPresentacion",
            data: "&_idMPresentacion="+_private.idMPresentacion,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.CPRES+"formEditMPresentacion").modal("show");
            }
        });
    };
    
    this.publico.postNewMPresentacion = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.CPRES+"btnGrMPresentacion",
            root: _private.config.modulo + "postNewMPresentacion",
            form: "#"+diccionario.tabs.CPRES+"formNewMPresentacion",
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){                            
                            simpleScript.reloadGrid("#"+diccionario.tabs.CPRES+"gridMPresentacion");
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
    
    this.publico.postEditMPresentacion = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.CPRES+"btnEdMPresentacion",
            root: _private.config.modulo + "postEditMPresentacion",
            form: "#"+diccionario.tabs.CPRES+"formEditMPresentacion",
            data: "&_idMPresentacion="+_private.idMPresentacion,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idMPresentacion = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.CPRES+"formEditMPresentacion");
                            simpleScript.reloadGrid("#"+diccionario.tabs.CPRES+"gridMPresentacion");
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
    
    this.publico.postDeleteMPresentacion = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    gifProcess: true,
                    data: "&_idMPresentacion="+id,
                    root: _private.config.modulo + "postDeleteMPresentacion",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.CPRES+"gridMPresentacion");
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
               data: '&_idMPresentacion='+id,
               fnCallback: function(data) {
                   if(!isNaN(data.result) && parseInt(data.result) === 1){
                       simpleScript.notify.ok({
                           content: lang.mensajes.MSG_24,
                           callback: function(){
                              simpleScript.reloadGrid("#"+diccionario.tabs.CPRES+"gridMPresentacion");
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
            data: '&_idMPresentacion='+id,
            fnCallback: function(data){
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_25,
                        callback: function(){
                           simpleScript.reloadGrid("#"+diccionario.tabs.CPRES+"gridMPresentacion");
                        }
                    });
                }
            }
        });
    };    
      
    return this.publico;
    
};
var mPresentacion = new mPresentacion_();