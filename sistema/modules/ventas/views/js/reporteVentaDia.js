/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 19-11-2014 22:11:59 
* Descripcion : reporteVentaDia.js
* ---------------------------------------
*/
var reporteVentaDia_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idReporteVentaDia = 0;
    _private.dataServer = {};
    _private.config = {
        modulo: "ventas/reporteVentaDia/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : ReporteVentaDia*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.VRPT1,
            label: $(element).attr("title"),
            fnCallback: function(){
                reporteVentaDia.getContenido();
            }
        });
    };
    
    /*contenido de tab: ReporteVentaDia*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            fnCallback: function(data){
                $("#"+diccionario.tabs.VRPT1+"_CONTAINER").html(data);     
                var id = $('#'+diccionario.tabs.VRPT1+'lst_caja').val();
                reporteVentaDia.getDatosContenido(id);
            }
        });
    };
    
    
    this.publico.getDatosContenido = function(id){
        simpleAjax.send({
            dataType: "html",
            gifProcess:true,
            root: _private.config.modulo+'getFormDatosCaja',
            data: '&_idCaja='+id,
            fnCallback: function(data){
                $("#"+diccionario.tabs.VRPT1+"cont-caja").html(data);                
            }
        });
    };
    
    this.publico.postPDF = function(btn,id){
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postPDF',
            data: '&_idCaja='+id,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VRPT1+'btnDowPDF').off('click');
                    $('#'+diccionario.tabs.VRPT1+'btnDowPDF').attr("onclick","window.open('public/files/"+data.archivo+"','_blank');");
                    $('#'+diccionario.tabs.VRPT1+'btnDowPDF').click();
                }
            }
        });
    };
           
    this.publico.postExcel = function(btn,id){
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postExcel',
            data: '&_idCaja='+id,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VRPT1+'btnDowExcel').off('click');
                    $('#'+diccionario.tabs.VRPT1+'btnDowExcel').attr("onclick","window.open('public/files/"+data.archivo+"','_self');");
                    $('#'+diccionario.tabs.VRPT1+'btnDowExcel').click();
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
var reporteVentaDia = new reporteVentaDia_();