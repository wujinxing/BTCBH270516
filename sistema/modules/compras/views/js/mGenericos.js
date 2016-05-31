/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 31-05-2016 00:05:34 
* Descripcion : mGenericos.js
* ---------------------------------------
*/
var mGenericos_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idMGenericos = 0;
    _private.mapaSitio = "";
        
    _private.config = {
        modulo: "compras/mGenericos/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : MGenericos*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.GENER,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                mGenericos.getContenido();
            }
        });
    };
    
    /*contenido de tab: MGenericos*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: "&_siteMap="+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.GENER+"_CONTAINER").html(data);
                mGenericos.getGridMGenericos();
            }
        });
    };
    
    this.publico.getGridMGenericos = function (){
        var oTable = $("#"+diccionario.tabs.GENER+"gridMGenericos").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "5%", sClass: "center"},
                {sTitle: lang.generico.DESCRIPCION, sWidth: "50%"},
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "8%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridMGenericos",
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.GENER+"gridMGenericos_filter").find("input").attr("placeholder",lang.busqueda.GENER).css("width","250px");
                simpleScript.enterSearch("#"+diccionario.tabs.GENER+"gridMGenericos",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.GENER,
                    typeElement: "button"
                });
            }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewMGenericos = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewMGenericos",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.GENER+"formNewMGenericos").modal("show");
            }
        });
    };
    
    this.publico.getFormEditMGenericos = function(btn,id){
        _private.idMGenericos = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditMGenericos",
            data: "&_idMGenericos="+_private.idMGenericos,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.GENER+"formEditMGenericos").modal("show");
            }
        });
    };
    
    this.publico.postNewMGenericos = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.GENER+"btnGrMGenericos",
            root: _private.config.modulo + "postNewMGenericos",
            form: "#"+diccionario.tabs.GENER+"formNewMGenericos",
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){                            
                            simpleScript.reloadGrid("#"+diccionario.tabs.GENER+"gridMGenericos");
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
    
    this.publico.postEditMGenericos = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.GENER+"btnEdMGenericos",
            root: _private.config.modulo + "postEditMGenericos",
            form: "#"+diccionario.tabs.GENER+"formEditMGenericos",
            data: "&_idMGenericos="+_private.idMGenericos,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idMGenericos = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.GENER+"formEditMGenericos");
                            simpleScript.reloadGrid("#"+diccionario.tabs.GENER+"gridMGenericos");
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
    
    this.publico.postDeleteMGenericos = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    gifProcess: true,
                    data: "&_idMGenericos="+id,
                    root: _private.config.modulo + "postDeleteMGenericos",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.GENER+"gridMGenericos");
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
               data: '&_idMGenericos='+id,
               fnCallback: function(data) {
                   if(!isNaN(data.result) && parseInt(data.result) === 1){
                       simpleScript.notify.ok({
                           content: lang.mensajes.MSG_24,
                           callback: function(){
                              simpleScript.reloadGrid("#"+diccionario.tabs.GENER+"gridMGenericos");
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
            data: '&_idMGenericos='+id,
            fnCallback: function(data){
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_25,
                        callback: function(){
                           simpleScript.reloadGrid("#"+diccionario.tabs.GENER+"gridMGenericos");
                        }
                    });
                }
            }
        });
    };   
      
    return this.publico;
    
};
var mGenericos = new mGenericos_();