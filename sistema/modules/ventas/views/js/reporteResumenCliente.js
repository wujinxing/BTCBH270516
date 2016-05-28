/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 17-05-2016 17:05:16 
* Descripcion : reporteResumenCliente.js
* ---------------------------------------
*/
var reporteResumenCliente_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idReporteResumenCliente = 0;
    _private.mapaSitio = "";
        
    _private.config = {
        modulo: "ventas/reporteResumenCliente/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : ReporteResumenCliente*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.VRPT5,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                reporteResumenCliente.getContenido();
            }
        });
    };
    
    /*contenido de tab: ReporteResumenCliente*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: "&_siteMap="+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.VRPT5+"_CONTAINER").html(data);
                reporteResumenCliente.getGridReporteResumenCliente();
            }
        });
    };
    
    this.publico.getGridReporteResumenCliente = function (){
        var oTable = $("#"+diccionario.tabs.VRPT5+"gridReporteResumenCliente").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "1%", bSortable: false},                
                {sTitle: lang.generico.ID, sWidth: "10%"},
                {sTitle: lang.generico.CLIENTE, sWidth: "35%"},
                {sTitle: lang.generico.CIUDAD, sWidth: "20%"},                
                {sTitle: lang.generico.NROVENTAS,  sClass: "right", sWidth: "10%"},
                {sTitle: lang.generico.ACCIONES, sWidth: "10%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridReporteResumenCliente",
            fnServerParams: function(aoData) {
                aoData.push({"name": "_fecha1", "value": $("#"+diccionario.tabs.VRPT5+"txt_f1").val()});                
                aoData.push({"name": "_fecha2", "value": $("#"+diccionario.tabs.VRPT5+"txt_f2").val()});
                aoData.push({"name": "_idSucursalGrid", "value": $("#"+diccionario.tabs.VRPT5+"lst_sucursalGrid").val()});
            },
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.VRPT5+"gridReporteResumenCliente_filter").find("input").attr("placeholder",lang.busqueda.VRPT5).css("width","200px");
                simpleScript.enterSearch("#"+diccionario.tabs.VRPT5+"gridReporteResumenCliente",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.VRPT5,
                    typeElement: "button"
                });
            }
        });
        setup_widgets_desktop();
    };
    
    this.publico.postExcelGeneral = function(){
        var f1 = $("#"+diccionario.tabs.VRPT5+"txt_f1").val();
        var f2 = $("#"+diccionario.tabs.VRPT5+"txt_f2").val();
        var s = $("#"+diccionario.tabs.VRPT5+"lst_sucursalGrid").val();        
        simpleAjax.send({
            gifProcess:true,
            root: _private.config.modulo + 'postExcelGeneral',
            data: '&_fecha1='+f1+'&_fecha2='+f2+'&_idSucursalGrid='+s,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VRPT5+'btnDowExcel').off('click');
                    $('#'+diccionario.tabs.VRPT5+'btnDowExcel').attr("onclick","window.open('public/files/"+data.archivo+"','_self');");
                    $('#'+diccionario.tabs.VRPT5+'btnDowExcel').click();
                }
                if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: 'Ocurrió un error al exportar Venta.'
                    });
                }
            }
        });
    };
    
    this.publico.postPDF = function(btn,id){
        var f1 = $("#"+diccionario.tabs.VRPT5+"txt_f1").val();
        var f2 = $("#"+diccionario.tabs.VRPT5+"txt_f2").val();     
        var s = $("#"+diccionario.tabs.VRPT5+"lst_sucursalGrid").val();
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postPDF',
            data: '&_idPersona='+id+'&_fecha1='+f1+'&_fecha2='+f2+'&_idSucursalGrid='+s,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VRPT5+'btnDowPDF').off('click');
                    $('#'+diccionario.tabs.VRPT5+'btnDowPDF').attr("onclick","window.open('public/files/"+data.archivo+"','_blank');");
                    $('#'+diccionario.tabs.VRPT5+'btnDowPDF').click();
                }
            }
        });
    };
           
    this.publico.postExcel = function(btn,id){
        var f1 = $("#"+diccionario.tabs.VRPT5+"txt_f1").val();
        var f2 = $("#"+diccionario.tabs.VRPT5+"txt_f2").val();
        var s = $("#"+diccionario.tabs.VRPT5+"lst_sucursalGrid").val();
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postExcel',
            data: '&_idPersona='+id+'&_fecha1='+f1+'&_fecha2='+f2+'&_idSucursalGrid='+s,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VRPT5+'btnDowExcel').off('click');
                    $('#'+diccionario.tabs.VRPT5+'btnDowExcel').attr("onclick","window.open('public/files/"+data.archivo+"','_self');");
                    $('#'+diccionario.tabs.VRPT5+'btnDowExcel').click();
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
var reporteResumenCliente = new reporteResumenCliente_();