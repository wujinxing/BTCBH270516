/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 18-11-2014 22:11:42 
* Descripcion : vConsultaSaldo.js
* ---------------------------------------
*/
var vConsultaSaldo_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idVConsultaSaldo = 0;
    
    _private.config = {
        modulo: "ventas/vConsultaSaldo/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : VConsultaSaldo*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.VCSCL,
            label: $(element).attr("title"),
            fnCallback: function(){
                vConsultaSaldo.getContenido();
            }
        });
    };
    
    /*contenido de tab: VConsultaSaldo*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            fnCallback: function(data){
                $("#"+diccionario.tabs.VCSCL+"_CONTAINER").html(data);
                vConsultaSaldo.getGridVConsultaSaldo();
            }
        });
    };
    
    this.publico.getGridVConsultaSaldo = function (){
        var _f1 = $("#"+diccionario.tabs.VCSCL+"txt_f1").val();
        var _f2 = $("#"+diccionario.tabs.VCSCL+"txt_f2").val();        
        
        var oTable = $("#"+diccionario.tabs.VCSCL+"gridVConsultaSaldo").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "5%"},
                {sTitle: lang.generico.CLIENTE, sWidth: "40%"},  
                {sTitle: lang.generico.SUCURSAL, sWidth: "20%"},  
                {sTitle: lang.generico.SALDO, sWidth: "15%",  sClass: "right"}, 
                {sTitle: lang.generico.ACCIONES, sWidth: "10%", sClass: "center", bSortable: false} 
            ],
            aaSorting: [[3, "desc"]],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+"getGridVConsultaSaldo",
            fnServerParams: function(aoData) {
                aoData.push({"name": "_idSucursalGrid", "value": $("#"+diccionario.tabs.VCSCL+"lst_sucursalGrid").val()});
            },
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.VCSCL+"gridVConsultaSaldo_filter").find("input").attr("placeholder","Buscar por Cliente").css("width","200px");
                simpleScript.enterSearch("#"+diccionario.tabs.VCSCL+"gridVConsultaSaldo",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.VCSCL,
                    typeElement: "button"
                });               
           }
        });
        setup_widgets_desktop();
    };        
    
    this.publico.postPDFGeneral = function(){
        var s = $("#"+diccionario.tabs.VCSCL+"lst_sucursalGrid").val();
        simpleAjax.send({
            gifProcess:true,
            root: _private.config.modulo + 'postPDFGeneral',
            data: '&_idSucursalGrid='+s,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VCSCL+'btnDowPDF').off('click');
                    $('#'+diccionario.tabs.VCSCL+'btnDowPDF').attr("onclick","window.open('public/files/"+data.archivo+"','_blank');");
                    $('#'+diccionario.tabs.VCSCL+'btnDowPDF').click();
                }
                if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: 'Ocurrió un error al exportar Venta.'
                    });
                }
            }
        });
    };
    
    this.publico.postExcelGeneral = function(){
        var s = $("#"+diccionario.tabs.VCSCL+"lst_sucursalGrid").val();
        simpleAjax.send({
            gifProcess:true,
            root: _private.config.modulo + 'postExcelGeneral',
            data: '&_idSucursalGrid='+s,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VCSCL+'btnDowExcel').off('click');
                    $('#'+diccionario.tabs.VCSCL+'btnDowExcel').attr("onclick","window.open('public/files/"+data.archivo+"','_self');");
                    $('#'+diccionario.tabs.VCSCL+'btnDowExcel').click();
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
        var s = $("#"+diccionario.tabs.VCSCL+"lst_sucursalGrid").val();
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postPDF',
            data: '&_idPersona='+id+'&_idSucursalGrid='+s,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VCSCL+'btnDowPDF').off('click');
                    $('#'+diccionario.tabs.VCSCL+'btnDowPDF').attr("onclick","window.open('public/files/"+data.archivo+"','_blank');");
                    $('#'+diccionario.tabs.VCSCL+'btnDowPDF').click();
                }
            }
        });
    };    
    
    this.publico.postExcel = function(btn,id){
        var s = $("#"+diccionario.tabs.VCSCL+"lst_sucursalGrid").val();
        simpleAjax.send({
            element:btn,
            root: _private.config.modulo + 'postExcel',
            data: '&_idPersona='+id+'&_idSucursalGrid='+s,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VCSCL+'btnDowExcel').off('click');
                    $('#'+diccionario.tabs.VCSCL+'btnDowExcel').attr("onclick","window.open('public/files/"+data.archivo+"','_self');");
                    $('#'+diccionario.tabs.VCSCL+'btnDowExcel').click();
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
var vConsultaSaldo = new vConsultaSaldo_();