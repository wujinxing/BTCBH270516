/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 06-12-2014 23:12:35 
* Descripcion : cajaCierre.js
* ---------------------------------------
*/
var cajaCierre_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idCajaCierre = 0;
    
    _private.config = {
        modulo: "ventas/cajaCierre/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : CajaCierre*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.CAJAC,
            label: $(element).attr("title"),
            fnCallback: function(){
                cajaCierre.getContenido();
            }
        });
    };
    
    /*contenido de tab: CajaCierre*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            fnCallback: function(data){
                $("#"+diccionario.tabs.CAJAC+"_CONTAINER").html(data);
                cajaCierre.getGridCajaCierre();
            }
        });
    };
    
    this.publico.getGridCajaCierre = function (){
        var oTable = $("#"+diccionario.tabs.CAJAC+"gridCajaCierre").dataTable({
            bFilter:false,
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.ID, sWidth: "2%"},
                {sTitle: lang.generico.FECHAHORA, sWidth: "15%"},
                {sTitle: lang.generico.SUCURSAL, sWidth: "8%", sClass: "center"},
                {sTitle: lang.generico.MONEDA, sWidth: "3%", sClass: "center"},
                {sTitle: lang.generico.INICIAL, sWidth: "10%", sClass: "right"},
                {sTitle: lang.generico.INGRESOS, sWidth: "10%", sClass: "right"},
                {sTitle: lang.generico.EGRESOS, sWidth: "10%", sClass: "right"},
                {sTitle: lang.generico.SALDO, sWidth: "10%", sClass: "right"},
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "12%", sClass: "center", bSortable: false}      
            ],
            aaSorting: [[0, "desc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridCajaCierre",
           fnServerParams: function(aoData) {
                aoData.push({"name": "_fecha1", "value": $("#"+diccionario.tabs.CAJAC+"txt_f1").val()});                
                aoData.push({"name": "_fecha2", "value": $("#"+diccionario.tabs.CAJAC+"txt_f2").val()});
                aoData.push({"name": "_idSucursalGrid", "value": $("#"+diccionario.tabs.CAJAC+"lst_sucursalGrid").val()});
            },
            fnDrawCallback: function() {
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.CAJAC,
                    typeElement: "button"
                });               
           }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewCierre = function(btn,id){
        _private.idCajaCierre = id;
                            
        simpleScript.addTab({
            id : diccionario.tabs.CAJAC+'new',
            label: lang.caja.NEWCIERRE,
            fnCallback: function(){
               cajaCierre.getContCierre(btn);
           }
        });                  
    };
    
    this.publico.getContCierre = function(btn){
        simpleAjax.send({
            dataType: 'html',
            element: btn,
            data: "&_idCajaCierre="+_private.idCajaCierre,
            root: _private.config.modulo+'getFormNewCierre',            
            fnCallback: function(data){
                $('#'+diccionario.tabs.CAJAC+'new_CONTAINER').html(data);
            }
        });
    };        
    
    this.publico.postGenerarCierre = function(){   
        
         simpleScript.notify.confirm({
            content: lang.mensajes.MSG_21,
            callbackSI: function(){
                simpleAjax.send({
                    element: "#"+diccionario.tabs.CAJAC+"btnGrCierre",
                    root: _private.config.modulo + "postGenerarCierre",
                    form: "#"+diccionario.tabs.CAJAC+"formNewCierre",
                    data: "&_idCajaCierre="+_private.idCajaCierre,
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_3,
                                callback: function(){
                                    simpleScript.closeTab(diccionario.tabs.CAJAC+'new');                                                        
                                    if($('#'+diccionario.tabs.VGEVE+'_CONTAINER').length > 0)
                                        simpleScript.closeTab(diccionario.tabs.VGEVE);
                                    if($('#'+diccionario.tabs.VGEVE+'cotizacion_CONTAINER').length > 0)
                                        simpleScript.closeTab(diccionario.tabs.VGEVE+'cotizacion');                                    
                                    if($('#'+diccionario.tabs.VGEVE+'new_CONTAINER').length > 0)
                                        simpleScript.closeTab(diccionario.tabs.VGEVE+'new');
                                    if($('#'+diccionario.tabs.VGEVE+'edit_CONTAINER').length > 0)
                                        simpleScript.closeTab(diccionario.tabs.VGEVE+'edit');
                                    if($('#'+diccionario.tabs.VSEVE+'_CONTAINER').length > 0)
                                        simpleScript.closeTab(diccionario.tabs.VSEVE);                                        
                                    if($('#'+diccionario.tabs.VEGRE+'_CONTAINER').length > 0)
                                        simpleScript.closeTab(diccionario.tabs.VEGRE);                          
                                    simpleScript.reloadGrid("#"+diccionario.tabs.CAJAC+"gridCajaCierre");                                    
                                    /*Ejecutar Boton de Cierre */
                                    simpleScript.asyncJs({fn: function(){$('#btnCaja'+data.idCaja).click();}, tiempo:1000});
                                    
                                    _private.idCajaCierre = 0;
                                }
                            });
                        }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                            simpleScript.notify.error({
                                content: "No existe caja. Por favor revisar."
                            });
                        }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                            simpleScript.notify.warning({
                                content: "La caja ya se encuentra cerrada, ya no se puede realizar este proceso."
                            });
                            simpleScript.reloadGrid("#"+diccionario.tabs.CAJAC+"gridCajaCierre");                                    
                            /*Ejecutar Boton de Cierre */
                            simpleScript.asyncJs({fn: function(){$('#btnCaja'+data.idCaja).click();}, tiempo:1000});
                        }
                    }
                });
            }
        });
    };    
     
    this.publico.postPDF = function(btn,id){
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postPDF',
            data: '&_idCajaCierre='+id,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.CAJAC+'btnDowPDF').off('click');
                    $('#'+diccionario.tabs.CAJAC+'btnDowPDF').attr("onclick","window.open('public/files/"+data.archivo+"','_blank');");
                    $('#'+diccionario.tabs.CAJAC+'btnDowPDF').click();
                }
            }
        });
    };
           
    this.publico.postExcel = function(btn,id){
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postExcel',
            data: '&_idCajaCierre='+id,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.CAJAC+'btnDowExcel').off('click');
                    $('#'+diccionario.tabs.CAJAC+'btnDowExcel').attr("onclick","window.open('public/files/"+data.archivo+"','_self');");
                    $('#'+diccionario.tabs.CAJAC+'btnDowExcel').click();
                }
                if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: 'Ocurrió un error al exportar Venta.'
                    });
                }
            }
        });
    };
    return this.publico;
    
};
var cajaCierre = new cajaCierre_();