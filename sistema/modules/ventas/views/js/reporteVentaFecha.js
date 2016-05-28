/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 20-11-2014 00:11:17 
* Descripcion : reporteVentaFecha.js
* ---------------------------------------
*/
var reporteVentaFecha_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idReporteVentaFecha = 0;
    
    _private.config = {
        modulo: "ventas/reporteVentaFecha/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : ReporteVentaFecha*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.VRPT2,
            label: $(element).attr("title"),
            fnCallback: function(){
                reporteVentaFecha.getContenido();
            }
        });
    };
    
    /*contenido de tab: ReporteVentaFecha*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            fnCallback: function(data){
                $("#"+diccionario.tabs.VRPT2+"_CONTAINER").html(data);
                reporteVentaFecha.getGridReporteVentaFecha();
            }
        });
    };
    
    this.publico.getGridReporteVentaFecha = function (){
        var _f1 = $("#"+diccionario.tabs.VRPT2+"txt_f1").val();
        var _f2 = $("#"+diccionario.tabs.VRPT2+"txt_f2").val();  
        
        var oTable = $("#"+diccionario.tabs.VRPT2+"gridReporteVentaFecha").dataTable({
            bFilter: false, 
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
            aaSorting: [[1, "desc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridReporteVentaFecha",
             fnServerParams: function(aoData) {
                aoData.push({"name": "_idSucursalGrid", "value": $("#"+diccionario.tabs.VRPT2+"lst_sucursalGrid").val()});
                aoData.push({"name": "_f1", "value": _f1});                
                aoData.push({"name": "_f2", "value": _f2});
            },
            fnDrawCallback: function() {           
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.VRPT2,
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
            data: '&_idReporteVentaFecha='+id,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VRPT2+'btnDowPDF').off('click');
                    $('#'+diccionario.tabs.VRPT2+'btnDowPDF').attr("onclick","window.open('public/files/"+data.archivo+"','_blank');");
                    $('#'+diccionario.tabs.VRPT2+'btnDowPDF').click();
                }
            }
        });
    };
    
    this.publico.postExcel = function(btn,id){
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postExcel',
            data: '&_idReporteVentaFecha='+id,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VRPT2+'btnDowExcel').off('click');
                    $('#'+diccionario.tabs.VRPT2+'btnDowExcel').attr("onclick","window.open('public/files/"+data.archivo+"','_self');");
                    $('#'+diccionario.tabs.VRPT2+'btnDowExcel').click();
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
var reporteVentaFecha = new reporteVentaFecha_();