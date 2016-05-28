/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 04-05-2016 17:05:37 
* Descripcion : reporteGraficoIngresosMes.js
* ---------------------------------------
*/
var reporteGraficoIngresosMes_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idReporteGraficoIngresosMes = 0;
    _private.mapaSitio = "";
        
    _private.config = {
        modulo: "ventas/reporteGraficoIngresosMes/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : ReporteGraficoIngresosMes*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.VRPT6,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                reporteGraficoIngresosMes.getContenido();
            }
        });
    };
    
    /*contenido de tab: ReporteGraficoIngresosMes*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: "&_siteMap="+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.VRPT6+"_CONTAINER").html(data);
                reporteGraficoIngresosMes.getGraficoChk();
            }
        });
    };
    
    this.publico.getReporteGrafico = function (){
        var mes = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];        
        var montoArray = {};
        var mesArray = {};
        var sigla;
        var colores = ['#FF0F00','#FF6600','#FF9E01','#FCD202','#F8FF01','#B0DE09','#04D215','#0D8ECF','#0D52D1','#2A0CD0','#8A0CCF','#CD0D74'];        
        
        var periodo =  $("#"+diccionario.tabs.VRPT6+"lst_periodo").val();
        var sucursal =  $("#"+diccionario.tabs.VRPT6+"lst_sucursalGrid").val();
        
        simpleAjax.send({            
            root: _private.config.modulo + 'getGrafico',  
            data: '&_periodo='+periodo+'&_idSucursalGrid='+sucursal,
            fnCallback: function(data) {
              sigla = data[0].sucursal;   
              for(var i in data){
                       montoArray[i] = data[i].monto;
                       mesArray[i] = data[i].mes;
                }
                var color = '',mees='',monto=0;
                var datos = '[';

                for(var m = 0;m<12;m++){
                    monto = 0;                             
                    mees = mes[m];
                    color = colores[m];
                    for(var i in montoArray){
                        if((m+1) == mesArray[i]){
                            monto = montoArray[i];
                        }
                    }

                    datos += '{\n\ ';
                    datos += '    mes: "'+mees+'",\n\ ';
                    datos += '    monto: '+monto+',\n\ ';
                    datos += '    color: "'+color+'"\n\ ';
                    datos += '}, ';
                }

                datos = datos.substring(0, datos.length-1);
                datos += ']';                
                var chartData = eval(datos);
                var chart = AmCharts.makeChart(diccionario.tabs.VRPT6+"chartdiv", {
                  "type": "serial",
                  "theme": "light",
                   titles: [{
                        "text": "Ventas mensuales del periodo: "+periodo+ " - SEVEND ["+sigla+"]",
                        "size": 20
                    }],
                  "marginRight": 70,
                  "dataProvider": chartData,
                  "valueAxes": [{
                    "axisAlpha": 0,
                    "position": "left",
                    "title": "Ingresos"
                  }],
                  "startDuration": 1,
                  "graphs": [{
                    "balloonText": "<span style='font-size:14px'>[[category]]: <b>S/ [[value]]</b></span>",
                    "bullet": "round",
                    "bulletSize": 8,         
                    "lineColor": "#901D78",
                    "lineThickness": 2,
                    "negativeLineColor": "#FF0F00",
                    "type": "smoothedLine",
                    "valueField": "monto"
                  }],
                "depth3D": 20,
                "angle": 30,
                "chartCursor": {
                        "categoryBalloonDateFormat": "YYYY",
                        "cursorAlpha": 0,
                        "valueLineEnabled":true,
                        "valueLineBalloonEnabled":true,
                        "valueLineAlpha":0.5,
                        "fullWidth":true
                    },
                  "categoryField": "mes",
                  "categoryAxis": {
                    "gridPosition": "start",
                    "labelRotation": 45
                  },                       
                  exportConfig: {
                        menuTop: "10px",
                        menuBottom: "auto",
                        menuRight: "15px",
                        backgroundColor: "#efefef",
                            menuItemOutput: {                                    
                                    fileName: 'Reporte grafico Ingresos SEVEND'                                
                            },
                            menuItemStyle: {
                                  backgroundColor: '#F1B8E6',
                                  rollOverBackgroundColor: '#D93CB9'
                            },
                            menuItems: [{
                                    textAlign: 'center',
                                    icon: 'public/js/amcharts/images/export.png',
                                    iconTitle: 'Guardar como imagen',
                                    format: 'jpg'
                                }]
                    }

                });
                
            }
        });                        
    };
    
    this.publico.getReporteGraficoBarras = function (){
        var mes = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];        
        var montoArray = {};
        var mesArray = {};
        var sigla;
        var colores = ['#FF0F00','#FF6600','#FF9E01','#FCD202','#F8FF01','#B0DE09','#04D215','#0D8ECF','#0D52D1','#2A0CD0','#8A0CCF','#CD0D74'];        
        
        var periodo =  $("#"+diccionario.tabs.VRPT6+"lst_periodo").val();
        var sucursal =  $("#"+diccionario.tabs.VRPT6+"lst_sucursalGrid").val();
        
        simpleAjax.send({            
            root: _private.config.modulo + 'getGrafico',  
            data: '&_periodo='+periodo+'&_idSucursalGrid='+sucursal,
            fnCallback: function(data) {
              sigla = data[0].sucursal;   
              for(var i in data){
                       montoArray[i] = data[i].monto;
                       mesArray[i] = data[i].mes;
                }
                var color = '',mees='',monto=0;
                var datos = '[';

                for(var m = 0;m<12;m++){
                    monto = 0;                             
                    mees = mes[m];
                    color = colores[m];
                    for(var i in montoArray){
                        if((m+1) == mesArray[i]){
                            monto = montoArray[i];
                        }
                    }

                    datos += '{\n\ ';
                    datos += '    mes: "'+mees+'",\n\ ';
                    datos += '    monto: '+monto+',\n\ ';
                    datos += '    color: "'+color+'"\n\ ';
                    datos += '}, ';
                }

                datos = datos.substring(0, datos.length-1);
                datos += ']';                
                var chartData = eval(datos);
                var chart = AmCharts.makeChart(diccionario.tabs.VRPT6+"chartdiv", {
                  "type": "serial",
                  "theme": "light",
                   titles: [{
                        "text": "Ingresos mensuales del periodo: "+periodo+ " - SEVEND ["+sigla+"]",
                        "size": 20
                    }],
                  "marginRight": 70,
                  "dataProvider": chartData,
                  "valueAxes": [{
                    "axisAlpha": 0,
                    "position": "left",
                    "title": "Ingresos"
                  }],
                  "startDuration": 1,
                  "graphs": [{
                    "balloonText": "<span style='font-size:14px'>[[category]]: <b>S/ [[value]]</b></span>",
                    "fillColorsField": "color",
                    "fillAlphas": 0.9,
                    "lineAlpha": 0.1,
                    "type": "column",                    
                    "valueField": "monto"
                  }],
                "depth3D": 20,
                "angle": 30,
                "chartCursor": {
                        "categoryBalloonDateFormat": "YYYY",
                        "cursorAlpha": 0,
                        "valueLineEnabled":true,
                        "valueLineBalloonEnabled":true,
                        "valueLineAlpha":0.5,
                        "fullWidth":true
                    },
                  "categoryField": "mes",
                  "categoryAxis": {
                    "gridPosition": "start",
                    "labelRotation": 45
                  },                       
                  exportConfig: {
                        menuTop: "10px",
                        menuBottom: "auto",
                        menuRight: "15px",
                        backgroundColor: "#efefef",
                            menuItemOutput: {                                    
                                    fileName: 'Reporte grafico Ingresos SEVEND'                                
                            },
                            menuItemStyle: {
                                  backgroundColor: '#F1B8E6',
                                  rollOverBackgroundColor: '#D93CB9'
                            },
                            menuItems: [{
                                    textAlign: 'center',
                                    icon: 'public/js/amcharts/images/export.png',
                                    iconTitle: 'Guardar como imagen',
                                    format: 'jpg'
                                }]
                    }

                });
                
            }
        });                        
    };
    
    this.publico.getGraficoChk = function(){
        var chk = $('#'+diccionario.tabs.VRPT6+'chk_graf').is(':checked');
        if (chk ){
          reporteGraficoIngresosMes.getReporteGraficoBarras();
        }else{
          reporteGraficoIngresosMes.getReporteGrafico();
        }               
    };
    
    this.publico.postPDF = function(){
        var periodo =  $("#"+diccionario.tabs.VRPT6+"lst_periodo").val();
        var sucursal =  $("#"+diccionario.tabs.VRPT6+"lst_sucursalGrid").val();
        simpleAjax.send({            
            gifProcess:true,
            root: _private.config.modulo + 'postPDF',
            data: '&_periodo='+periodo+'&_idSucursalGrid='+sucursal,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VRPT6+'btnDowPDF').off('click');
                    $('#'+diccionario.tabs.VRPT6+'btnDowPDF').attr("onclick","window.open('public/files/"+data.archivo+"','_blank');");
                    $('#'+diccionario.tabs.VRPT6+'btnDowPDF').click();
                }
            }
        });
    };
      
    return this.publico;
    
};
var reporteGraficoIngresosMes = new reporteGraficoIngresosMes_();