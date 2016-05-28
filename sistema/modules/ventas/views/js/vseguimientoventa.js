/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 18-11-2014 17:11:41 
* Descripcion : vseguimientoventa.js
* ---------------------------------------
*/
var vseguimientoventa_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idVenta = 0;
    _private.saldo = 0;
    _private.mapaSitio = '';
     
    _private.config = {
        modulo: "ventas/vseguimientoventa/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : Vseguimientoventa*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.VSEVE,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                vseguimientoventa.getContenido();
            }
        });
    };
    
    /*contenido de tab: Vseguimientoventa*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.VSEVE+"_CONTAINER").html(data);
                vseguimientoventa.getGridVseguimientoventa();
            }
        });
    };
                
    this.publico.getGridVseguimientoventa = function (){
        var oTable = $("#"+diccionario.tabs.VSEVE+"gridVseguimientoventa").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.CODIGO, sWidth: "10%"},
                {sTitle: lang.generico.CLIENTE, sWidth: "30%"},  
                {sTitle: lang.generico.SUCURSAL, sWidth: "8%"},
                {sTitle: lang.generico.FECHA, sWidth: "10%",  sClass: "center"},
                {sTitle: lang.generico.TOTAL, sWidth: "13%",  sClass: "right"}, 
                {sTitle: lang.generico.SALDO, sWidth: "13%",  sClass: "right"}, 
                {sTitle: lang.generico.ESTADO, sWidth: "8%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "15%", sClass: "center", bSortable: false}                           
            ],
            aaSorting: [[0, "desc"]],
            sScrollY: "350px",
            sAjaxSource: _private.config.modulo+"getGridVseguimientoventa",
            fnServerParams: function(aoData) {
                aoData.push({"name": "_idSucursalGrid", "value": $("#"+diccionario.tabs.VSEVE+"lst_sucursalGrid").val()});
            },
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.VSEVE+"gridVseguimientoventa_filter").find("input").attr("placeholder",lang.busqueda.VSEVE).css("width","250px");
                simpleScript.enterSearch("#"+diccionario.tabs.VSEVE+"gridVseguimientoventa",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.VSEVE,
                    typeElement: "button"
                });                
           }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getIndexPago = function(btn,idd){
        _private.idVenta = idd;
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo+'getFormIndexPagoVenta',
            data: '&_idVenta='+_private.idVenta,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.VSEVE+"formIndexPagoVenta").modal("show");                
                setTimeout(function(){vseguimientoventa.getGridPagoVenta()},500);
                $("#"+diccionario.tabs.VSEVE+"formIndexPagoVenta .scroll-form").css('height','auto');  
            }
        });
    };        
    
    this.publico.getGridPagoVenta = function (){
        
        var oTable = $("#"+diccionario.tabs.VSEVE+"gridPagoVenta").dataTable({
            bFilter: false,
            sSearch: false,
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "1%",bSortable: false},                
                {sTitle: lang.generico.FECHA, sWidth: "20%", sClass: "center"},
                {sTitle: lang.generico.METODOPAGO, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.TOTAL, sWidth: "25%", sClass: "right"},
                {sTitle: lang.generico.ESTADO, sWidth: "15%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "10%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "200px",
            sAjaxSource: _private.config.modulo+"getGridPagoVenta",
            fnServerParams: function(aoData) {
                aoData.push({"name": "_idVenta", "value": _private.idVenta });
            },
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.VSEVE+"gridPagoVenta_filter").find("input").attr("placeholder",'Buscar por Serie o Numero').css("width","200px").css("height","28px");
                simpleScript.enterSearch("#"+diccionario.tabs.VSEVE+"gridPagoVenta",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#"+diccionario.tabs.VSEVE+'formIndexPagoVenta',
                    typeElement: "button"
                });               
                $("#"+diccionario.tabs.VSEVE+"gridPagoVenta_info").css('margin-left','10px'); 
                $("#"+diccionario.tabs.VSEVE+"gridPagoVenta_wrapper .dataTables_paginate").css('margin-right','10px'); 
                $('#'+diccionario.tabs.VSEVE+'pagoVenta_refresh').css('padding','5px 10px');                              
           }
        });
        setup_widgets_desktop();
    };    
    
    
    
    this.publico.getFormPagarVenta = function(btn,id){
        _private.idVenta = id;
        _private.saldo = $("#"+diccionario.tabs.VSEVE+"txt_saldo").val();    
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormPagarVenta",
            data: "&_idVenta="+_private.idVenta+'&_saldo='+_private.saldo,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.VSEVE+"formNewPagarVenta").modal("show");
            }
        });
    };    
                
    this.publico.postPagoVenta = function(){        
        
        if(parseFloat($('#'+diccionario.tabs.VSEVE+'txt_pago').val()) <= 0){
            simpleScript.notify.warning({
                content: 'Monto a pagar no puede ser igual o menor a CERO.'
            });
            return false;
        }
        
        simpleAjax.send({
            element: "#"+diccionario.tabs.VSEVE+"btnGrVseguimientoventa",
            root: _private.config.modulo + "postPagarVenta",
            form: "#"+diccionario.tabs.VSEVE+"formNewPagarVenta",
            data: "&_idVenta="+_private.idVenta,            
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){                                                        
                            simpleScript.reloadGrid("#"+diccionario.tabs.VSEVE+"gridPagoVenta");
                            $("#"+diccionario.tabs.VSEVE+"txt_saldo").val(simpleScript.deleteComa(data.saldo));
                            if(data.saldo == 0){
                                $("#"+diccionario.tabs.VSEVE+"btnNewPagoVenta").attr("disabled","disabled"); 
                            }
                            $('#'+diccionario.tabs.VSEVE+'saldo_pago').html('S/ '+globalScript.number_format(data.saldo,2));
                            
                            if($('#'+diccionario.tabs.VSEVE+'_CONTAINER').length > 0){ 
                                setTimeout(function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.VSEVE+"gridVseguimientoventa"); 
                                },500);    
                            }
                            if($('#'+diccionario.tabs.VGEVE+'_CONTAINER').length > 0){
                               setTimeout(function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.VGEVE+"gridGenerarVenta"); 
                                },1000);  
                            }                               
                            simpleScript.closeModal("#"+diccionario.tabs.VSEVE+"formNewPagarVenta");
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: "No existe saldo para registrar pago."
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.error({
                        content: "Monto es mayor que el saldo: "+'S/ '+globalScript.number_format(data.saldo,2)
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
                    $('#'+diccionario.tabs.VSEVE+'btnDowPDF').off('click');
                    $('#'+diccionario.tabs.VSEVE+'btnDowPDF').attr("onclick","window.open('public/files/"+data.archivo+"','_blank');");
                    $('#'+diccionario.tabs.VSEVE+'btnDowPDF').click();
                }
            }
        });
    };
    
    this.publico.postAnularPago = function(btn, idx){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_13,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    data: "&_idPago="+idx,
                    root: _private.config.modulo + "postAnularPago",
                    fnCallback: function(data){
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_17,
                                callback: function(){
                                    $('#'+diccionario.tabs.VSEVE+'saldo_pago').html('S/ '+globalScript.number_format(data.saldo,2));
                                    simpleScript.reloadGrid("#"+diccionario.tabs.VSEVE+"gridPagoVenta");
                                    $("#"+diccionario.tabs.VSEVE+"txt_saldo").val(simpleScript.deleteComa(data.saldo));
                                    if(data.saldo !== 0){
                                        $("#"+diccionario.tabs.VSEVE+"btnNewPagoVenta").removeAttr("disabled"); 
                                    }
                                    if($('#'+diccionario.tabs.VSEVE+'_CONTAINER').length > 0){ 
                                        setTimeout(function(){
                                            simpleScript.reloadGrid("#"+diccionario.tabs.VSEVE+"gridVseguimientoventa"); 
                                        },500);    
                                    }
                                    if($('#'+diccionario.tabs.VGEVE+'_CONTAINER').length > 0){
                                       setTimeout(function(){
                                            simpleScript.reloadGrid("#"+diccionario.tabs.VGEVE+"gridGenerarVenta"); 
                                        },1000);  
                                    }                                                                        
                                }
                            });
                         }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                            simpleScript.notify.error({
                                content: lang.mensajes.MSG_43
                            });                
                        }
                    }
                });
            }
        });
    };    
    
    this.publico.postEditarGenerarVenta = function(btn, idx){
        
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_44,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    data: "&_idVenta="+idx,
                    root: _private.config.modulo + "postModificarGenerarVenta",
                    fnCallback: function(data){
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_45,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.VSEVE+"gridVseguimientoventa"); 
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
var vseguimientoventa = new vseguimientoventa_();