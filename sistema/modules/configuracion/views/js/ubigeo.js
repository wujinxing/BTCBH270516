/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 17-12-2014 00:12:57 
* Descripcion : ubigeo.js
* ---------------------------------------
*/
var ubigeo_ = function(){
    
    /*metodos privados*/
    var _private = {};
        
    _private.idPais = 0;
    _private.idDepartamento = 0;
    _private.idUbigeo = 0;
    
    _private.tituloPais = '';
    _private.tituloDepartamento = '';
    _private.tituloUbigeo = '';
    _private.mapaSitio = '';
    
    _private.config = {
        modulo: "configuracion/ubigeo/"
    };

    /*metodos publicos*/
    this.publico = {};       
    
    this.publico.getIdPais = function(){
        return _private.idPais;
    };
    this.publico.getIdDepartamento = function(){
        return _private.idDepartamento;
    };
    this.publico.getIdUbigeo = function(){
        return _private.idUbigeo;
    };
    
    /*crea tab : Ubigeo*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.UBIG,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                ubigeo.getContenido();
            }
        });
    };
    
    /*contenido de tab: Ubigeo*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.UBIG+"_CONTAINER").html(data);
                ubigeo.getGridPais();
            }
        });
    };       
    
    this.publico.getGridPais = function (){
        var oTable = $("#"+diccionario.tabs.UBIG+"gridPais").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 50,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "1%",bSortable: false},               
                {sTitle: lang.generico.FLAG, sWidth: "3%",bSortable: false},
                {sTitle: lang.generico.PAIS, sWidth: "20%"},
                {sTitle: lang.generico.CONTINENTE, sWidth: "15%"},
                {sTitle: lang.generico.ISO2, sWidth: "5%", sClass: "center", bSortable: false},
                {sTitle: lang.generico.ISO3, sWidth: "5%", sClass: "center", bSortable: false},
                {sTitle: lang.generico.MONEDA, sWidth: "10%", sClass: "center", bSortable: false},
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center", bSortable: false},
                {sTitle: lang.generico.ACCIONES, sWidth: "10%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "130px",
            sAjaxSource: _private.config.modulo+"getGridPais",
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.UBIG+"gridPais_filter").find("input").attr("placeholder",lang.busqueda.CATPR1).css("width","250px");
                simpleScript.enterSearch("#"+diccionario.tabs.UBIG+"gridPais",oTable);                                                
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.UBIG+'pais',
                    typeElement: "button"
                });             
            }            
        });
        setup_widgets_desktop();
    };
    
    
    this.publico.getGridDepartamento = function (){                
        if (simpleScript.getParam(arguments[0]) !== false){
            ubigeo.resetPais();
            _private.idPais = simpleScript.getParam(arguments[0]);  
            _private.tituloPais = simpleScript.getParam(arguments[1]); 
            if (_private.tituloPais == false) _private.tituloPais = '';
        }                                
        
        var oTable = $("#"+diccionario.tabs.UBIG+"gridDepartamento").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 25,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "1%",bSortable: false},       
                {sTitle: lang.generico.CODIGO, sWidth: "10%"},
                {sTitle: lang.generico.DEPARTAMENTO, sWidth: "20%"},                
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center", bSortable: false},
                {sTitle: lang.generico.ACCIONES, sWidth: "10%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "130px",
            sAjaxSource: _private.config.modulo+"getGridDepartamento",
            fnServerParams: function(aoData) {
                aoData.push({"name": "_idPais", "value": _private.idPais});                
            },
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.UBIG+"gridDepartamento_filter").find("input").attr("placeholder",lang.busqueda.BUSCAR).css("width","150px");
                simpleScript.enterSearch("#"+diccionario.tabs.UBIG+"gridDepartamento",oTable);
                
                var texto = lang.generico.DEPARTAMENTODE +' '+_private.tituloPais;
                $('#widget_'+diccionario.tabs.UBIG+'departamento header h2').html(texto);                                                                                
                
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.UBIG+'departamento',
                    typeElement: "button"
                });               
           }
        });
        setup_widgets_desktop();                
    };
      
    this.publico.getGridUbigeo= function (){
        if (simpleScript.getParam(arguments[0]) !== false){
            ubigeo.resetDepartamento();
            _private.idDepartamento = simpleScript.getParam(arguments[0]);  
            _private.tituloDepartamento = simpleScript.getParam(arguments[1]);  
            if (_private.tituloDepartamento == false) _private.tituloDepartamento = '';
        }
        var oTable = $("#"+diccionario.tabs.UBIG+"gridUbigeo").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 100,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "1%",bSortable: false},    
                {sTitle: lang.generico.CODIGO, sWidth: "10%"},
                {sTitle: lang.generico.CIUDAD, sWidth: "20%"},                
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "10%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "130px",
            sAjaxSource: _private.config.modulo+"getGridUbigeo",
            fnServerParams: function(aoData) {
                aoData.push({"name": "_idDepartamento", "value": _private.idDepartamento});                
            },
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.UBIG+"gridUbigeo_filter").find("input").attr("placeholder",lang.busqueda.BUSCAR).css("width","150px");
                simpleScript.enterSearch("#"+diccionario.tabs.UBIG+"gridUbigeo",oTable);
                
                var texto = lang.generico.DISTRITODE +' '+_private.tituloDepartamento;
                $('#widget_'+diccionario.tabs.UBIG+'ubigeo header h2').html(texto);                                                                                
                                                                                 
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.UBIG+'ubigeo',
                    typeElement: "button"
                });              
           }
        });
        setup_widgets_desktop();                
    }; 
    
    this.publico.getFormNewUbigeo = function(){
        if (_private.idDepartamento == 0){
            simpleScript.notify.warning({
                content: lang.mensajes.MSG_31
            });
            return false;
        }                   
        simpleAjax.send({            
            dataType: "html",
            root: _private.config.modulo + "getFormNewUbigeo",
            data: "&_idDepartamento="+_private.idDepartamento,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.UBIG+"formNewUbigeo").modal("show");
            }
        });
        
    };
    
    this.publico.getFormEditUbigeo = function(btn,id){
        _private.idUbigeo = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditUbigeo",
            data: "&_idUbigeo="+_private.idUbigeo+"&_idDepartamento="+_private.idDepartamento,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.UBIG+"formEditUbigeo").modal("show");
            }
        });
    };
    
    this.publico.postNewUbigeo = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.UBIG+"btnGrUbigeo",
            root: _private.config.modulo + "postNewUbigeo",
            form: "#"+diccionario.tabs.UBIG+"formNewUbigeo",
            data: "&_idDepartamento="+_private.idDepartamento,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            simpleScript.reloadGrid("#"+diccionario.tabs.UBIG+"gridUbigeo");
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.mensajes.MSG_4
                    });
                }
            }
        });
    };
    
    this.publico.postEditUbigeo = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.UBIG+"btnEdUbigeo",
            root: _private.config.modulo + "postEditUbigeo",
            form: "#"+diccionario.tabs.UBIG+"formEditUbigeo",
            data: "&_idUbigeo="+_private.idUbigeo,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){                            
                            simpleScript.closeModal("#"+diccionario.tabs.UBIG+"formEditUbigeo");
                            simpleScript.reloadGrid("#"+diccionario.tabs.UBIG+"gridUbigeo");
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.mensajes.MSG_4
                    });
                }
            }
        });
    };
    
    this.publico.postDeleteUbigeo = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({                    
                    element: btn,
                    gifProcess: true,
                    data: "&_idUbigeo="+id,
                    root: _private.config.modulo + "postDeleteUbigeo",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.UBIG+"gridUbigeo");
                                }
                            });
                        }
                    }
                });
            }
        });
    };     
    
    this.publico.postDesactivar = function(btn,id){
           simpleAjax.send({
               element: btn,
               root: _private.config.modulo + 'postDesactivar',
               data: '&_idUbigeo='+id,
               fnCallback: function(data) {
                   if(!isNaN(data.result) && parseInt(data.result) === 1){
                       simpleScript.notify.ok({
                           content: lang.mensajes.MSG_24,
                           callback: function(){
                               simpleScript.reloadGrid('#'+diccionario.tabs.UBIG+'gridUbigeo');
                           }
                       });
                   }
               }
           });
       };
    
    this.publico.postActivar = function(btn,id){
        simpleAjax.send({
            element: btn,
            root: _private.config.modulo + 'postActivar',
            data: '&_idUbigeo='+id,
            fnCallback: function(data){
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_25,
                        callback: function(){
                            simpleScript.reloadGrid('#'+diccionario.tabs.UBIG+'gridUbigeo');
                        }
                    });
                }
            }
        });
    };   
    
    this.publico.resetPais = function(){
        $("#"+diccionario.tabs.UBIG+"gridDepartamento").html('');
        $("#"+diccionario.tabs.UBIG+"gridUbigeo").html('');
        $('#'+diccionario.tabs.UBIG+'tollDepartamento button').hide();        
        $('#'+diccionario.tabs.UBIG+'tollUbigeo button').hide();
        _private.idDepartamento = 0;        
        _private.idUbigeo = 0;
        _private.tituloDepartamento = '';        
        _private.tituloUbigeo = '';
        $('#widget_'+diccionario.tabs.UBIG+'departamento header h2').html(lang.generico.DEPARTAMENTO.toUpperCase());          
        $('#widget_'+diccionario.tabs.UBIG+'ubigeo header h2').html(lang.generico.DISTRITO.toUpperCase());      
    }
    this.publico.resetDepartamento = function(){
        $("#"+diccionario.tabs.UBIG+"gridUbigeo").html('');
        $('#'+diccionario.tabs.UBIG+'tollUbigeo button').hide();
        _private.idUbigeo = 0;
        _private.tituloUbigeo = '';
        $('#widget_'+diccionario.tabs.UBIG+'ubigeo header h2').html(lang.generico.DISTRITO.toUpperCase()); 
    }    
           
    /* ----  Reutilizable ---*/
    /* Combos para Sistema */
    this.publico.getDepartamento = function(obj){
        simpleAjax.send({
            gifProcess: true,
            root: _private.config.modulo + 'getDepartamentos',
            data: '&_idPais='+obj.idPais,
            fnCallback: function(data){
               
                simpleScript.listBox({
                    data: data,
                    optionSelec: true,
                    icon:false,
                    encript:true,
                    content: obj.content,
                    attr:{
                        id: obj.idElement,
                        name: obj.nameElement
                    },
                    dataView:{
                        etiqueta: 'departamento',
                        value: 'id_departamento'
                    },
                    fnCallback: function(){
                        simpleScript.setEvent.change({
                            element: '#'+obj.idElement,
                            event: function(){                                     
                                ubigeo.getUbigeo({
                                    idDepartamento: $('#'+obj.idElement).val(),
                                    content: obj.contentUbigeo,
                                    idElement: obj.idUbigeo,
                                    nameElement: obj.idUbigeo
                                });
                            }
                        });
                        simpleScript.chosen({'id':'#'+obj.idElement});
                    }
                });
            }
        });
    };
         
    this.publico.getUbigeo = function(obj){
        simpleAjax.send({
            gifProcess: true,
            root: _private.config.modulo + 'getUbigeo',
            data: '&_idDepartamento='+obj.idDepartamento,
            fnCallback: function(data){
                simpleScript.listBox({
                    data: data,
                    optionSelec: true,
                    icon:false,
                    encript:true,
                    content: obj.content,
                    attr:{
                        id: obj.idElement,
                        name: obj.nameElement
                    },
                    dataView:{
                        etiqueta: 'distrito',
                        value: 'id_ubigeo'
                    }
                });
                simpleScript.chosen({'id':'#'+obj.idElement});
            }
        });
    };
    /* Fin Combos para Sistema */
    
    
    return this.publico;
    
};
var ubigeo = new ubigeo_();