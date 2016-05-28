/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 22-11-2014 20:11:18 
* Descripcion : vegresos.js
* ---------------------------------------
*/
var vegresos_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idVegresos = 0;
    _private.mapaSitio = '';
    
    _private.config = {
        modulo: "ventas/vegresos/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : Vegresos*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.VEGRE,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                vegresos.getContenido();
            }
        });
    };
    
    /*contenido de tab: Vegresos*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.VEGRE+"_CONTAINER").html(data);
                vegresos.getGridVegresos();
            }
        });
    };
    
    this.publico.getGridVegresos = function (){
        var oTable = $("#"+diccionario.tabs.VEGRE+"gridVegresos").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.ID, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.SUCURSAL, sWidth: "8%"},
                {sTitle: lang.generico.CONCEPTO, sWidth: "35%"},
                {sTitle: lang.generico.FECHA, sWidth: "10%",  sClass: "center"},
                {sTitle: lang.generico.TOTAL, sWidth: "15%",  sClass: "right"},
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "10%", sClass: "center", bSortable: false}                           
            ],
            aaSorting: [[0, "desc"]],
            sScrollY: "350px",
            sAjaxSource: _private.config.modulo+"getGridVegresos",
            fnServerParams: function(aoData) {
                aoData.push({"name": "_fecha1", "value": $("#"+diccionario.tabs.VEGRE+"txt_f1").val()});                
                aoData.push({"name": "_fecha2", "value": $("#"+diccionario.tabs.VEGRE+"txt_f2").val()});
                aoData.push({"name": "_idSucursalGrid", "value": $("#"+diccionario.tabs.VEGRE+"lst_sucursalGrid").val()});
            },
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.VEGRE+"gridVegresos_filter").find("input").attr("placeholder","Buscar por Concepto").css("width","200px");
                simpleScript.enterSearch("#"+diccionario.tabs.VEGRE+"gridVegresos",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.VEGRE,
                    typeElement: "button"
                });                
           }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewVegresos = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewVegresos",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.VEGRE+"formNewVegresos").modal("show");
            }
        });
    };
    
    this.publico.getFormEditVegresos = function(btn,id){
        _private.idVegresos = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditVegresos",
            data: "&_idVegresos="+_private.idVegresos,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.VEGRE+"formEditVegresos").modal("show");
            }
        });
    };
    
    this.publico.postNewVegresos = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.VEGRE+"btnGrVegresos",
            root: _private.config.modulo + "postNewVegresos",
            form: "#"+diccionario.tabs.VEGRE+"formNewVegresos",
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3                        
                    });
                    simpleScript.reloadGrid("#"+diccionario.tabs.VEGRE+"gridVegresos");
                    
                    $('#'+diccionario.tabs.VEGRE+'txt_descripcion').val('');
                    $('#'+diccionario.tabs.VEGRE+'txt_monto').val('');
                    $("#"+diccionario.tabs.VEGRE+"lst_beneficiario").select2("val", "");
                    
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: "No existe caja. Por favor revisar."
                    });
                }
            }
        });
    };
    
    this.publico.postEditVegresos = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.VEGRE+"btnEdVegresos",
            root: _private.config.modulo + "postEditVegresos",
            form: "#"+diccionario.tabs.VEGRE+"formEditVegresos",
            data: "&_idVegresos="+_private.idVegresos,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idVegresos = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.VEGRE+"formEditVegresos");
                            simpleScript.reloadGrid("#"+diccionario.tabs.VEGRE+"gridVegresos");
                        }
                    });
                }
            }
        });
    };
        
    this.publico.postDeleteVegresos = function(btn, idx){
        
         simpleScript.notify.confirm({
            content: lang.mensajes.MSG_13,
            callbackSI: function(){
                simpleAjax.send({
                    element: btn,
                    data: "&_idVegresos="+idx,
                    root: _private.config.modulo + "postDeleteVegresos",
                    fnCallback: function(data){
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_17,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.VEGRE+"gridVegresos");
                                }
                            });
                        }
                    }
                });
            }
        });              
    };
    
    this.publico.getBeneficiario = function(obj){
        simpleAjax.send({
            gifProcess: true,
            root: _private.config.modulo + 'getFindDataBeneficiarioAllCombo',
            data: '&_idSucursalCombo='+obj.idSucursal,
            fnCallback: function(data){
                simpleScript.listBox({
                    data: data,
                    optionSelec: true,                    
                    icon:false,    
                    content: obj.content,
                    encript: true,
                    attr:{
                        id: obj.idElement,
                        name: obj.nameElement,
                        onchange: obj.onchange
                    },
                    dataView:{
                        etiqueta: 'persona',
                        value: 'id_persona',
                        group: 'tipo'
                    }
                });                
                simpleScript.chosen({'id':'#'+obj.idElement});
            }
        });
    };    
    
    return this.publico;
    
};
var vegresos = new vegresos_();