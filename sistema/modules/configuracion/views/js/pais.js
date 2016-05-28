/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 17-12-2014 00:12:57 
* Descripcion : ubigeo.js
* ---------------------------------------
*/
var pais_ = function(){
    
    /*metodos privados*/
    var _private = {};
        
    _private.idPais = 0;
    
    _private.config = {
        modulo: "configuracion/pais/"
    };

    /*metodos publicos*/
    this.publico = {};      
    
    this.publico.getFormNewPais = function(){
        simpleAjax.send({            
            dataType: "html",
            root: _private.config.modulo + "getFormNewPais",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.UBIG+"formNewPais").modal("show");
            }
        });
    };
    
    this.publico.getFormEditPais = function(btn,id){
        _private.idPais = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditPais",
            data: "&_idPais="+_private.idPais,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.UBIG+"formEditPais").modal("show");
            }
        });
    };
    
    this.publico.postNewPais = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.UBIG+"btnGrPais",
            root: _private.config.modulo + "postNewPais",
            form: "#"+diccionario.tabs.UBIG+"formNewPais",
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            simpleScript.reloadGrid("#"+diccionario.tabs.UBIG+"gridPais");
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
    
    this.publico.postEditPais = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.UBIG+"btnEdPais",
            root: _private.config.modulo + "postEditPais",
            form: "#"+diccionario.tabs.UBIG+"formEditPais",
            data: "&_idPais="+_private.idPais,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idPais = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.UBIG+"formEditPais");
                            simpleScript.reloadGrid("#"+diccionario.tabs.UBIG+"gridPais");
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
    
    this.publico.postDeletePais = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({                    
                    element: btn,
                    gifProcess: true,
                    data: "&_idPais="+id,
                    root: _private.config.modulo + "postDeletePais",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.UBIG+"gridPais");
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
               data: '&_idPais='+id,
               fnCallback: function(data) {
                   if(!isNaN(data.result) && parseInt(data.result) === 1){
                       simpleScript.notify.ok({
                           content: lang.mensajes.MSG_24,
                           callback: function(){
                               simpleScript.reloadGrid('#'+diccionario.tabs.UBIG+'gridPais');
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
            data: '&_idPais='+id,
            fnCallback: function(data){
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_25,
                        callback: function(){
                            simpleScript.reloadGrid('#'+diccionario.tabs.UBIG+'gridPais');
                        }
                    });
                }
            }
        });
    };   
    
    return this.publico;
    
};
var pais = new pais_();