/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 08-06-2016 02:06:12 
* Descripcion : mProveedor.js
* ---------------------------------------
*/
var mProveedor_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idMProveedor = 0;
    _private.mapaSitio = "";
        
    _private.config = {
        modulo: "compras/mProveedor/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : MProveedor*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.PROVV,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                mProveedor.getContenido();
            }
        });
    };
    
    /*contenido de tab: MProveedor*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: "&_siteMap="+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.PROVV+"_CONTAINER").html(data);
                mProveedor.getGridMProveedor();
            }
        });
    };
    
    this.publico.getGridMProveedor = function (){
        var oTable = $("#"+diccionario.tabs.PROVV+"gridMProveedor").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: "N°", sWidth: "1%",bSortable: false},                
                {sTitle: "CAMPO 1", sWidth: "25%"},
                {sTitle: "CAMPO 2", sWidth: "25%", bSortable: false},
                {sTitle: "Estado", sWidth: "10%", sClass: "center", bSortable: false},
                {sTitle: "Acciones", sWidth: "8%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridMProveedor",
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.PROVV+"gridMProveedor_filter").find("input").attr("placeholder","Buscar por MProveedor").css("width","250px");
                simpleScript.enterSearch("#"+diccionario.tabs.PROVV+"gridMProveedor",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.PROVV,
                    typeElement: "button"
                });
            }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewMProveedor = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewMProveedor",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.PROVV+"formNewMProveedor").modal("show");
            }
        });
    };
    
    this.publico.getFormEditMProveedor = function(btn,id){
        _private.idMProveedor = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditMProveedor",
            data: "&_idMProveedor="+_private.idMProveedor,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.PROVV+"formEditMProveedor").modal("show");
            }
        });
    };
    
    this.publico.postNewMProveedor = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.PROVV+"btnGrMProveedor",
            root: _private.config.modulo + "postNewMProveedor",
            form: "#"+diccionario.tabs.PROVV+"formNewMProveedor",
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){                            
                            simpleScript.reloadGrid("#"+diccionario.tabs.PROVV+"gridMProveedor");
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
    
    this.publico.postEditMProveedor = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.PROVV+"btnEdMProveedor",
            root: _private.config.modulo + "postEditMProveedor",
            form: "#"+diccionario.tabs.PROVV+"formEditMProveedor",
            data: "&_idMProveedor="+_private.idMProveedor,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idMProveedor = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.PROVV+"formEditMProveedor");
                            simpleScript.reloadGrid("#"+diccionario.tabs.PROVV+"gridMProveedor");
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
    
    this.publico.postDeleteMProveedor = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    gifProcess: true,
                    data: "&_idMProveedor="+id,
                    root: _private.config.modulo + "postDeleteMProveedor",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.PROVV+"gridMProveedor");
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
var mProveedor = new mProveedor_();