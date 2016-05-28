/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 09-12-2014 17:12:42 
* Descripcion : mensajesPlantilla.js
* ---------------------------------------
*/
var mensajesPlantilla_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idMensajes = 0;
    _private.mapaSitio = '';
    
    _private.config = {
        modulo: "configuracion/mensajesPlantilla/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : Mensajes*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.PMSJ,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                mensajesPlantilla.getContenido();
            }
        });
    };
    
    /*contenido de tab: Mensajes*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.PMSJ+"_CONTAINER").html(data);
                mensajesPlantilla.getGridMensajes();
            }
        });
    };
    
    this.publico.getGridMensajes = function (){
        
        var idIdioma = $("#"+diccionario.tabs.PMSJ+"lst_idioma").val();        
        var oTable = $("#"+diccionario.tabs.PMSJ+"gridMensajes").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "5%", sClass: "center", bSortable: false},
                {sTitle: lang.generico.ASUNTO, sWidth: "35%"},
                {sTitle: lang.generico.ALIAS, sWidth: "15%"},
                {sTitle: lang.generico.IDIOMA, sWidth: "15%"},
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "8%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[2, "asc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridMensajes",
            fnServerParams: function(aoData) {
                aoData.push({"name": "_idIdioma", "value": idIdioma});
            },
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.PMSJ+"gridMensajes_filter").find("input").attr("placeholder",lang.busqueda.PMSJ).css("width","250px");
                simpleScript.enterSearch("#"+diccionario.tabs.PMSJ+"gridMensajes",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.PMSJ,
                    typeElement: "button"
                });                
           }
        });
        setup_widgets_desktop();
    };
    
     this.publico.getFormNewMensajes = function(){        
        simpleScript.addTab({
            id : diccionario.tabs.PMSJ+'new',
            label: lang.msjPlantilla.NEW,
            fnCallback: function(){
                simpleAjax.send({
                    dataType: 'html',
                    root: _private.config.modulo+'getFormNewMensajes',
                    fnCallback: function(data){
                        $('#'+diccionario.tabs.PMSJ+'new_CONTAINER').html(data);
                    }
                });
            }
        });               
    };     
    
    this.publico.getFormEditMensajes = function(id){
        _private.idMensajes = id;
        
        simpleScript.addTab({
            id : diccionario.tabs.PMSJ+'edit',
            label: lang.msjPlantilla.EDIT ,
            fnCallback: function(){
                simpleAjax.send({
                    dataType: 'html',
                    root: _private.config.modulo+'getFormEditMensajes',
                    data: '&_idMensajes='+_private.idMensajes,
                    fnCallback: function(data){
                        $('#'+diccionario.tabs.PMSJ+'edit_CONTAINER').html(data);
                    }
                });
            }
        });                  
    };

    this.publico.getFormClonarMensajes = function(id){
        _private.idMensajes = id;
        
        simpleScript.addTab({
            id : diccionario.tabs.PMSJ+'clonar',
            label: lang.msjPlantilla.CLONAR ,
            fnCallback: function(){
                simpleAjax.send({
                    dataType: 'html',
                    root: _private.config.modulo+'getFormClonarMensajes',
                    data: '&_idMensajes='+_private.idMensajes,
                    fnCallback: function(data){
                        $('#'+diccionario.tabs.PMSJ+'clonar_CONTAINER').html(data);
                    }
                });
            }
        });                  
    };
    
    this.publico.postNewMensajes = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.PMSJ+"btnGrMensajes",
            root: _private.config.modulo + "postNewMensajes",
            form: "#"+diccionario.tabs.PMSJ+"formNewMensajes",
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            simpleScript.closeTab(diccionario.tabs.PMSJ+'new');  
                            simpleScript.reloadGrid("#"+diccionario.tabs.PMSJ+"gridMensajes");
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
    
    this.publico.postEditMensajes = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.PMSJ+"btnEdMensajes",
            root: _private.config.modulo + "postEditMensajes",
            form: "#"+diccionario.tabs.PMSJ+"formEditMensajes",
            data: "&_idMensajes="+_private.idMensajes,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idMensajes = 0;
                            simpleScript.closeTab(diccionario.tabs.PMSJ+'edit');  
                            simpleScript.reloadGrid("#"+diccionario.tabs.PMSJ+"gridMensajes");
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
    
    this.publico.postClonarMensajes = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.PMSJ+"btnClMensajes",
            root: _private.config.modulo + "postNewMensajes",
            form: "#"+diccionario.tabs.PMSJ+"formClonarMensajes",
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            simpleScript.closeTab(diccionario.tabs.PMSJ+'clonar');  
                            simpleScript.reloadGrid("#"+diccionario.tabs.PMSJ+"gridMensajes");
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
    
    this.publico.postDeleteMensajes = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({                    
                    element: btn,
                    gifProcess: true,
                    data: "&_idMensajes="+id,
                    root: _private.config.modulo + "postDeleteMensajes",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.PMSJ+"gridMensajes");
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
               data: '&_idMensajes='+id,
               fnCallback: function(data) {
                   if(!isNaN(data.result) && parseInt(data.result) === 1){
                       simpleScript.notify.ok({
                           content: lang.mensajes.MSG_24,
                           callback: function(){
                               simpleScript.reloadGrid('#'+diccionario.tabs.PMSJ+'gridMensajes');
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
            data: '&_idMensajes='+id,
            fnCallback: function(data){
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_25,
                        callback: function(){
                            simpleScript.reloadGrid('#'+diccionario.tabs.PMSJ+'gridMensajes');
                        }
                    });
                }
            }
        });
    };       
    
    return this.publico;
    
};
var mensajesPlantilla = new mensajesPlantilla_();