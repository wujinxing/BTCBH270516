/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 20-04-2016 16:04:52 
* Descripcion : metodoPago.js
* ---------------------------------------
*/
var metodoPago_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idMetodoPago = 0;
    _private.mapaSitio = "";
        
    _private.config = {
        modulo: "Configuracion/metodoPago/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : MetodoPago*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.MEPAG,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                metodoPago.getContenido();
            }
        });
    };
    
    /*contenido de tab: MetodoPago*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: "&_siteMap="+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.MEPAG+"_CONTAINER").html(data);
                metodoPago.getGridMetodoPago();
            }
        });
    };
    
    this.publico.getGridMetodoPago = function (){
        var oTable = $("#"+diccionario.tabs.MEPAG+"gridMetodoPago").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "5%", sClass: "center", bSortable: false},                
                {sTitle: lang.generico.DESCRIPCION, sWidth: "30%"},
                {sTitle: lang.generico.ICONO, sWidth: "20%"},
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center", bSortable: false},
                {sTitle: lang.generico.ACCIONES, sWidth: "8%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridMetodoPago",
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.MEPAG+"gridMetodoPago_filter").find("input").attr("placeholder",lang.busqueda.MEPAG).css("width","250px");
                simpleScript.enterSearch("#"+diccionario.tabs.MEPAG+"gridMetodoPago",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.MEPAG,
                    typeElement: "button"
                });
            }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewMetodoPago = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewMetodoPago",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.MEPAG+"formNewMetodoPago").modal("show");
            }
        });
    };
    
    this.publico.getFormEditMetodoPago = function(btn,id){
        _private.idMetodoPago = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditMetodoPago",
            data: "&_idMetodoPago="+_private.idMetodoPago,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.MEPAG+"formEditMetodoPago").modal("show");
            }
        });
    };
    
    this.publico.postNewMetodoPago = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.MEPAG+"btnGrMetodoPago",
            root: _private.config.modulo + "postNewMetodoPago",
            form: "#"+diccionario.tabs.MEPAG+"formNewMetodoPago",
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){                            
                            simpleScript.reloadGrid("#"+diccionario.tabs.MEPAG+"gridMetodoPago");
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
    
    this.publico.postEditMetodoPago = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.MEPAG+"btnEdMetodoPago",
            root: _private.config.modulo + "postEditMetodoPago",
            form: "#"+diccionario.tabs.MEPAG+"formEditMetodoPago",
            data: "&_idMetodoPago="+_private.idMetodoPago,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idMetodoPago = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.MEPAG+"formEditMetodoPago");
                            simpleScript.reloadGrid("#"+diccionario.tabs.MEPAG+"gridMetodoPago");
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
    
    this.publico.postDeleteMetodoPago = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    gifProcess: true,
                    data: "&_idMetodoPago="+id,
                    root: _private.config.modulo + "postDeleteMetodoPago",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.MEPAG+"gridMetodoPago");
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
               data: '&_idMetodoPago='+id,
               fnCallback: function(data) {
                   if(!isNaN(data.result) && parseInt(data.result) === 1){
                       simpleScript.notify.ok({
                           content: lang.mensajes.MSG_24,
                           callback: function(){
                               simpleScript.reloadGrid("#"+diccionario.tabs.MEPAG+"gridMetodoPago");
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
            data: '&_idMetodoPago='+id,
            fnCallback: function(data){
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_25,
                        callback: function(){
                            simpleScript.reloadGrid("#"+diccionario.tabs.MEPAG+"gridMetodoPago");
                        }
                    });
                }
            }
        });
    };       
        
      
    return this.publico;
    
};
var metodoPago = new metodoPago_();