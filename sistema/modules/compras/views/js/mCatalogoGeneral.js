/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 02-06-2016 19:06:04 
* Descripcion : mCatalogoGeneral.js
* ---------------------------------------
*/
var mCatalogoGeneral_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idMCatalogoGeneral = 0;
    _private.mapaSitio = "";
        
    _private.config = {
        modulo: "compras/mCatalogoGeneral/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : MCatalogoGeneral*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.CATGR,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                mCatalogoGeneral.getContenido();
            }
        });
    };
    
    /*contenido de tab: MCatalogoGeneral*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: "&_siteMap="+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.CATGR+"_CONTAINER").html(data);
                mCatalogoGeneral.getGridMCatalogoGeneral();
            }
        });
    };
    
    this.publico.getGridMCatalogoGeneral = function (){
        var oTable = $("#"+diccionario.tabs.CATGR+"gridMCatalogoGeneral").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "5%", sClass: "center"},
                {sTitle: lang.generico.PRODUCTO, sWidth: "20%"},
                {sTitle: lang.generico.CONCENTRACION, sWidth: "10%"},                                
                {sTitle: lang.generico.PRESENTACION, sWidth: "15%"},
                {sTitle: lang.generico.LABORATORIO, sWidth: "20%"},
                {sTitle: lang.generico.FRACCION, sWidth: "8%"},
                {sTitle: lang.generico.RECETAMEDICA, sWidth: "8%"},
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "8%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridMCatalogoGeneral",
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.CATGR+"gridMCatalogoGeneral_filter").find("input").attr("placeholder",lang.busqueda.CATGR).css("width","400px");
                simpleScript.enterSearch("#"+diccionario.tabs.CATGR+"gridMCatalogoGeneral",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.CATGR,
                    typeElement: "button"
                });
            }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewMCatalogoGeneral = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewMCatalogoGeneral",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.CATGR+"formNewMCatalogoGeneral").modal("show");
            }
        });
    };
    
    this.publico.getFormEditMCatalogoGeneral = function(btn,id){
        _private.idMCatalogoGeneral = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditMCatalogoGeneral",
            data: "&_idMCatalogoGeneral="+_private.idMCatalogoGeneral,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.CATGR+"formEditMCatalogoGeneral").modal("show");
            }
        });
    };
    
    this.publico.postNewMCatalogoGeneral = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.CATGR+"btnGrMCatalogoGeneral",
            root: _private.config.modulo + "postNewMCatalogoGeneral",
            form: "#"+diccionario.tabs.CATGR+"formNewMCatalogoGeneral",
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){                            
                            simpleScript.reloadGrid("#"+diccionario.tabs.CATGR+"gridMCatalogoGeneral");
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
    
    this.publico.postEditMCatalogoGeneral = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.CATGR+"btnEdMCatalogoGeneral",
            root: _private.config.modulo + "postEditMCatalogoGeneral",
            form: "#"+diccionario.tabs.CATGR+"formEditMCatalogoGeneral",
            data: "&_idMCatalogoGeneral="+_private.idMCatalogoGeneral,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idMCatalogoGeneral = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.CATGR+"formEditMCatalogoGeneral");
                            simpleScript.reloadGrid("#"+diccionario.tabs.CATGR+"gridMCatalogoGeneral");
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
    
    this.publico.postDeleteMCatalogoGeneral = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    gifProcess: true,
                    data: "&_idMCatalogoGeneral="+id,
                    root: _private.config.modulo + "postDeleteMCatalogoGeneral",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.CATGR+"gridMCatalogoGeneral");
                                }
                            });
                        }
                    }
                });
            }
        });
    };
      
    return this.publico;
    
};
var mCatalogoGeneral = new mCatalogoGeneral_();