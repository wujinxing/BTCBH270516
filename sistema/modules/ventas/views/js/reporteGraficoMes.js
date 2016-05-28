/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 20-11-2014 17:11:16 
* Descripcion : reporteGraficoMes.js
* ---------------------------------------
*/
var reporteGraficoMes_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idReporteGraficoMes = 0;
    
    _private.config = {
        modulo: "ventas/reporteGraficoMes/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : ReporteGraficoMes*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.VRPT3,
            label: $(element).attr("title"),
            fnCallback: function(){
                reporteGraficoMes.getContenido();
            }
        });
    };
    
    /*contenido de tab: ReporteGraficoMes*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            fnCallback: function(data){
                $("#"+diccionario.tabs.VRPT3+"_CONTAINER").html(data);
                reporteGraficoMes.getGraficoChk();
            }
        });
    };
    
    this.publico.getReporteGraficoMes = function (){
        var montoArray = {};
        var fechaArray = {}; 
        var sigla;
        
        var periodo =  $("#"+diccionario.tabs.VRPT3+"lst_periodo").val();
        var sucursal =  $("#"+diccionario.tabs.VRPT3+"lst_sucursalGrid").val();
        var mes =  $("#"+diccionario.tabs.VRPT3+"lst_mes").val();
        simpleAjax.send({            
            root: _private.config.modulo + 'getGrafico',  
            data: '&_periodo='+periodo+'&_idSucursalGrid='+sucursal+'&_mes='+mes,
            fnCallback: function(data) {      
              if(data[0] !== undefined ){
                var color = '', j=0;
                var datos = '[';
                for(var i in data){
                      montoArray[i] = data[i].monto;
                      fechaArray[i] = data[i].fecha;    
                      j = i;                      
                      j++;
                      if( j== 12) j=1;
                      datos += '{\n\ ';
                      datos += '    mes: "'+fechaArray[i]+'",\n\ ';
                      datos += '    monto: '+montoArray[i]+',\n\ ';                      
                      datos += '}, ';

                  }
                  sigla = data[0].sucursal; 
                  datos = datos.substring(0, datos.length-1);
                  datos += ']';
                  var chart;

                  var chartData = eval(datos);
                  var chart = AmCharts.makeChart(diccionario.tabs.VRPT3+"chartdiv", {
                    "type": "serial",
                    "theme": "light",
                     titles: [{
                          "text": "Saldo de caja de "+simpleScript.nombreMes(mes)+" del "+periodo+" - SEVEND ["+sigla+"]",
                          "size": 20
                      }],
                    "marginRight": 70,
                    "dataProvider": chartData,
                    "valueAxes": [{
                      "axisAlpha": 0,
                      "position": "left",
                      "title": "Ganancia"
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
                                      fileName: 'Reporte grafico SEVEND'                                
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
               }else{
                    simpleScript.notify.warning({
                        content: lang.mensajes.MSG_47
                    });                   
               }
            }
        });                        
    };
    
    this.publico.getReporteGraficoMesBarras = function (){
        var montoArray = {};
        var fechaArray = {}; 
        var sigla;
        var colores = ['#FF0F00','#FF6600','#FF9E01','#FCD202','#F8FF01','#B0DE09','#04D215','#0D8ECF','#0D52D1','#2A0CD0','#8A0CCF','#CD0D74'];        
        
        var periodo =  $("#"+diccionario.tabs.VRPT3+"lst_periodo").val();
        var sucursal =  $("#"+diccionario.tabs.VRPT3+"lst_sucursalGrid").val();
        var mes =  $("#"+diccionario.tabs.VRPT3+"lst_mes").val();
        simpleAjax.send({            
            root: _private.config.modulo + 'getGrafico',  
            data: '&_periodo='+periodo+'&_idSucursalGrid='+sucursal+'&_mes='+mes,
            fnCallback: function(data) {
              if(data[0] !== undefined ){
                    var color = '', j=0;
                    var datos = '[';
                    var j=1;
                    for(var i in data){
                          montoArray[i] = data[i].monto;
                          fechaArray[i] = data[i].fecha;                              
                          color = colores[j]; 
                          j++;
                          if( j == 12) j=1;
                          datos += '{\n\ ';
                          datos += '    mes: "'+fechaArray[i]+'",\n\ ';
                          datos += '    monto: '+montoArray[i]+',\n\ ';
                          datos += '    color: "'+color+'"\n\ ';
                          datos += '}, ';

                      }
                    sigla = data[0].sucursal; 
                    datos = datos.substring(0, datos.length-1);
                    datos += ']';
                    var chart;

                    var chartData = eval(datos);
                    var chart = AmCharts.makeChart(diccionario.tabs.VRPT3+"chartdiv", {
                      "type": "serial",
                      "theme": "light",
                       titles: [{
                            "text": "Ganancia mensual del periodo: "+periodo+" - SEVEND ["+sigla+"]",
                            "size": 20
                        }],
                      "marginRight": 70,
                      "dataProvider": chartData,
                      "valueAxes": [{
                        "axisAlpha": 0,
                        "position": "left",
                        "title": "Ganancia"
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
                                        fileName: 'Reporte grafico SEVEND'                                
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
            }
        });                        
    };
  
   this.publico.getGraficoChk = function(){
        var chk = $('#'+diccionario.tabs.VRPT3+'chk_graf').is(':checked');
        if (chk ){
          reporteGraficoMes.getReporteGraficoMesBarras();
        }else{
          reporteGraficoMes.getReporteGraficoMes();
        }               
    };
    
    this.publico.postPDF = function(){
        var periodo =  $("#"+diccionario.tabs.VRPT3+"lst_periodo").val();
        var sucursal =  $("#"+diccionario.tabs.VRPT3+"lst_sucursalGrid").val();
        var mes =  $("#"+diccionario.tabs.VRPT3+"lst_mes").val();
        simpleAjax.send({            
            gifProcess:true,
            root: _private.config.modulo + 'postPDF',
            data: '&_periodo='+periodo+'&_idSucursalGrid='+sucursal+'&_mes='+mes,
            fnCallback: function(data) {
                if(parseInt(data.result) === 1){
                    $('#'+diccionario.tabs.VRPT3+'btnDowPDF').off('click');
                    $('#'+diccionario.tabs.VRPT3+'btnDowPDF').attr("onclick","window.open('public/files/"+data.archivo+"','_blank');");
                    $('#'+diccionario.tabs.VRPT3+'btnDowPDF').click();
                }
            }
        });
    };
    
    return this.publico;
    
};
var reporteGraficoMes = new reporteGraficoMes_();