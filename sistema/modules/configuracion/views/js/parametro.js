var parametro_ = function(){
    
    var _private = {};
    
    _private.idParametro = 0;    
    _private.mapaSitio = '';
    
    _private.config = {
        modulo: 'configuracion/parametro/'
    };

    this.publico = {};
    
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.T100,
            label: $(element).attr('title'),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                parametro.getContenido();
            }
        });
    };
    
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: 'html',
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $('#'+diccionario.tabs.T100+'_CONTAINER').html(data);
                parametro.getGridParametro();
            }
        });
    };
    
    this.publico.getGridParametro = function (){
        var oTable = $('#'+diccionario.tabs.T100+'gridParametro').dataTable({           
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "5%", sClass: "center", bSortable: false},
                {sTitle: lang.generico.NOMBRE, sWidth: "30%"},                
                {sTitle: lang.generico.VALOR, sWidth: "30%"},
                {sTitle: lang.generico.ALIAS, sWidth: "10%",  sClass: "center", bSortable: false},
                {sTitle: lang.generico.ESTADO, sWidth: "8%",  sClass: "center", bSortable: false},
                {sTitle: lang.generico.ACCIONES, sWidth: "8%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, 'asc']],
            sScrollY: "300px",
            sAjaxSource: _private.config.modulo+'getGridParametro',
            fnDrawCallback: function() {
                $('#'+diccionario.tabs.T100+'gridParametro_filter').find('input:text').attr('placeholder',lang.busqueda.T100).css("width","300px");
                simpleScript.enterSearch("#"+diccionario.tabs.T100+"gridParametro",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: '#widget_'+diccionario.tabs.T100, //widget del datagrid
                    typeElement: 'button, #'+diccionario.tabs.T100+'chk_all'
                });                
           }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getNuevoParametro = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: 'html',
            root: _private.config.modulo + 'getNuevoParametro',
            fnCallback: function(data){
                $('#cont-modal').append(data);  /*los formularios con append*/
                $('#'+diccionario.tabs.T100+'formParametro').modal('show');
            }
        });
    };
    
    this.publico.getEditarParametro = function(id){
        _private.idParametro = id;
        
        simpleAjax.send({
            gifProcess: true,
            dataType: 'html',
            root: _private.config.modulo + 'getEditarParametro',
            data: '&_idParametro='+_private.idParametro,
            fnCallback: function(data){
                $('#cont-modal').append(data);  /*los formularios con append*/
                $('#'+diccionario.tabs.T100+'formParametro').modal('show');
            }
        });
    };
    
    this.publico.postNuevoParametro = function(){
        simpleAjax.send({
            flag: 1,
            element: '#'+diccionario.tabs.T100+'btnGparm',
            root: _private.config.modulo + 'postNuevoParametro',
            form: '#'+diccionario.tabs.T100+'formParametro',
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            parametro.getGridParametro();
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
    
    this.publico.postEditarParametro = function(){
        
        simpleAjax.send({
            flag: 2,
            element: '#'+diccionario.tabs.T100+'btnEparm',
            root: _private.config.modulo + 'postEditarParametro',
            form: '#'+diccionario.tabs.T100+'formParametro',
            data: '&_idParametro='+_private.idParametro,
            fnCallback: function(data) {                
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            _private.idParametro = 0;
                            simpleScript.reloadGrid('#'+diccionario.tabs.T100+'gridParametro');
                            simpleScript.closeModal('#'+diccionario.tabs.T100+'formParametro');
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
    this.publico.postDeleteParametroAll = function(btn){
        simpleScript.validaCheckBox({
            id: '#'+diccionario.tabs.T100+'gridParametro',
            msn: lang.mensajes.MSG_9,
            fnCallback: function(){
                simpleScript.notify.confirm({
                    content: lang.mensajes.MSG_7,
                    callbackSI: function(){
                        simpleAjax.send({
                            flag: 3,
                            element: btn,
                            form: '#'+diccionario.tabs.T100+'formGridParametro',
                            root: _private.config.modulo + 'postDeleteParametroAll',
                            fnCallback: function(data) {
                                if(!isNaN(data.result) && parseInt(data.result) === 1){
                                    simpleScript.notify.ok({
                                        content: lang.mensajes.MSG_8,
                                        callback: function(){
                                            parametro.getGridParametro();
                                        }
                                    });
                                }
                            }
                        });
                    }
                });
            }
        });
    };
    this.publico.postDesactivar = function(btn,id){
           simpleAjax.send({
               element: btn,
               root: _private.config.modulo + 'postDesactivar',
               data: '&_idParametro='+id,
               clear: true,
               fnCallback: function(data) {
                   if(!isNaN(data.result) && parseInt(data.result) === 1){
                       simpleScript.notify.ok({
                           content: lang.mensajes.MSG_24,
                           callback: function(){
                               simpleScript.reloadGrid('#'+diccionario.tabs.T100+'gridParametro');
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
            data: '&_idParametro='+id,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_25,
                        callback: function(){
                            simpleScript.reloadGrid('#'+diccionario.tabs.T100+'gridParametro');
                        }
                    });
                }
            }
        });
    };      
    
    return this.publico;
    
};
var parametro = new parametro_();