/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 09-12-2014 16:12:55 
* Descripcion : moneda.js
* ---------------------------------------
*/
var moneda_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idMoneda = 0;
    _private.mapaSitio = '';
    
    _private.config = {
        modulo: "configuracion/moneda/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : Moneda*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.MOND,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                moneda.getContenido();
            }
        });
    };
    
    /*contenido de tab: Moneda*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.MOND+"_CONTAINER").html(data);
                moneda.getGridMoneda();
            }
        });
    };
    
    this.publico.getGridMoneda = function (){
        var oTable = $("#"+diccionario.tabs.MOND+"gridMoneda").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "5%", sClass: "center", bSortable: false},                
                {sTitle: lang.generico.DESCRIPCION, sWidth: "25%"},
                {sTitle: lang.generico.SIGLA, sWidth: "25%"},
                {sTitle: lang.generico.FECHACREADO, sWidth: "10%", sClass: "center", bSortable: false},
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center", bSortable: false},
                {sTitle: lang.generico.ACCIONES, sWidth: "8%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridMoneda",
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.MOND+"gridMoneda_filter").find("input").attr("placeholder",lang.busqueda.ANEDI2).css("width","250px");
                simpleScript.enterSearch("#"+diccionario.tabs.MOND+"gridMoneda",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.MOND,
                    typeElement: "button"
                });               
           }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewMoneda = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewMoneda",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.MOND+"formNewMoneda").modal("show");
            }
        });
    };
    
    this.publico.getFormEditMoneda = function(btn,id){
        _private.idMoneda = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditMoneda",
            data: "&_idMoneda="+_private.idMoneda,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.MOND+"formEditMoneda").modal("show");
            }
        });
    };
    
    this.publico.postNewMoneda = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.MOND+"btnGrMoneda",
            root: _private.config.modulo + "postNewMoneda",
            form: "#"+diccionario.tabs.MOND+"formNewMoneda",
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            moneda.getGridMoneda();                        
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
    
    this.publico.postEditMoneda = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.MOND+"btnEdMoneda",
            root: _private.config.modulo + "postEditMoneda",
            form: "#"+diccionario.tabs.MOND+"formEditMoneda",
            data: "&_idMoneda="+_private.idMoneda,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idMoneda = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.MOND+"formEditMoneda");
                            simpleScript.reloadGrid("#"+diccionario.tabs.MOND+"gridMoneda");
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
    
    this.publico.postDeleteMonedaAll = function(btn){
        simpleScript.validaCheckBox({
            id: "#"+diccionario.tabs.MOND+"gridMoneda",
            msn: lang.mensajes.MSG_9,
            fnCallback: function(){
                simpleScript.notify.confirm({
                    content: lang.mensajes.MSG_7,
                    callbackSI: function(){
                        simpleAjax.send({
                            element: btn,
                            form: "#"+diccionario.tabs.MOND+"formGridMoneda",
                            root: _private.config.modulo + "postDeleteMonedaAll",
                            fnCallback: function(data) {
                                if(!isNaN(data.result) && parseInt(data.result) === 1){
                                    simpleScript.notify.ok({
                                        content: lang.mensajes.MSG_8,
                                        callback: function(){
                                            moneda.getGridMoneda();
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
               data: '&_idMoneda='+id,
               fnCallback: function(data) {
                   if(!isNaN(data.result) && parseInt(data.result) === 1){
                       simpleScript.notify.ok({
                           content: lang.mensajes.MSG_24,
                           callback: function(){
                               simpleScript.reloadGrid('#'+diccionario.tabs.MOND+'gridMoneda');
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
            data: '&_idMoneda='+id,
            fnCallback: function(data){
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_25,
                        callback: function(){
                            simpleScript.reloadGrid('#'+diccionario.tabs.MOND+'gridMoneda');
                        }
                    });
                }
            }
        });
    };       
    
    
    return this.publico;
    
};
var moneda = new moneda_();