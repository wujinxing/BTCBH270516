/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 31-05-2016 00:05:31 
* Descripcion : mFamilia.js
* ---------------------------------------
*/
var mFamilia_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idMFamilia = 0;
    _private.mapaSitio = "";
        
    _private.config = {
        modulo: "compras/mFamilia/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : MFamilia*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.FAM,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                mFamilia.getContenido();
            }
        });
    };
    
    /*contenido de tab: MFamilia*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: "&_siteMap="+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.FAM+"_CONTAINER").html(data);
                mFamilia.getGridMFamilia();
            }
        });
    };
    
    this.publico.getGridMFamilia = function (){
        var oTable = $("#"+diccionario.tabs.FAM+"gridMFamilia").dataTable({
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
            sAjaxSource: _private.config.modulo+"getGridMFamilia",
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.FAM+"gridMFamilia_filter").find("input").attr("placeholder",lang.busqueda.FAM).css("width","250px");
                simpleScript.enterSearch("#"+diccionario.tabs.FAM+"gridMFamilia",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.FAM,
                    typeElement: "button"
                });
            }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewMFamilia = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewMFamilia",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.FAM+"formNewMFamilia").modal("show");
            }
        });
    };
    
    this.publico.getFormEditMFamilia = function(btn,id){
        _private.idMFamilia = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditMFamilia",
            data: "&_idMFamilia="+_private.idMFamilia,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.FAM+"formEditMFamilia").modal("show");
            }
        });
    };
    
    this.publico.postNewMFamilia = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.FAM+"btnGrMFamilia",
            root: _private.config.modulo + "postNewMFamilia",
            form: "#"+diccionario.tabs.FAM+"formNewMFamilia",
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){                            
                            simpleScript.reloadGrid("#"+diccionario.tabs.FAM+"gridMFamilia");
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
    
    this.publico.postEditMFamilia = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.FAM+"btnEdMFamilia",
            root: _private.config.modulo + "postEditMFamilia",
            form: "#"+diccionario.tabs.FAM+"formEditMFamilia",
            data: "&_idMFamilia="+_private.idMFamilia,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idMFamilia = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.FAM+"formEditMFamilia");
                            simpleScript.reloadGrid("#"+diccionario.tabs.FAM+"gridMFamilia");
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
    
    this.publico.postDeleteMFamilia = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    gifProcess: true,
                    data: "&_idMFamilia="+id,
                    root: _private.config.modulo + "postDeleteMFamilia",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.FAM+"gridMFamilia");
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
               data: '&_idMFamilia='+id,
               fnCallback: function(data) {
                   if(!isNaN(data.result) && parseInt(data.result) === 1){
                       simpleScript.notify.ok({
                           content: lang.mensajes.MSG_24,
                           callback: function(){
                              simpleScript.reloadGrid("#"+diccionario.tabs.FAM+"gridMFamilia");
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
            data: '&_idMFamilia='+id,
            fnCallback: function(data){
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_25,
                        callback: function(){
                           simpleScript.reloadGrid("#"+diccionario.tabs.FAM+"gridMFamilia");
                        }
                    });
                }
            }
        });
    };    
      
    return this.publico;
    
};
var mFamilia = new mFamilia_();