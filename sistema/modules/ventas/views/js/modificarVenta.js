/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 10-05-2016 19:05:57 
* Descripcion : modificarVenta.js
* ---------------------------------------
*/
var modificarVenta_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idVenta = 0;
    _private.mapaSitio = "";
        
    _private.config = {
        modulo: "ventas/modificarVenta/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : ModificarVenta*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.VMOVE,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                modificarVenta.getContenido();
            }
        });
    };
    
    /*contenido de tab: ModificarVenta*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: "&_siteMap="+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.VMOVE+"_CONTAINER").html(data);
                modificarVenta.getGridModificarVenta();
            }
        });
    };
    
    this.publico.getGridModificarVenta = function (){
        var oTable = $("#"+diccionario.tabs.VMOVE+"gridModificarVenta").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.CODIGO, sWidth: "8%"},
                {sTitle: lang.generico.CLIENTE, sWidth: "20%"},  
                {sTitle: lang.generico.SUCURSAL, sWidth: "10%"},
                {sTitle: lang.generico.FECHA, sWidth: "10%",  sClass: "center"},
                {sTitle: lang.generico.TOTAL, sWidth: "11%",  sClass: "right"}, 
                {sTitle: lang.generico.PAGADO, sWidth: "11%",  sClass: "right"}, 
                {sTitle: lang.generico.SALDO, sWidth: "11%",  sClass: "right"}, 
                {sTitle: lang.generico.ESTADO, sWidth: "8%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "15%", sClass: "center", bSortable: false}            
            ],
            aaSorting: [[0, "desc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridModificarVenta",
             fnServerParams: function(aoData) {
                aoData.push({"name": "_idSucursalGrid", "value": $("#"+diccionario.tabs.VMOVE+"lst_sucursalGrid").val()});
            },
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.VMOVE+"gridModificarVenta_filter").find("input").attr("placeholder",lang.busqueda.VMOVE).css("width","250px");
                simpleScript.enterSearch("#"+diccionario.tabs.VMOVE+"gridModificarVenta",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.VMOVE,
                    typeElement: "button"
                });
            }
        });
        setup_widgets_desktop();
    };
    
    this.publico.postPDF = function(btn,id){
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postPDF',
            data: '&_idVenta='+id,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VMOVE+'btnDowPDF').off('click');
                    $('#'+diccionario.tabs.VMOVE+'btnDowPDF').attr("onclick","window.open('public/files/"+data.archivo+"','_blank');");
                    $('#'+diccionario.tabs.VMOVE+'btnDowPDF').click();
                }
            }
        });
    };
    
    this.publico.getFormEditVenta = function(btn,id){
        _private.idVenta = id;        
        if($('#'+diccionario.tabs.VGEVE+'new_CONTAINER').length > 0){
            simpleScript.notify.warning({
                content: "La pestaña de Nueva o Editar o Procesar Venta se encuentra abierta, debe cerrarla para realizar este proceso."
            });
            return;
        }
        generarVentaScript.resetArrayProducto();
        simpleScript.addTab({
            id : diccionario.tabs.VGEVE+'new',
            label: 'Editar Venta',
            fnCallback: function(){
                modificarVenta.getContEditVenta(btn);
            }
        });
    };
    
    this.publico.getContEditVenta = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: 'html',
            root: _private.config.modulo+'getFormEditVenta',
            data: '&_idVenta='+_private.idVenta,
            fnCallback: function(data){
                $('#'+diccionario.tabs.VGEVE+'new_CONTAINER').html(data);
            }
        });
    };          
    
    this.publico.getFormCancelarVenta = function(btn,id){
        _private.idVenta = id;
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormCancelarVenta",
            data: "&_idVenta="+_private.idVenta,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.VMOVE+"formCancelarVenta").modal("show");
            }
        });
    };    
    
    this.publico.postEditarGenerarVenta = function(){   
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_46,
            callbackSI: function(){
                 simpleAjax.send({
                    element: "#"+diccionario.tabs.VMOVE+"btnEdVenta",
                    root: _private.config.modulo + "postModificarVenta",
                    form: "#"+diccionario.tabs.VMOVE+"formEditGenerarVenta",
                    data: '&_idVenta='+_private.idVenta,
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_3,
                                callback: function(){
                                    simpleScript.closeTab(diccionario.tabs.VGEVE+'new');                                                 
                                    simpleScript.reloadGrid("#"+diccionario.tabs.VMOVE+"gridModificarVenta");
                                    _private.idVenta = 0;            
                                    if($('#'+diccionario.tabs.VGEVE+'_CONTAINER').length > 0){
                                        simpleScript.asyncJs({fn: function(){simpleScript.reloadGrid("#"+diccionario.tabs.VGEVE+"gridGenerarVenta");}, tiempo:1500});                                
                                    }     
                                }
                            });
                        }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                            simpleScript.notify.error({
                                content: "No existe caja. Por favor revisar."
                            });
                        }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                            simpleScript.notify.error({
                                content: "El pago inicial + el pago anterior, es mayor que el total a pagar. Por favor verificar."
                            });
                        }
                    }
                });
            }
        });
       
    };
    
     this.publico.postCancelarVenta = function(){
         
          simpleScript.notify.confirm({
            content: lang.mensajes.MSG_44,
            callbackSI: function(){
                simpleAjax.send({
                    element: "#"+diccionario.tabs.VMOVE+"btnInVenta",
                    root: _private.config.modulo + "postCancelarVenta",
                    form: "#"+diccionario.tabs.VMOVE+"formCancelarVenta",
                    data: '&_idVenta='+_private.idVenta,
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_3,
                                callback: function(){                                                                
                                    simpleScript.closeModal("#"+diccionario.tabs.VMOVE+"formCancelarVenta");
                                    simpleScript.reloadGrid("#"+diccionario.tabs.VMOVE+"gridModificarVenta");
                                    _private.idVenta = 0;            
                                    if($('#'+diccionario.tabs.VGEVE+'_CONTAINER').length > 0){
                                        simpleScript.asyncJs({fn: function(){simpleScript.reloadGrid("#"+diccionario.tabs.VGEVE+"gridGenerarVenta");}, tiempo:1500});                                
                                    }     
                                }
                            });
                        }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                            simpleScript.notify.error({
                                content: "No existe caja. Por favor revisar."
                            });
                        }
                    }
                });
            }
        });                  
               
    };
      
    return this.publico;
    
};
var modificarVenta = new modificarVenta_();