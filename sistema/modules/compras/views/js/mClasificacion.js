/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 31-05-2016 00:05:51 
* Descripcion : mClasificacion.js
* ---------------------------------------
*/
var mClasificacion_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idMClasificacion = 0;
    _private.mapaSitio = "";
        
    _private.config = {
        modulo: "compras/mClasificacion/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : MClasificacion*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.CLASF,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                mClasificacion.getContenido();
            }
        });
    };
    
    /*contenido de tab: MClasificacion*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: "&_siteMap="+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.CLASF+"_CONTAINER").html(data);
                mClasificacion.getGridMClasificacion();
            }
        });
    };
    
    this.publico.getGridMClasificacion = function (){
        var oTable = $("#"+diccionario.tabs.CLASF+"gridMClasificacion").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "5%", sClass: "center"},
                {sTitle: lang.generico.DESCRIPCION, sWidth: "40%"},
                {sTitle: lang.generico.DESCRIPCIONCORTA, sWidth: "20%"},
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "8%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridMClasificacion",
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.CLASF+"gridMClasificacion_filter").find("input").attr("placeholder",lang.busqueda.CLASF).css("width","250px");
                simpleScript.enterSearch("#"+diccionario.tabs.CLASF+"gridMClasificacion",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.CLASF,
                    typeElement: "button"
                });
            }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewMClasificacion = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewMClasificacion",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.CLASF+"formNewMClasificacion").modal("show");
            }
        });
    };
    
    this.publico.getFormEditMClasificacion = function(btn,id){
        _private.idMClasificacion = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditMClasificacion",
            data: "&_idMClasificacion="+_private.idMClasificacion,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.CLASF+"formEditMClasificacion").modal("show");
            }
        });
    };
    
    this.publico.postNewMClasificacion = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.CLASF+"btnGrMClasificacion",
            root: _private.config.modulo + "postNewMClasificacion",
            form: "#"+diccionario.tabs.CLASF+"formNewMClasificacion",
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){                            
                            simpleScript.reloadGrid("#"+diccionario.tabs.CLASF+"gridMClasificacion");
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
    
    this.publico.postEditMClasificacion = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.CLASF+"btnEdMClasificacion",
            root: _private.config.modulo + "postEditMClasificacion",
            form: "#"+diccionario.tabs.CLASF+"formEditMClasificacion",
            data: "&_idMClasificacion="+_private.idMClasificacion,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idMClasificacion = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.CLASF+"formEditMClasificacion");
                            simpleScript.reloadGrid("#"+diccionario.tabs.CLASF+"gridMClasificacion");
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
    
    this.publico.postDeleteMClasificacion = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    gifProcess: true,
                    data: "&_idMClasificacion="+id,
                    root: _private.config.modulo + "postDeleteMClasificacion",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.CLASF+"gridMClasificacion");
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
               data: '&_idMClasificacion='+id,
               fnCallback: function(data) {
                   if(!isNaN(data.result) && parseInt(data.result) === 1){
                       simpleScript.notify.ok({
                           content: lang.mensajes.MSG_24,
                           callback: function(){
                              simpleScript.reloadGrid("#"+diccionario.tabs.CLASF+"gridMClasificacion");
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
            data: '&_idMClasificacion='+id,
            fnCallback: function(data){
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_25,
                        callback: function(){
                           simpleScript.reloadGrid("#"+diccionario.tabs.CLASF+"gridMClasificacion");
                        }
                    });
                }
            }
        });
    };      
      
    return this.publico;
    
};
var mClasificacion = new mClasificacion_();