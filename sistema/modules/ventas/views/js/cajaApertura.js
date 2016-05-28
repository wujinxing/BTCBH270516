/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 06-12-2014 21:12:27 
* Descripcion : cajaApertura.js
* ---------------------------------------
*/
var cajaApertura_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idCajaApertura = 0;
    _private.mapaSitio = '';
    _private.config = {
        modulo: "ventas/cajaApertura/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : CajaApertura*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.CAJAA,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                cajaApertura.getContenido();
            }
        });
    };
    
    /*contenido de tab: CajaApertura*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.CAJAA+"_CONTAINER").html(data);
                cajaApertura.getGridCajaApertura();
            }
        });
    };
    
    this.publico.getGridCajaApertura = function (){
        var oTable = $("#"+diccionario.tabs.CAJAA+"gridCajaApertura").dataTable({
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
                {sTitle: lang.generico.FECHAHORA, sWidth: "12%"},
                {sTitle: lang.generico.SUCURSAL, sWidth: "8%", sClass: "center"},
                {sTitle: lang.generico.MONEDA, sWidth: "5%", sClass: "center"},
                {sTitle: lang.generico.INICIAL, sWidth: "10%", sClass: "right"},
                {sTitle: lang.generico.INGRESOS, sWidth: "10%", sClass: "right"},
                {sTitle: lang.generico.EGRESOS, sWidth: "10%", sClass: "right"},
                {sTitle: lang.generico.SALDO, sWidth: "10%", sClass: "right"},
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "5%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[0, "desc"]],
            sScrollY: "350px",
            sAjaxSource: _private.config.modulo+"getGridCajaApertura",
            fnServerParams: function(aoData) {
                aoData.push({"name": "_fecha1", "value": $("#"+diccionario.tabs.CAJAA+"txt_f1").val()});                
                aoData.push({"name": "_fecha2", "value": $("#"+diccionario.tabs.CAJAA+"txt_f2").val()});
                aoData.push({"name": "_idSucursalGrid", "value": $("#"+diccionario.tabs.CAJAA+"lst_sucursalGrid").val()});
            },
            fnDrawCallback: function() {                                
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.CAJAA,
                    typeElement: "button"
                });               
           }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewCajaApertura = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewCajaApertura",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.CAJAA+"formNewCajaApertura").modal("show");
            }
        });
    };    
    
    this.publico.getFormEditCajaApertura = function(btn,id,mon){
        _private.idCajaApertura = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditCajaApertura",
            data: "&_idCajaApertura="+_private.idCajaApertura+'&_moneda='+mon,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.CAJAA+"formEditCajaApertura").modal("show");
            }
        });
    };
    
    this.publico.postNewCajaApertura = function(){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_21,
            callbackSI: function(){
                simpleAjax.send({
                    element: "#"+diccionario.tabs.CAJAA+"btnGrCajaApertura",                    
                    root: _private.config.modulo + 'postNewCajaApertura',
                    form: "#"+diccionario.tabs.CAJAA+"formNewCajaApertura",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_20,
                                callback: function(){        
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
                                    simpleScript.reloadGrid("#"+diccionario.tabs.CAJAA+"gridCajaApertura");   
                                    simpleScript.closeModal("#"+diccionario.tabs.CAJAA+"formNewCajaApertura");
                                }
                            });
                       }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                            simpleScript.notify.warning({
                                content: "La Caja ya fue Aperturada, debe de cerrar Caja para volver a Aperturar."
                            });
                        }
                    }
                });
            }
        });
    };
    
    this.publico.postEditCajaApertura = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.CAJAA+"btnEdCajaApertura",
            root: _private.config.modulo + "postEditCajaApertura",
            form: "#"+diccionario.tabs.CAJAA+"formEditCajaApertura",
            data: "&_idCajaApertura="+_private.idCajaApertura,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idCajaApertura = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.CAJAA+"formEditCajaApertura");
                            simpleScript.reloadGrid("#"+diccionario.tabs.CAJAA+"gridCajaApertura");
                        }
                    });
                }
            }
        });
    };        
    
    this.publico.getCaja = function(obj){
        simpleAjax.send({
            gifProcess: true,
            root: _private.config.modulo + 'getDataCajaAllCombo',
            data: '&_idSucursalCombo='+obj.idSucursal,
            fnCallback: function(data){
                simpleScript.listBox({
                    data: data,
                    optionSelec: false,                    
                    icon:true,    
                    content: obj.content,
                    encript: true,
                    attr:{
                        id: obj.idElement,
                        name: obj.nameElement
                    },
                    dataView:{
                        etiqueta: 'descripcion',
                        value: 'id_caja'
                    }
                });
            }
        });
    };    
    
    this.publico.getCajaSaldo = function(obj){
        simpleAjax.send({
            gifProcess: true,
            root: _private.config.modulo + 'getUltimoSaldoCombo',
            data: '&_idSucursalCombo='+obj.idSucursal,
            fnCallback: function(data){   
                var saldo = data.saldo;
                if (isNaN(saldo)) saldo = 0;
                
                $(obj.idElement).val( parseFloat(saldo).toFixed(2));                              
            }
        });
    };  
    
    return this.publico;
    
};
var cajaApertura = new cajaApertura_();