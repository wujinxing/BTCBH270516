/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 23-11-2015 17:11:06 
* Descripcion : errorBD.js
* ---------------------------------------
*/
var errorBD_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idErrorBD = 0;
    _private.mapaSitio = "";
        
    _private.config = {
        modulo: "usuarios/errorBD/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : ErrorBD*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.ERRBD,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                errorBD.getContenido();
            }
        });
    };
    
    /*contenido de tab: ErrorBD*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: "&_siteMap="+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.ERRBD+"_CONTAINER").html(data);
                errorBD.getGridErrorBD();
            }
        });
    };
    
    this.publico.getGridErrorBD = function (){
        var oTable = $("#"+diccionario.tabs.ERRBD+"gridErrorBD").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "1%"},                
                {sTitle: lang.generico.FECHAHORA, sWidth: "15%"},
                {sTitle: lang.generico.IP, sWidth: "10%"},
                {sTitle: lang.generico.ERROR, sWidth: "25%"},
                {sTitle: lang.generico.ACCIONES, sWidth: "8%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[0, "desc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridErrorBD",
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.ERRBD+"gridErrorBD_filter").find("input").attr("placeholder",lang.busqueda.ERRBD).css("width","350px");
                simpleScript.enterSearch("#"+diccionario.tabs.ERRBD+"gridErrorBD",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.ERRBD,
                    typeElement: "button"
                });
            }
        });
        setup_widgets_desktop();
    };

    this.publico.getFormEditErrorBD = function(btn,id){
        _private.idErrorBD = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditErrorBD",
            data: "&_idErrorBD="+_private.idErrorBD,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.ERRBD+"formEditErrorBD").modal("show");
            }
        });
    };
    
    this.publico.postDeleteErrorBD = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    gifProcess: true,
                    data: "&_idErrorBD="+id,
                    root: _private.config.modulo + "postDeleteErrorBD",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.closeModal('#'+diccionario.tabs.ERRBD+'formEditErrorBD');
                                    simpleScript.reloadGrid("#"+diccionario.tabs.ERRBD+"gridErrorBD");
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
var errorBD = new errorBD_();