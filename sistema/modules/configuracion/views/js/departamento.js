/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 17-12-2014 00:12:57 
* Descripcion : ubigeo.js
* ---------------------------------------
*/
var departamento_ = function(){
    
    /*metodos privados*/
    var _private = {};
        
    _private.idDepartamento = 0;
    _private.idPais = 0;
    
    _private.config = {
        modulo: "configuracion/departamento/"
    };

    /*metodos publicos*/
    this.publico = {};      
    
    this.publico.getFormNewDepartamento = function(idP){                
        if (idP == 0){
            simpleScript.notify.warning({
                content: lang.mensajes.MSG_31
            });
            return false;
        }        
        _private.idPais = idP;
        simpleAjax.send({            
            dataType: "html",
            root: _private.config.modulo + "getFormNewDepartamento",
            data: "&_idPais="+_private.idPais,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.UBIG+"formNewDepartamento").modal("show");
            }
        });
    };
    
    this.publico.getFormEditDepartamento = function(btn,id){
        _private.idDepartamento = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditDepartamento",
            data: "&_idDepartamento="+_private.idDepartamento+"&_idPais="+_private.idPais,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.UBIG+"formEditDepartamento").modal("show");
            }
        });
    };
    
    this.publico.postNewDepartamento = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.UBIG+"btnGrDepartamento",
            root: _private.config.modulo + "postNewDepartamento",
            form: "#"+diccionario.tabs.UBIG+"formNewDepartamento",
            data: "&_idPais="+_private.idPais,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            simpleScript.reloadGrid("#"+diccionario.tabs.UBIG+"gridDepartamento");
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
    
    this.publico.postEditDepartamento = function(){
        simpleAjax.send({
            element: "#"+diccionario.tabs.UBIG+"btnEdDepartamento",
            root: _private.config.modulo + "postEditDepartamento",
            form: "#"+diccionario.tabs.UBIG+"formEditDepartamento",
            data: "&_idDepartamento="+_private.idDepartamento,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idDepartamento = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.UBIG+"formEditDepartamento");
                            simpleScript.reloadGrid("#"+diccionario.tabs.UBIG+"gridDepartamento");
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
    
    this.publico.postDeleteDepartamento = function(btn,id){
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({                    
                    element: btn,
                    gifProcess: true,
                    data: "&_idDepartamento="+id,
                    root: _private.config.modulo + "postDeleteDepartamento",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    simpleScript.reloadGrid("#"+diccionario.tabs.UBIG+"gridDepartamento");
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
               data: '&_idDepartamento='+id,
               fnCallback: function(data) {
                   if(!isNaN(data.result) && parseInt(data.result) === 1){
                       simpleScript.notify.ok({
                           content: lang.mensajes.MSG_24,
                           callback: function(){
                               simpleScript.reloadGrid('#'+diccionario.tabs.UBIG+'gridDepartamento');
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
            data: '&_idDepartamento='+id,
            fnCallback: function(data){
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_25,
                        callback: function(){
                            simpleScript.reloadGrid('#'+diccionario.tabs.UBIG+'gridDepartamento');
                        }
                    });
                }
            }
        });
    };   
    
    return this.publico;
    
};
var departamento = new departamento_();