/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 05-04-2016 18:04:07 
* Descripcion : vSucursal.js
* ---------------------------------------
*/
var vSucursal_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idVSucursal = 0;
    _private.mapaSitio = "";
        
    _private.config = {
        modulo: "ventas/vSucursal/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : VSucursal*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.SUCUR,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                vSucursal.getContenido();
            }
        });
    };
    
    /*contenido de tab: VSucursal*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: "&_siteMap="+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.SUCUR+"_CONTAINER").html(data);
                vSucursal.getGridVSucursal();
            }
        });
    };
    
    this.publico.getGridVSucursal = function (){
        var oTable = $("#"+diccionario.tabs.SUCUR+"gridVSucursal").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "3%", sClass: "center"},             
                {sTitle: lang.generico.DESCRIPCION, sWidth: "50%"},
                {sTitle: lang.generico.SIGLA, sWidth: "10%"},
                {sTitle: lang.generico.ESTADO, sWidth: "8%", sClass: "center", bSortable: false},
                {sTitle: lang.generico.ACCIONES, sWidth: "15%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "350px",
            sAjaxSource: _private.config.modulo+"getGridVSucursal",
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.SUCUR+"gridVSucursal_filter").find("input").attr("placeholder",lang.busqueda.SUCUR).css("width","250px");
                simpleScript.enterSearch("#"+diccionario.tabs.SUCUR+"gridVSucursal",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.SUCUR,
                    typeElement: "button"
                });
            }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewVSucursal = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewVSucursal",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.SUCUR+"formNewVSucursal").modal("show");
            }
        });
    };
    
    this.publico.getFormEditVSucursal = function(btn,id){
        _private.idVSucursal = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditVSucursal",
            data: "&_idVSucursal="+_private.idVSucursal,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.SUCUR+"formEditVSucursal").modal("show");
            }
        });
    };
    
    this.publico.postNewVSucursal = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.SUCUR+"btnGrVSucursal",
            root: _private.config.modulo + "postNewVSucursal",
            form: "#"+diccionario.tabs.SUCUR+"formNewVSucursal",
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){                            
                            simpleScript.reloadGrid("#"+diccionario.tabs.SUCUR+"gridVSucursal");
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
    
    this.publico.postEditVSucursal = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.SUCUR+"btnEdVSucursal",
            root: _private.config.modulo + "postEditVSucursal",
            form: "#"+diccionario.tabs.SUCUR+"formEditVSucursal",
            data: "&_idVSucursal="+_private.idVSucursal,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idVSucursal = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.SUCUR+"formEditVSucursal");
                            simpleScript.reloadGrid("#"+diccionario.tabs.SUCUR+"gridVSucursal");
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
    
    this.publico.postDeleteVSucursal = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    gifProcess: true,
                    data: "&_idVSucursal="+id,
                    root: _private.config.modulo + "postDeleteVSucursal",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.SUCUR+"gridVSucursal");
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
            data: '&_idVSucursal='+id,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: 'Sucursal se desactivo correctamente',
                        callback: function(){
                            simpleScript.reloadGrid("#"+diccionario.tabs.SUCUR+"gridVSucursal");
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
            data: '&_idVSucursal='+id,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: 'Sucursal se activo correctamente',
                        callback: function(){
                            simpleScript.reloadGrid("#"+diccionario.tabs.SUCUR+"gridVSucursal");
                        }
                    });
                }
            }
        });
    };    
      
    return this.publico;
    
};
var vSucursal = new vSucursal_();