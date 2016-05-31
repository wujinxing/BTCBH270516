/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 30-05-2016 17:05:25 
* Descripcion : mLaboratorios.js
* ---------------------------------------
*/
var mLaboratorios_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idMLaboratorios = 0;
    _private.mapaSitio = "";
        
    _private.config = {
        modulo: "compras/mLaboratorios/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : MLaboratorios*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.RELAB,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                mLaboratorios.getContenido();
            }
        });
    };
    
    /*contenido de tab: MLaboratorios*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: "&_siteMap="+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.RELAB+"_CONTAINER").html(data);
                mLaboratorios.getGridMLaboratorios();
            }
        });
    };
    
    this.publico.getGridMLaboratorios = function (){
        var oTable = $("#"+diccionario.tabs.RELAB+"gridMLaboratorios").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "5%", sClass: "center"},
                {sTitle: lang.generico.DESCRIPCION, sWidth: "35%"},
                {sTitle: lang.generico.ALIAS, sWidth: "15%", sClass: "left"},
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "8%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridMLaboratorios",
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.RELAB+"gridMLaboratorios_filter").find("input").attr("placeholder",lang.busqueda.RELAB).css("width","250px");
                simpleScript.enterSearch("#"+diccionario.tabs.RELAB+"gridMLaboratorios",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.RELAB,
                    typeElement: "button"
                });
            }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewMLaboratorios = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewMLaboratorios",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.RELAB+"formNewMLaboratorios").modal("show");
            }
        });
    };
    
    this.publico.getFormEditMLaboratorios = function(btn,id){
        _private.idMLaboratorios = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditMLaboratorios",
            data: "&_idMLaboratorios="+_private.idMLaboratorios,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.RELAB+"formEditMLaboratorios").modal("show");
            }
        });
    };
    
    this.publico.postNewMLaboratorios = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.RELAB+"btnGrMLaboratorios",
            root: _private.config.modulo + "postNewMLaboratorios",
            form: "#"+diccionario.tabs.RELAB+"formNewMLaboratorios",
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){                            
                            simpleScript.reloadGrid("#"+diccionario.tabs.RELAB+"gridMLaboratorios");
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
    
    this.publico.postEditMLaboratorios = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.RELAB+"btnEdMLaboratorios",
            root: _private.config.modulo + "postEditMLaboratorios",
            form: "#"+diccionario.tabs.RELAB+"formEditMLaboratorios",
            data: "&_idMLaboratorios="+_private.idMLaboratorios,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idMLaboratorios = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.RELAB+"formEditMLaboratorios");
                            simpleScript.reloadGrid("#"+diccionario.tabs.RELAB+"gridMLaboratorios");
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
    
    this.publico.postDeleteMLaboratorios = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    gifProcess: true,
                    data: "&_idMLaboratorios="+id,
                    root: _private.config.modulo + "postDeleteMLaboratorios",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.RELAB+"gridMLaboratorios");
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
               data: '&_idMLaboratorios='+id,
               fnCallback: function(data) {
                   if(!isNaN(data.result) && parseInt(data.result) === 1){
                       simpleScript.notify.ok({
                           content: lang.mensajes.MSG_24,
                           callback: function(){
                              simpleScript.reloadGrid("#"+diccionario.tabs.RELAB+"gridMLaboratorios");
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
            data: '&_idMLaboratorios='+id,
            fnCallback: function(data){
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_25,
                        callback: function(){
                           simpleScript.reloadGrid("#"+diccionario.tabs.RELAB+"gridMLaboratorios");
                        }
                    });
                }
            }
        });
    };    
      
    return this.publico;
    
};
var mLaboratorios = new mLaboratorios_();