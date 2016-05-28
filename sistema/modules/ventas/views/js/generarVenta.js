/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 05-09-2014 23:09:58 
* Descripcion : generarVenta.js
* ---------------------------------------
*/
var generarVenta_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idVenta = 0;
    _private.idCotizacion = 0;
    _private.mapaSitio = '';
    _private.tab = '';
    
    _private.config = {
        modulo: "ventas/generarVenta/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : GenerarVenta*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.VGEVE,
            label: $(element).attr("title"),
            fnCallback: function(){
                  _private.mapaSitio = $(element).data("sitemap"); 
                generarVenta.getContenido();
            }
        });
    };
    
    /*contenido de tab: GenerarVenta*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.VGEVE+"_CONTAINER").html(data);
                generarVenta.getGridGenerarVenta();
            }
        });
    };
    
    this.publico.getGridGenerarVenta = function (){
       var oTable = $('#'+diccionario.tabs.VGEVE+'gridGenerarVenta').dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.CODIGO, sWidth: "8%"},
                {sTitle: lang.generico.CLIENTE, sWidth: "25%"},  
                {sTitle: lang.generico.SUCURSAL, sWidth: "8%"},  
                {sTitle: lang.generico.FECHA, sWidth: "10%",  sClass: "center"},
                {sTitle: lang.generico.TOTAL, sWidth: "13%",  sClass: "right"}, 
                {sTitle: lang.generico.SALDO, sWidth: "13%",  sClass: "right"}, 
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "20%", sClass: "center", bSortable: false}                
            ],
            aaSorting: [[0, 'desc']],
            sScrollY: "350px",
            sAjaxSource: _private.config.modulo+'getGridGenerarVenta',
            fnServerParams: function(aoData) {
                aoData.push({"name": "_fecha1", "value": $("#"+diccionario.tabs.VGEVE+"txt_f1").val()});                
                aoData.push({"name": "_fecha2", "value": $("#"+diccionario.tabs.VGEVE+"txt_f2").val()});
                aoData.push({"name": "_idSucursalGrid", "value": $("#"+diccionario.tabs.VGEVE+"lst_sucursalGrid").val()});
            },
            fnDrawCallback: function() {
                $('#'+diccionario.tabs.VGEVE+'gridGenerarVenta_filter').find('input').attr('placeholder',lang.busqueda.VGEVE).css('width','200px');               
                simpleScript.enterSearch("#"+diccionario.tabs.VGEVE+"gridGenerarVenta",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: '#widget_'+diccionario.tabs.VGEVE, //widget del datagrid
                    typeElement: 'button'
                });
           }
        });
        setup_widgets_desktop();                
        
    };    
    
    this.publico.getFormBuscarProductos = function(btn,tab){
        _private.tab = tab;
        simpleAjax.send({
            element: btn,
            dataType: 'html',
            root: _private.config.modulo + 'getFormBuscarProductos',
            fnCallback: function(data){
                $('#cont-modal').append(data);  /*los formularios con append*/
                $('#'+diccionario.tabs.VGEVE+'formBuscarProductos').modal('show');
            }
        });
    };
    
    this.publico.getFormNewIngresoDirecto = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewIngresoDirecto",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.VGEVE+"formNewIngresoDirecto").modal("show");
            }
        });
    };
    
    this.publico.getFormNewGenerarVenta = function(btn){
        if($('#'+diccionario.tabs.VGEVE+'new_CONTAINER').length > 0){
            simpleScript.notify.warning({
                content: "La pestaña de Nueva o Editar o Procesar Venta se encuentra abierta, debe cerrarla para realizar este proceso."
            });
            return;
        }
        generarVentaScript.resetArrayProducto();
        simpleScript.addTab({
            id : diccionario.tabs.VGEVE+'new',
            label: 'Nueva Venta',
            fnCallback: function(){
                generarVenta.getContGenerarVenta();
            }
        });
    };
    
    this.publico.getContGenerarVenta = function(){
        simpleAjax.send({
            dataType: 'html',
            root: _private.config.modulo+'getFormNewGenerarVenta',
            fnCallback: function(data){
                $('#'+diccionario.tabs.VGEVE+'new_CONTAINER').html(data);
            }
        });
    };
    
    this.publico.getFormProcesarGenerarVenta = function(btn,id){
        _private.idCotizacion = id;        
        
        if($('#'+diccionario.tabs.VGEVE+'new_CONTAINER').length > 0){
            simpleScript.notify.warning({
                content: "La pestaña de Nueva o Editar o Procesar Venta se encuentra abierta, debe cerrarla para realizar este proceso."
            });
            return;
        }
        
        generarVentaScript.resetArrayProducto();
        simpleScript.addTab({
            id : diccionario.tabs.VGEVE+'new',
            label: 'Nueva Venta - Cotización',
            fnCallback: function(){             
                generarVenta.getContProcesarVenta(btn);             
            }
        });
    };
    
    this.publico.getContProcesarVenta = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: 'html',
            root: _private.config.modulo+'getFormProcesarGenerarVenta',
            data: '&_idCotizacion='+_private.idCotizacion,
            fnCallback: function(data){
                $('#'+diccionario.tabs.VGEVE+'new_CONTAINER').html(data);
            }
        });
    };           
           
    this.publico.postNewGenerarVenta = function(){   
        simpleAjax.send({
            element: "#"+diccionario.tabs.VGEVE+"btnGrVenta",
            root: _private.config.modulo + "postNewGenerarVenta",
            form: "#"+diccionario.tabs.VGEVE+"formNewGenerarVenta",
            data: '&_idVenta='+_private.idVenta+'&_idCotizacion='+_private.idCotizacion,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            simpleScript.closeTab(diccionario.tabs.VGEVE+'new');
                            /*Si esta abierto Cotizacion*/
                            if($('#'+diccionario.tabs.VCOTI+'_CONTAINER').length > 0){
                                simpleScript.asyncJs({fn: function(){simpleScript.reloadGrid("#"+diccionario.tabs.VCOTI+"gridVGenerarCotizacion");}, tiempo:1500});                                
                            }                               
                            simpleScript.reloadGrid("#"+diccionario.tabs.VGEVE+"gridGenerarVenta");
                            _private.idVenta = 0;
                            _private.idCotizacion= 0;
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: "No existe caja. Por favor revisar."
                    });
                }
            }
        });
    };
    
    this.publico.postNewIngresoDirecto = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.VGEVE+"btnGrIngresoDirecto",
            root: _private.config.modulo + "postNewIngresoDirecto",
            form: "#"+diccionario.tabs.VGEVE+"formNewIngresoDirecto",
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3                        
                    });
                    simpleScript.reloadGrid("#"+diccionario.tabs.VGEVE+"gridGenerarVenta");
                    simpleScript.closeModal("#"+diccionario.tabs.VGEVE+"formNewIngresoDirecto");  
                    
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: "No existe caja. Por favor revisar."
                    });
                }
            }
        });
    };    
        
    this.publico.postPDF = function(btn,id){
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postPDF',
            data: '&_idVenta='+id,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VGEVE+'btnDowPDF').off('click');
                    $('#'+diccionario.tabs.VGEVE+'btnDowPDF').attr("onclick","window.open('public/files/"+data.archivo+"','_blank');");
                    $('#'+diccionario.tabs.VGEVE+'btnDowPDF').click();
                }
            }
        });
    };
           
    this.publico.postExcel = function(btn,id){
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postExcel',
            data: '&_idVenta='+id,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VGEVE+'btnDowExcel').off('click');
                    $('#'+diccionario.tabs.VGEVE+'btnDowExcel').attr("onclick","window.open('public/files/"+data.archivo+"','_self');");
                    $('#'+diccionario.tabs.VGEVE+'btnDowExcel').click();
                }
                if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: 'Ocurrió un error al exportar Venta.'
                    });
                }
            }
        });
    };
    
    this.publico.postAnularGenerarVenta = function(btn, idx){
        
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_13,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    data: "&_idVenta="+idx,
                    root: _private.config.modulo + "postAnularGenerarVenta",
                    fnCallback: function(data){
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_17,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.VGEVE+"gridGenerarVenta");
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
var generarVenta = new generarVenta_();