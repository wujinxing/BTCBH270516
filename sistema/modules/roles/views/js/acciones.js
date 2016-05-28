var acciones_ = function(){
    
    var _private = {};
    
    _private.mapaSitio = '';
    
    _private.config = {
        modulo: 'roles/acciones/'
    };
    
    _private.idAccion = 0;
    
    this.publico = {};
    
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.T2,
            label: $(element).attr('title') ,
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                acciones.getContenido();
            }
        });
    };
    
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: 'html',
            root: _private.config.modulo+'acciones',
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $('#'+diccionario.tabs.T2+'_CONTAINER').html(data);
                acciones.getGridAcciones();
            }
        });
    };
    
    this.publico.getGridAcciones = function (){        
        var oTable = $('#'+diccionario.tabs.T2+'gridAcciones').dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: "Código", sClass: "center",sWidth: "10%"},
                {sTitle: "Acción", sWidth: "25%"},
                {sTitle: "Diseño", sWidth: "15%", sClass: "center", bSortable: false},
                {sTitle: "Alias", sWidth: "10%"},
                {sTitle: "Estado", sWidth: "10%", sClass: "center", bSortable: false},
                {sTitle: "Acciones", sWidth: "15%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[0, 'asc']],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+'getAcciones',
            fnDrawCallback: function() {
                $('#'+diccionario.tabs.T2+'gridAcciones_filter').find('input').attr('placeholder','Buscar por acción').css("width","250px");
                simpleScript.enterSearch("#"+diccionario.tabs.T2+"gridAcciones",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: '#widget_'+diccionario.tabs.T2+'acciones',
                    typeElement: 'button'
                });               
           }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNuevaAccion = function(btn){
        var bt = '#'+diccionario.tabs.T2+'btnNew';
        if(btn !== undefined){
            bt = btn;
        }
        simpleAjax.send({
            element: bt,
            dataType: 'html',
            root: _private.config.modulo + 'getNuevaAccion',
            fnCallback: function(data){
                $('#cont-modal').append(data);  /*los formularios con append*/
                $('#'+diccionario.tabs.T2+'formNuevaAccion').modal('show');               
            }
        });
    };
    
    /*extraer rol para editar*/
    this.publico.getFormEditAccion = function(){
        _private.idAccion = simpleScript.getParam(arguments[0]);
       
        simpleAjax.send({
            dataType: 'html',
            gifProcess: true,
            data: '&_key='+_private.idAccion,
            root: _private.config.modulo + 'getEditAccion',
            fnCallback: function(data){
                $('#cont-modal').append(data);
                $('#'+diccionario.tabs.T2+'formEditAccion').modal('show');                
            }
        });
    };
    
    this.publico.postNewAccion = function(){
        simpleAjax.send({
            flag: 1,
            element: '#'+diccionario.tabs.T2+'btnGrabaAccion',
            root: _private.config.modulo + 'postAccion',
            form: '#'+diccionario.tabs.T2+'formNuevaAccion',
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            simpleScript.reloadGrid('#'+diccionario.tabs.T2+'gridAcciones');
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.accion.VAL01
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.error({
                        content: lang.accion.VAL02
                    });
                }
            }
        });
    };
    
    this.publico.postEditAccion = function(){
        simpleAjax.send({
            flag: 2,
            element: '#'+diccionario.tabs.T2+'btnEditaAccion',
            root: _private.config.modulo + 'postAccion',
            form: '#'+diccionario.tabs.T2+'formEditAccion',
            data: '&_key='+_private.idAccion,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                           simpleScript.reloadGrid('#'+diccionario.tabs.T2+'gridAcciones');
                           simpleScript.closeModal('#'+diccionario.tabs.T2+'formEditAccion');
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.accion.VAL01
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.error({
                        content: lang.accion.VAL02
                    });
                }
            }
        });
    };
    
    this.publico.postDeleteAccion = function(){
        _private.idAccion = simpleScript.getParam(arguments[0]);
        
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({
                    flag: 3,
                    gifProcess: true,
                    data: '&_key='+_private.idAccion,
                    root: _private.config.modulo + 'postDeleteAccion',
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid('#'+diccionario.tabs.T2+'gridAcciones');
                                }
                            });
                        }
                    }
                });
            }
        });
    };
    
    return this.publico;
    
};
var acciones = new acciones_();