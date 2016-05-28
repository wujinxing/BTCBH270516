/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 20-05-2016 18:05:40 
* Descripcion : reporteBeneficiario.js
* ---------------------------------------
*/
var reporteBeneficiario_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idReporteBeneficiario = 0;
    _private.mapaSitio = "";
        
    _private.config = {
        modulo: "ventas/reporteBeneficiario/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : ReporteBeneficiario*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.VRPT7,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                reporteBeneficiario.getContenido();
            }
        });
    };
    
    /*contenido de tab: ReporteBeneficiario*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: "&_siteMap="+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.VRPT7+"_CONTAINER").html(data);
                reporteBeneficiario.getGridReporteBeneficiario();
            }
        });
    };
    
    this.publico.getGridReporteBeneficiario = function (){
        var oTable = $("#"+diccionario.tabs.VRPT7+"gridReporteBeneficiario").dataTable({
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
                {sTitle: lang.generico.ID, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.SUCURSAL, sWidth: "10%"},
                {sTitle: lang.generico.CONCEPTO, sWidth: "35%"},
                {sTitle: lang.generico.FECHA, sWidth: "10%",  sClass: "center"},
                {sTitle: lang.generico.TOTAL, sWidth: "15%",  sClass: "right"},
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center"}
            ],
            aaSorting: [[3, "desc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridReporteBeneficiario",
            fnServerParams: function(aoData) {
                aoData.push({"name": "_fecha1", "value": $("#"+diccionario.tabs.VRPT7+"txt_f1").val()});                
                aoData.push({"name": "_fecha2", "value": $("#"+diccionario.tabs.VRPT7+"txt_f2").val()});
                aoData.push({"name": "_idSucursalGrid", "value": $("#"+diccionario.tabs.VRPT7+"lst_sucursalGrid").val()});
                aoData.push({"name": "_idBeneficiario", "value": $("#"+diccionario.tabs.VRPT7+"lst_beneficiario").val()});
            },
            fnDrawCallback: function() {                                
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.VRPT7,
                    typeElement: "button"
                });
            }
        });
        setup_widgets_desktop();
    };
    
    this.publico.postPDFGeneral = function(){
                          
        var f1 = $("#"+diccionario.tabs.VRPT7+"txt_f1").val();
        var f2 = $("#"+diccionario.tabs.VRPT7+"txt_f2").val();     
        var s = $("#"+diccionario.tabs.VRPT7+"lst_sucursalGrid").val();
        var id = $("#"+diccionario.tabs.VRPT7+"lst_beneficiario").val();
        simpleAjax.send({
            gifProcess:true,
            root: _private.config.modulo + 'postPDFGeneral',
            data: '&_idBeneficiario='+id+'&_fecha1='+f1+'&_fecha2='+f2+'&_idSucursalGrid='+s,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VRPT7+'btnDowPDF').off('click');
                    $('#'+diccionario.tabs.VRPT7+'btnDowPDF').attr("onclick","window.open('public/files/"+data.archivo+"','_blank');");
                    $('#'+diccionario.tabs.VRPT7+'btnDowPDF').click();
                }
            }
        });
    };
           
    this.publico.postExcelGeneral = function(){
        var f1 = $("#"+diccionario.tabs.VRPT7+"txt_f1").val();
        var f2 = $("#"+diccionario.tabs.VRPT7+"txt_f2").val();     
        var s = $("#"+diccionario.tabs.VRPT7+"lst_sucursalGrid").val();
        var id = $("#"+diccionario.tabs.VRPT7+"lst_beneficiario").val();
        simpleAjax.send({
            gifProcess:true,
            root: _private.config.modulo + 'postExcelGeneral',
            data: '&_idPersona='+id+'&_fecha1='+f1+'&_fecha2='+f2+'&_idSucursalGrid='+s,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VRPT7+'btnDowExcel').off('click');
                    $('#'+diccionario.tabs.VRPT7+'btnDowExcel').attr("onclick","window.open('public/files/"+data.archivo+"','_self');");
                    $('#'+diccionario.tabs.VRPT7+'btnDowExcel').click();
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
var reporteBeneficiario = new reporteBeneficiario_();