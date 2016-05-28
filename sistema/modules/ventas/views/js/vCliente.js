/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 17-11-2014 17:11:18 
* Descripcion : vCliente.js
* ---------------------------------------
*/
var vCliente_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idPersona = 0;
    _private.mapaSitio = '';
    _private.tab = '';
    _private.fn = '';
    _private.callbackData = null;
    
    _private.config = {
        modulo: "ventas/vCliente/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : Vcliente*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.VRECL,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                vCliente.getContenido();
            }
        });
    };
    
    /*contenido de tab: Vcliente*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.VRECL+"_CONTAINER").html(data);
                vCliente.getGridVcliente();
            }
        });
    };
    
    this.publico.getGridVcliente = function (){
        var oTable = $("#"+diccionario.tabs.VRECL+"gridVcliente").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "3%", sClass: "center"},
                {sTitle: lang.generico.CLIENTE, sWidth: "30%"},
                {sTitle: lang.generico.TELEFONO, sWidth: "25%"},
                {sTitle: lang.generico.CIUDAD, sWidth: "20%"},
                {sTitle: lang.generico.ESTADO, sWidth: "8%", sClass: "center", bSortable: false},
                {sTitle: lang.generico.ACCIONES, sWidth: "15%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[0, "desc"]],
            sScrollY: "350px",
            sAjaxSource: _private.config.modulo+"getGridVcliente",
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.VRECL+"gridVcliente_filter").find("input").attr("placeholder",lang.busqueda.VRECL).css("width","300px");
                simpleScript.enterSearch("#"+diccionario.tabs.VRECL+"gridVcliente",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.VRECL,
                    typeElement: "button"
                });
            }
        });
        setup_widgets_desktop();                      
    };
           
    this.publico.getFormNewVcliente = function(btn,callbackData){
        _private.callbackData = callbackData;   
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewVcliente",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.VRECL+"formNewVcliente").modal("show");
            }
        });
    };
    
    this.publico.getFormEditVcliente = function(btn,id){
        _private.idPersona = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditVcliente",
            data: "&_idPersona="+_private.idPersona,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.VRECL+"formEditVcliente").modal("show");
            }
        });
    };
    
    this.publico.postNewVcliente = function(){
        
        var nom =  $("#"+diccionario.tabs.VRECL+"txt_nombres").val();
        var emp =  $("#"+diccionario.tabs.VRECL+"txt_empresa").val();
        
        if (nom == '' && emp == ''){
            simpleScript.notify.warning({
                        content: "Debe de ingresar la empresa o el nombre del Cliente"
            });    
            return;
        }
        
        simpleAjax.send({
            element: "#"+diccionario.tabs.VRECL+"btnGrVcliente",
            root: _private.config.modulo + "postNewVcliente",
            form: "#"+diccionario.tabs.VRECL+"formNewVcliente",
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            
                            if(_private.callbackData.length > 0){       
                                if($('#'+diccionario.tabs.VGEVE+'_CONTAINER').length > 0){
                                    $("#"+diccionario.tabs.VGEVE+"txt_idpersona").val(simpleAjax.stringPost(data.idPersona));       
                                    $("#"+diccionario.tabs.VGEVE+"txt_cliente").val(data.nombre);
                                }
                                if($('#'+diccionario.tabs.VCOTI+'_CONTAINER').length > 0){
                                    $("#"+diccionario.tabs.VCOTI+"txt_idpersona").val(simpleAjax.stringPost(data.idPersona));       
                                    $("#"+diccionario.tabs.VCOTI+"txt_cliente").val(data.nombre);                                                                          
                                }
                                simpleScript.closeModal(_private.callbackData);
                            }
                            /*se verifica si existe tabb para recargar grid*/
                            if($('#'+diccionario.tabs.VRECL+'_CONTAINER').length > 0){
                               vCliente.getGridVcliente();
                            }                                                        
                            simpleScript.closeModal("#"+diccionario.tabs.VRECL+"formNewVcliente");
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: "Nro de Documento ya existe."
                    });                
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.error({
                        content: "La empresa que ingreso, ya se encuentra registrada en el sistema."
                    });                
                }
            }
        });
    };
    
    this.publico.postEditVcliente = function(){
         var nom =  $("#"+diccionario.tabs.VRECL+"txt_nombres").val();
        var emp =  $("#"+diccionario.tabs.VRECL+"txt_empresa").val();
        
        if (nom == '' && emp == ''){
            simpleScript.notify.warning({
                        content: "Debe de ingresar la empresa o el nombre del Cliente"
            });    
            return;
        }
        
        simpleAjax.send({
            element: "#"+diccionario.tabs.VRECL+"btnEdVcliente",
            root: _private.config.modulo + "postEditVcliente",
            form: "#"+diccionario.tabs.VRECL+"formEditVcliente",
            data: "&_idPersona="+_private.idPersona,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idPersona = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.VRECL+"formEditVcliente");
                            simpleScript.reloadGrid("#"+diccionario.tabs.VRECL+"gridVcliente");
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: "Nro de Documento ya existe."
                    });                
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.error({
                        content: "El Cliente que esta registrando ya existe. Debe de buscarlo y seleccionarlo."
                    });                
                }
            }
        });
    };
    
    this.publico.postDeleteVcliente = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({                    
                    element: btn,
                    gifProcess: true,
                    data: "&_idPersona="+id,
                    root: _private.config.modulo + "postDeleteVcliente",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.VRECL+"gridVcliente");
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
            data: '&_idPersona='+id,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: 'Cliente se desactivo correctamente',
                        callback: function(){
                           simpleScript.reloadGrid('#'+diccionario.tabs.VRECL+'gridVcliente'); 
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
            data: '&_idPersona='+id,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: 'Cliente se activo correctamente',
                        callback: function(){
                            simpleScript.reloadGrid('#'+diccionario.tabs.VRECL+'gridVcliente'); 
                        }
                    });
                }
            }
        });
    };    
    
    this.publico.postPDFCarta = function(btn,id){
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postPDFCarta',
            data: '&_idPersona='+id,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VRECL+'btnDowPDF').off('click');
                    $('#'+diccionario.tabs.VRECL+'btnDowPDF').attr("onclick","window.open('public/files/"+data.archivo+"','_blank');globalScript.deleteArchivo('"+data.archivo+"');");
                    $('#'+diccionario.tabs.VRECL+'btnDowPDF').click();
                }
            }
        });
    };        
    
      this.publico.getMensaje = function(btn,id,clie,mail){
        simpleAjax.send({
            element: btn,
            dataType: 'html',
            root: _private.config.modulo + 'getViewMensaje',
            data: '&_nombres='+clie+'&_idPersona='+id+'&_mail='+mail,            
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
            data: '&_nombres='+nom+'&_idPersona='+id+'&_mail='+mail,            
            fnCallback: function(data){
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: 'Carta de Intención, se envió correctamente.'
                    });    
                    globalScript.deleteArchivo(data.carta);
                    simpleScript.reloadGrid("#"+diccionario.tabs.VRECL+"gridVcliente");
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: 'Ocurrió un error con el envío de la Carta de Intención.'
                    });
                }
                simpleScript.closeModal('#'+diccionario.tabs.T4+'formViewMensaje');
            }
        });                
    };    
    
    this.publico.getBuscarCliente = function(btn, tab, fn){
        _private.tab =  tab;
        _private.fn = fn;
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo+'getFormBuscarCliente',
            data: '&_tab='+_private.tab+'&_funcionExterna'+fn,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+tab+"formBuscarCliente").modal("show");                
                setTimeout(function(){vCliente.getGridBuscarCliente()},500);
                $("#"+tab+"formBuscarCliente .scroll-form").css('height','auto');  
            }
        });
    };           
    
    this.publico.getGridBuscarCliente = function (){
        var tab = _private.tab;
        var fn = _private.fn;
        var oTable = $("#"+tab+"gridClientesFound").dataTable({
            bFilter: true,
            sSearch: true,
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "1%",bSortable: false},                
                {sTitle: lang.generico.CLIENTE, sWidth: "70%"},
                {sTitle: lang.generico.ACCIONES, sWidth: "10%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "200px",
            sAjaxSource: _private.config.modulo+"getBuscarCliente",
            fnServerParams: function(aoData) {
                aoData.push({"name": "_tab", "value": tab});
                aoData.push({"name": "_funcionExterna", "value": fn});
            },
            fnDrawCallback: function() {
                $("#"+tab+"gridClientesFound_filter").find("input").attr("placeholder",'Buscar por Nombre o Empresa').css("width","300px").css("height","28px");
                simpleScript.enterSearch("#"+tab+"gridClientesFound",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#"+tab+'formBuscarCliente',
                    typeElement: "button"
                });               
                $("#"+tab+"gridClientesFound_info").css('margin-left','10px'); 
                $("#"+tab+"gridClientesFound_wrapper .dataTables_paginate").css('margin-right','10px'); 
                $('#'+tab+'gridClientesFound_refresh').css('padding','5px 10px');                              
           }
        });
        setup_widgets_desktop();
    };    
    
    return this.publico;
    
};
var vCliente = new vCliente_();