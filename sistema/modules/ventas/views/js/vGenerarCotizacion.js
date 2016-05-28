/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 05-09-2014 23:09:58 
* Descripcion : vGenerarCotizacion.js
* ---------------------------------------
*/
var vGenerarCotizacion_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idCotizacion = 0;
    _private.mapaSitio = '';
    _private.tab = '';
    
    _private.config = {
        modulo: "ventas/generarCotizacion/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : VGenerarCotizacion*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.VCOTI,
            label: $(element).attr("title"),
            fnCallback: function(){
                 _private.mapaSitio = $(element).data("sitemap"); 
                vGenerarCotizacion.getContenido();
            }
        });
    };
    
    /*contenido de tab: VGenerarCotizacion*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.VCOTI+"_CONTAINER").html(data);
                vGenerarCotizacion.getGridVGenerarCotizacion();
            }
        });
    };
    
    this.publico.getGridVGenerarCotizacion = function (){
       var oTable = $('#'+diccionario.tabs.VCOTI+'gridVGenerarCotizacion').dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.CODIGO, sWidth: "11%"},
                {sTitle: lang.generico.CLIENTE, sWidth: "30%"},                
                {sTitle: lang.generico.FECHA, sWidth: "12%",  sClass: "center"},
                {sTitle: lang.generico.TOTAL, sWidth: "15%",  sClass: "right"}, 
                {sTitle: lang.generico.ESTADO, sWidth: "15%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "18%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[0, 'desc']],
            sScrollY: "350px",
            sAjaxSource: _private.config.modulo+'getGridGenerarCotizacion',
            fnDrawCallback: function() {
                $('#'+diccionario.tabs.VCOTI+'gridVGenerarCotizacion_filter').find('input').attr('placeholder',lang.busqueda.VCOTI).css('width','400px');               
                simpleScript.enterSearch("#"+diccionario.tabs.VCOTI+"gridVGenerarCotizacion",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: '#widget_'+diccionario.tabs.VCOTI, //widget del datagrid
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
                $('#'+diccionario.tabs.VCOTI+'formBuscarProductos').modal('show');
            }
        });
    };
    
    this.publico.getFormNewVGenerarCotizacion = function(btn){
        if($('#'+diccionario.tabs.VCOTI+'new_CONTAINER').length > 0){
            simpleScript.notify.warning({
                content: "La pestaña de Nueva o Clonar Cotización se encuentra abierta, debe cerrarla para realizar este proceso."
            });
            return;
        }
        vGenerarCotizacionScript.resetArrayProducto();
        simpleScript.addTab({
            id : diccionario.tabs.VCOTI+'new',
            label: 'Nueva Cotización',
            fnCallback: function(){
                vGenerarCotizacion.getContGenerarCotizacion(btn);
            }
        });
    };
    
    this.publico.getContGenerarCotizacion = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: 'html',
            root: _private.config.modulo+'getFormNewVGenerarCotizacion',
            fnCallback: function(data){
                $('#'+diccionario.tabs.VCOTI+'new_CONTAINER').html(data);
            }
        });
    };    
    
    this.publico.getClonarCotizacion = function(btn,idCot){
              
        _private.idCotizacion = idCot;
        
        if($('#'+diccionario.tabs.VCOTI+'new_CONTAINER').length > 0){
            simpleScript.notify.warning({
                content: "La pestaña de Nueva o Clonar Cotización se encuentra abierta, debe cerrarla para realizar este proceso."
            });
            return;
        }
        
        simpleScript.addTab({
            id : diccionario.tabs.VCOTI+'new',
            label: 'Clonar Cotización',
            fnCallback: function(){
                vGenerarCotizacion.getContClonar(btn);
                vGenerarCotizacionScript.resetArrayProducto();
            }
        });
    };
    
    this.publico.getContClonar = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: 'html',
            root: _private.config.modulo+'getFormClonarCotizacion',
            data: '&_idCotizacion='+_private.idCotizacion,
            fnCallback: function(data){
                $('#'+diccionario.tabs.VCOTI+'new_CONTAINER').html(data);
            }
        });
    };    
        
    this.publico.getContGenerar = function(){
        simpleAjax.send({
            dataType: 'html',
            root: _private.config.modulo+'getFormGenerarVenta',
            data: '&_idCotizacion='+_private.idCotizacion,
            fnCallback: function(data){
                $('#'+diccionario.tabs.VCOTI+'edit_CONTAINER').html(data);
            }
        });
    };           
               
    this.publico.getFormViewDocumentos = function(btn,id){
        _private.idCotizacion = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormViewDocumentos",
            data: "&_idCotizacion="+_private.idCotizacion,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.VCOTI+"formViewDocumentos").modal("show");
            }
        });
    };    
       
    this.publico.postNewVGenerarCotizacion = function(f){      
                     
        simpleAjax.send({
            flag: f,
            element: "#"+diccionario.tabs.VCOTI+"btnGrCotizacion",
            root: _private.config.modulo + "postNewGenerarCotizacion",
            form: "#"+diccionario.tabs.VCOTI+"formNewGenerarCotizacion, #"+diccionario.tabs.VCOTI+"formClonarGenerarCotizacion", 
            data: '&_idCotizacion='+_private.idCotizacion,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            simpleScript.closeTab(diccionario.tabs.VCOTI+'new');
                            simpleScript.reloadGrid("#"+diccionario.tabs.VCOTI+"gridVGenerarCotizacion");
                            _private.idCotizacion = 0;
                        }
                    });
                }
            }
        });
    };
    
    this.publico.postPDF = function(btn,id){
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postPDF',
            data: '&_idCotizacion='+id,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VCOTI+'btnDowPDF').off('click');
                    $('#'+diccionario.tabs.VCOTI+'btnDowPDF').attr("onclick","window.open('public/files/"+data.archivo+"','_blank');");
                    $('#'+diccionario.tabs.VCOTI+'btnDowPDF').click();
                }
            }
        });
    };
    
    this.publico.postPDFCarta = function(btn,id){
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postPDFCarta',
            data: '&_idCotizacion='+id,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VCOTI+'btnDowPDF').off('click');
                    $('#'+diccionario.tabs.VCOTI+'btnDowPDF').attr("onclick","window.open('public/files/"+data.archivo+"','_blank');");
                    $('#'+diccionario.tabs.VCOTI+'btnDowPDF').click();
                }
            }
        });
    };    
    
    this.publico.postExcel = function(btn,id){
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postExcel',
            data: '&_idCotizacion='+id,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VCOTI+'btnDowExcel').off('click');
                    $('#'+diccionario.tabs.VCOTI+'btnDowExcel').attr("onclick","window.open('public/files/"+data.archivo+"','_self');globalScript.deleteArchivo('"+data.archivo+"');");
                    $('#'+diccionario.tabs.VCOTI+'btnDowExcel').click();
                }
                if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: 'Ocurrió un error al exportar Venta.'
                    });
                }
            }
        });
    };
    
    this.publico.postAnularVGenerarCotizacion = function(btn, idx){
    
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_13,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    data: "&_idCotizacion="+idx,
                    root: _private.config.modulo + "postAnularGenerarCotizacion",
                    fnCallback: function(data){
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_17,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.VCOTI+"gridVGenerarCotizacion");
                                }
                            });
                        }
                    }
                });
            }
        });
            
    };    
    
    this.publico.getMensaje = function(btn,id,clie,mail, ven){
        simpleAjax.send({
            element: btn,
            dataType: 'html',
            root: _private.config.modulo + 'getViewMensaje',
            data: '&_nombres='+clie+'&_idCotizacion='+id+'&_mail='+mail+'&_vendedor='+ven,            
            fnCallback: function(data){
                $('#cont-modal').append(data);  /*los formularios con append*/
                $('#'+diccionario.tabs.T4+'formViewMensaje').modal('show');
            }
        });                               
    };    
            
    this.publico.postEmail = function(id, nom, mail){
        
          simpleAjax.send({            
            element: '#'+diccionario.tabs.T4+'btnGrMensaje',
            form: '#'+diccionario.tabs.T4+'formViewMensaje',
            root: _private.config.modulo + 'postEmail',
            data: '&_nombres='+nom+'&_idCotizacion='+id+'&_mail='+mail,            
            fnCallback: function(data){
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: 'Cotización se envió correctamente.'
                    });    
                    globalScript.deleteArchivo(data.coti);
                    globalScript.deleteArchivo(data.carta);
                    simpleScript.reloadGrid("#"+diccionario.tabs.VCOTI+"gridVGenerarCotizacion");
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: 'Ocurrió un error con el envío de la cotización.'
                    });
                }
                simpleScript.closeModal('#'+diccionario.tabs.T4+'formViewMensaje');
            }
        });                
    };    
    
       
    this.publico.getFormMigrarCotizacion = function(btn,id){
        _private.idCotizacion = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormMigrarCotizacion",
            data: "&_idCotizacion="+_private.idCotizacion,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.VCOTI+"formMigrarCotizacion").modal("show");
            }
        });
    };
    
    this.publico.postMigrarCotizacion = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.VCOTI+"btnEdMigrar",
            root: _private.config.modulo + "postMigrarCotizacion",
            form: "#"+diccionario.tabs.VCOTI+"formMigrarCotizacion",
            data: "&_idCotizacion="+_private.idCotizacion,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idVSucursal = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.VCOTI+"formMigrarCotizacion");
                            simpleScript.reloadGrid("#"+diccionario.tabs.VCOTI+"gridVGenerarCotizacion");
                        }
                    });
                }
            }
        });
    };
    
    return this.publico;
    
};
var vGenerarCotizacion = new vGenerarCotizacion_();