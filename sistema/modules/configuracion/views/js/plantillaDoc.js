/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 12-09-2014 17:09:13 
* Descripcion : plantillaDoc.js
* ---------------------------------------
*/
var plantillaDoc_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idPlantilla = 0;
    _private.mapaSitio = '';
    
    _private.config = {
        modulo: "configuracion/plantillaDoc/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : PlantillaDoc*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.PLTDC,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                plantillaDoc.getContenido();
            }
        });
    };
    
    /*contenido de tab: PlantillaDoc*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.PLTDC+"_CONTAINER").html(data);
                plantillaDoc.getGridPlantillaDoc();
            }
        });
    };
    
    this.publico.getGridPlantillaDoc = function (){
        var oTable = $("#"+diccionario.tabs.PLTDC+"gridPlantillaDoc").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "5%", sClass: "center", bSortable: false},
                {sTitle: lang.generico.NOMBRE, sWidth: "40%"},
                {sTitle: lang.generico.ALIAS, sWidth: "10%"},    
                {sTitle: lang.generico.FECHACREADO, sClass: "center", sWidth: "15%"},                
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center", bSortable: false},                
                {sTitle: lang.generico.ACCIONES, sWidth: "15%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridPlantillaDoc",
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.PLTDC+"gridPlantillaDoc_filter").find("input").attr("placeholder",lang.busqueda.RESOC).css("width","300px");
                simpleScript.enterSearch("#"+diccionario.tabs.PLTDC+"gridPlantillaDoc",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.PLTDC,
                    typeElement: "button"
                });                
            }            
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewPlantillaDoc = function(){        
        simpleScript.addTab({
            id : diccionario.tabs.PLTDC+'new',
            label: lang.plantillaDoc.NEW,
            fnCallback: function(){
                simpleAjax.send({
                    dataType: 'html',
                    root: _private.config.modulo+'getFormNewPlantillaDoc',
                    fnCallback: function(data){
                        $('#'+diccionario.tabs.PLTDC+'new_CONTAINER').html(data);
                    }
                });
            }
        });               
    };     
    
    this.publico.getFormEditPlantillaDoc = function(id){
        _private.idPlantilla = id;
        
        simpleScript.addTab({
            id : diccionario.tabs.PLTDC+'edit',
            label: lang.plantillaDoc.EDIT,
            fnCallback: function(){
                simpleAjax.send({
                    dataType: 'html',
                    root: _private.config.modulo+'getFormEditPlantillaDoc',
                     data: '&_idPlantilla='+_private.idPlantilla,
                    fnCallback: function(data){
                        $('#'+diccionario.tabs.PLTDC+'edit_CONTAINER').html(data);
                    }
                });
            }
        });                  
    };
    
    this.publico.getClonar = function(idd){
        //cerrar tab nuevo
        simpleScript.closeTab(diccionario.tabs.PLTDC+'new');
        
        _private.idPlantilla = idd;
        
        simpleScript.addTab({
            id : diccionario.tabs.PLTDC+'clon',
            label: lang.plantillaDoc.CLONAR,
            fnCallback: function(){
                 simpleAjax.send({
                    dataType: 'html',
                    root: _private.config.modulo+'getFormClonarPlantillaDoc',
                    data: '&_idPlantilla='+_private.idPlantilla,
                    fnCallback: function(data){
                        $('#'+diccionario.tabs.PLTDC+'clon_CONTAINER').html(data);
                    }
                });
            }
        });
    };
        
    this.publico.postNewPlantillaDoc = function(){
                                
        simpleAjax.send({
            flag: 1,
            element: "#"+diccionario.tabs.PLTDC+"btnGrPlantillaDoc",
            root: _private.config.modulo + "postNewPlantillaDoc",
            form: "#"+diccionario.tabs.PLTDC+"formNewPlantillaDoc",            
            fnCallback: function(data){
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            simpleScript.closeTab(diccionario.tabs.PLTDC+'new');  
                            plantillaDoc.getGridPlantillaDoc();                                                        
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
    
    this.publico.postEditPlantillaDoc = function(){
        simpleAjax.send({
            flag: 2,
            element: "#"+diccionario.tabs.PLTDC+"btnEdPlantillaDoc",
            root: _private.config.modulo + "postEditPlantillaDoc",
            form: "#"+diccionario.tabs.PLTDC+"formEditPlantillaDoc",
            data: "&_idPlantilla="+_private.idPlantilla,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            _private.idPlantilla = 0;
                            simpleScript.closeTab(diccionario.tabs.PLTDC+'edit');  
                            simpleScript.reloadGrid("#"+diccionario.tabs.PLTDC+"gridPlantillaDoc");
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
    
     this.publico.postClonPlantillaDoc = function(){
                                
        simpleAjax.send({
            flag: 1,
            element: "#"+diccionario.tabs.PLTDC+"btnCdPlantillaDoc",
            root: _private.config.modulo + "postNewPlantillaDoc",
            form: "#"+diccionario.tabs.PLTDC+"formClonPlantillaDoc",            
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            simpleScript.closeTab(diccionario.tabs.PLTDC+'clon');  
                            plantillaDoc.getGridPlantillaDoc();                                                        
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
    
    this.publico.postDeletePlantillDoc = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({  
                    flag: 3,
                    element: btn,
                    gifProcess: true,
                    data: "&_idPlantilla="+id,
                    root: _private.config.modulo + "postDeletePlantillaDoc",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.PLTDC+"gridPlantillaDoc");
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
               data: '&_idPlantilla='+id,
               fnCallback: function(data) {
                   if(!isNaN(data.result) && parseInt(data.result) === 1){
                       simpleScript.notify.ok({
                           content: lang.plantillaDoc.DESCT,
                           callback: function(){
                               simpleScript.reloadGrid('#'+diccionario.tabs.PLTDC+'gridPlantillaDoc');
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
            data: '&_idPlantilla='+id,
            fnCallback: function(data){
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.plantillaDoc.ACT,
                        callback: function(){
                            simpleScript.reloadGrid('#'+diccionario.tabs.PLTDC+'gridPlantillaDoc');
                        }
                    });
                }
            }
        });
    };       
        
    return this.publico;
    
};
var plantillaDoc = new plantillaDoc_();