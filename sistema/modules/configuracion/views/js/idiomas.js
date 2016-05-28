/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 23-12-2014 14:12:17 
* Descripcion : idiomas.js
* ---------------------------------------
*/
var idiomas_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idIdiomas = 0;
    _private.mapaSitio = '';
    _private.sigla = '';
    
    _private.config = {
        modulo: "configuracion/idiomas/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : Idiomas*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.IDIOM,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                idiomas.getContenido();
            }
        });
    };
    
    /*contenido de tab: Idiomas*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.IDIOM+"_CONTAINER").html(data);
                idiomas.getGridIdiomas();
            }
        });
    };
    
    this.publico.getGridIdiomas = function (){
        var oTable = $("#"+diccionario.tabs.IDIOM+"gridIdiomas").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "5%", sClass: "center", bSortable: false},
                {sTitle: lang.generico.NOMBRE, sWidth: "25%"},
                {sTitle: lang.generico.ALIAS, sWidth: "15%", sClass: "center"},
                {sTitle: lang.generico.ICONO, sWidth: "8%", sClass: "center", bSortable: false},
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "8%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[2, "asc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridIdiomas",
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.IDIOM+"gridIdiomas_filter").find("input").attr("placeholder",lang.busqueda.IDIOM).css("width","250px");
                simpleScript.enterSearch("#"+diccionario.tabs.IDIOM+"gridIdiomas",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.IDIOM,
                    typeElement: "button"
                });              
           }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewIdiomas = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewIdiomas",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.IDIOM+"formNewIdiomas").modal("show");
            }
        });
    };
    
    this.publico.getFormEditIdiomas = function(btn,id){
        _private.idIdiomas = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditIdiomas",
            data: "&_idIdiomas="+_private.idIdiomas,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.IDIOM+"formEditIdiomas").modal("show");
            }
        });
    };
    
    this.publico.getFormFileIdiomas = function(btn,id, ed){
        _private.idIdiomas = id;
        _private.sigla = ed;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormFileIdiomas",
            data: "&_idIdiomas="+_private.idIdiomas+"&_sigla="+_private.sigla,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.IDIOM+"formFileIdiomas").modal("show");
            }
        });
    };    
    
    this.publico.postNewIdiomas = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.IDIOM+"btnGrIdiomas",
            root: _private.config.modulo + "postNewIdiomas",
            form: "#"+diccionario.tabs.IDIOM+"formNewIdiomas",
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){                            
                            simpleScript.reloadGrid("#"+diccionario.tabs.IDIOM+"gridIdiomas");
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content:  lang.mensajes.MSG_4
                    });
                }
            }
        });
    };
    
    this.publico.postEditIdiomas = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.IDIOM+"btnEdIdiomas",
            root: _private.config.modulo + "postEditIdiomas",
            form: "#"+diccionario.tabs.IDIOM+"formEditIdiomas",
            data: "&_idIdiomas="+_private.idIdiomas,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idIdiomas = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.IDIOM+"formEditIdiomas");
                            simpleScript.reloadGrid("#"+diccionario.tabs.IDIOM+"gridIdiomas");
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content:  lang.mensajes.MSG_4
                    });
                }
            }
        });
    };
    
    this.publico.postFileIdiomas = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.IDIOM+"btnFileIdiomas",
            root: _private.config.modulo + "postFileIdiomas",
            form: "#"+diccionario.tabs.IDIOM+"formFileIdiomas",
            data: "&_idIdiomas="+_private.idIdiomas+"&_sigla="+_private.sigla,
            fnCallback: function(data){
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idIdiomas = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.IDIOM+"formFileIdiomas");
                            simpleScript.reloadGrid("#"+diccionario.tabs.IDIOM+"gridIdiomas");
                        }
                    });
                }
            }
        });
    };
    
    this.publico.postDeleteIdiomas = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    gifProcess: true,
                    data: "&_idIdiomas="+id,
                    root: _private.config.modulo + "postDeleteIdiomas",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.IDIOM+"gridIdiomas");
                                }
                            });
                        }
                    }
                });
            }
        });
    };
    
    this.publico.postDeleteIdiomasAll = function(btn){
        simpleScript.validaCheckBox({
            id: "#"+diccionario.tabs.IDIOM+"gridIdiomas",
            msn: lang.mensajes.MSG_9,
            fnCallback: function(){
                simpleScript.notify.confirm({
                    content: lang.mensajes.MSG_7,
                    callbackSI: function(){
                        simpleAjax.send({
                            flag: 3, //si se usa SP usar flag, sino se puede eliminar esta linea
                            element: btn,
                            form: "#"+diccionario.tabs.IDIOM+"formGridIdiomas",
                            root: _private.config.modulo + "postDeleteIdiomasAll",
                            fnCallback: function(data) {
                                if(!isNaN(data.result) && parseInt(data.result) === 1){
                                    simpleScript.notify.ok({
                                        content: lang.mensajes.MSG_8,
                                        callback: function(){
                                            idiomas.getGridIdiomas();
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
    
    this.publico.postDesactivar = function(btn,id){
           simpleAjax.send({
               element: btn,
               root: _private.config.modulo + 'postDesactivar',
               data: '&_idIdiomas='+id,
               fnCallback: function(data) {
                   if(!isNaN(data.result) && parseInt(data.result) === 1){
                       simpleScript.notify.ok({
                           content: lang.mensajes.MSG_24,
                           callback: function(){
                               simpleScript.reloadGrid('#'+diccionario.tabs.IDIOM+'gridIdiomas');
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
            data: '&_idIdiomas='+id,
            fnCallback: function(data){
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_25,
                        callback: function(){
                            simpleScript.reloadGrid('#'+diccionario.tabs.IDIOM+'gridIdiomas');
                        }
                    });
                }
            }
        });
    };       
    
    return this.publico;
    
};
var idiomas = new idiomas_();