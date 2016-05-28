/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 07-04-2016 17:04:08 
* Descripcion : reporteVendedor.js
* ---------------------------------------
*/
var reporteVendedor_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idReporteVendedor = 0;
    _private.mapaSitio = "";
        
    _private.config = {
        modulo: "ventas/reporteVendedor/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : ReporteVendedor*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.VRPT4,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                reporteVendedor.getContenido();
            }
        });
    };
    
    /*contenido de tab: ReporteVendedor*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: "&_siteMap="+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.VRPT4+"_CONTAINER").html(data);
                reporteVendedor.getGridReporteVendedor();
            }
        });
    };
    
    this.publico.getGridReporteVendedor = function (){
        var f1 = $("#"+diccionario.tabs.VRPT4+"txt_f1").val();
        var f2 = $("#"+diccionario.tabs.VRPT4+"txt_f2").val();              
        
        var oTable = $("#"+diccionario.tabs.VRPT4+"gridReporteVendedor").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,   
            sSearch: false,
            bFilter: false,
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "1%",bSortable: false},               
                {sTitle: lang.generico.CODIGO, sWidth: "8%"},
                {sTitle: lang.generico.RESPONSABLE, sWidth: "25%"},
                {sTitle: lang.generico.TELEFONO, sWidth: "20%"},
                {sTitle: lang.generico.NROCOTIZACION, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "8%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridReporteVendedor",
            fnServerParams: function(aoData){
                aoData.push({"name": "_f1", "value": f1});                
                aoData.push({"name": "_f2", "value": f2}); 
            },
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.VRPT4+"gridReporteVendedor_filter").find("input").attr("placeholder","Buscar por ReporteVendedor").css("width","250px");
                simpleScript.enterSearch("#"+diccionario.tabs.VRPT4+"gridReporteVendedor",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.VRPT4,
                    typeElement: "button"
                });
            }
        });
        setup_widgets_desktop();
    };
    
   this.publico.postPDF = function(btn, idVendedor){
        
        var f1 = $("#"+diccionario.tabs.VRPT4+"txt_f1").val();
        var f2 = $("#"+diccionario.tabs.VRPT4+"txt_f2").val();              
        
        simpleAjax.send({
               element: btn,
               root: _private.config.modulo + 'postPDF',
               data: '&_f1='+f1+'&_f2='+f2+'&_idVendedor='+idVendedor,
               fnCallback: function(data) {
                   if(parseInt(data.result) === 1){
                       $('#'+diccionario.tabs.VRPT4+'btnDowPDF').off('click');
                       $('#'+diccionario.tabs.VRPT4+'btnDowPDF').attr("onclick","window.open('public/files/"+data.archivo+"','_blank');");
                       $('#'+diccionario.tabs.VRPT4+'btnDowPDF').click();
                   }
               }
           });
    };    
      
    this.publico.postExcel = function(btn,idVendedor){
        
        var f1 = $("#"+diccionario.tabs.VRPT4+"txt_f1").val();
        var f2 = $("#"+diccionario.tabs.VRPT4+"txt_f2").val();                      
        
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postExcel',
            data: '&_f1='+f1+'&_f2='+f2+'&_idVendedor='+idVendedor,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VRPT4+'btnDowExcel').off('click');
                    $('#'+diccionario.tabs.VRPT4+'btnDowExcel').attr("onclick","window.open('public/files/"+data.archivo+"','_self');");
                    $('#'+diccionario.tabs.VRPT4+'btnDowExcel').click();
                }
                if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: 'Ocurrió un error al exportar PDF.'
                    });
                }
            }
        });
    };      
      
    return this.publico;
    
};
var reporteVendedor = new reporteVendedor_();