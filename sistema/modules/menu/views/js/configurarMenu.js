var configurarMenu_ = function(){
    
    var _private = {};
    
    _private.config = {
        modulo: 'menu/configurarMenu/'
    };
    
    _private.idDominio = 0;    
    _private.idModulo = 0;    
    _private.idMenuPrincipal = 0;    
    _private.idOpcion = 0;
    _private.mapaSitio = '';
     
    this.publico = {};
    
    this.publico.resetKey = function(){
        _private.idDominio = 0;
    };
    
    this.publico.resetKeyModulo = function(){
        _private.idModulo = 0;
    };
    
    this.publico.resetKeyMenuPrincipal = function(){
        _private.idMenuPrincipal = 0;
    };
    
    this.publico.resetKeyOpcion = function(){
        _private.idOpcion = 0;
    };
    
    this.publico.main = function(element){
        configurarMenu.resetKey();
        configurarMenu.resetKeyModulo();
        configurarMenu.resetKeyMenuPrincipal();
        configurarMenu.resetKeyOpcion();
        
        simpleScript.addTab({
            id : diccionario.tabs.T3,
            label: $(element).attr('title'),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                configurarMenu.getContenido();
            }
        });
    };
    
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: 'html',
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $('#'+diccionario.tabs.T3+'_CONTAINER').html(data);
                setup_widgets_desktop();
            }
        });
    };
    
    /*listado de dominios*/
    this.publico.getListaDominios = function(){
        simpleAjax.send({
            dataType: 'html',
            gifProcess: true,
            root: _private.config.modulo + 'getListaDominios',
            fnCallback: function(data){
                $('#cont-listadominios').html(data);
            }
        });
    };
    
    /*listado de modulos*/
    this.publico.getModulos = function(){
        _private.idDominio = simpleScript.getParam(arguments[0]);
        
        simpleAjax.send({
            dataType: 'html',
            root: _private.config.modulo + 'getListaModulos',
            data: '&_idDominio='+_private.idDominio,
            fnCallback: function(data){
                $('#cont-listaModulos').html(data);
            }
        });
    };
    
    /*listado de menu principal*/
    this.publico.getMenuPrincipal = function(){
        _private.idModulo = simpleScript.getParam(arguments[0]);
        
        simpleAjax.send({
            dataType: 'html',
            root: _private.config.modulo + 'getListaMenuPrincipal',
            data: '&_idModulo='+_private.idModulo,
            fnCallback: function(data){
                $('#cont-listaMenuPrincipal').html(data);
            }
        });
    };
    
    /*listado de opciones*/
    this.publico.getOpciones = function(){
        _private.idMenuPrincipal = simpleScript.getParam(arguments[0]);
        
        simpleAjax.send({
            dataType: 'html',
            root: _private.config.modulo + 'getListaOpciones',
            data: '&_idMenuPrincipal='+_private.idMenuPrincipal,
            fnCallback: function(data){
                $('#cont-listaOpciones').html(data);
            }
        });
    };
    
    this.publico.getNuevoDominio = function(btn){
        simpleAjax.send({
            gifProcess: true,
            dataType: 'html',
            root: _private.config.modulo + 'getNuevoDominio',
            fnCallback: function(data){
                $('#cont-modal').append(data);  /*los formularios con append*/
                $('#'+diccionario.tabs.T3+'formNuevoDominio').modal('show');
            }
        });
    };
    
    this.publico.getNuevoModulo = function(){
        if(_private.idDominio === 0){
            simpleScript.notify.error({
                content: lang.menu.VAL08
            });
        }else{
            simpleAjax.send({
                gifProcess: true,
                dataType: 'html',
                root: _private.config.modulo + 'getNuevoModulo',
                fnCallback: function(data){
                    $('#cont-modal').append(data);  /*los formularios con append*/
                    $('#'+diccionario.tabs.T3+'formNuevoModulo').modal('show');
                }
            });
        }        
    };
    
    this.publico.getNuevoMenuPrincipal = function(btn){
        if(_private.idModulo === 0){
            simpleScript.notify.error({
                content: lang.menu.VAL09
            });
        }else{
            simpleAjax.send({
                gifProcess: true,
                dataType: 'html',
                root: _private.config.modulo + 'getNuevoMenuPrincipal',
                fnCallback: function(data){
                    $('#cont-modal').append(data);  /*los formularios con append*/
                    $('#'+diccionario.tabs.T3+'formNuevoMenuPrincipal').modal('show');
                }
            });
        }
    };
    
    this.publico.getNuevaOpcion = function(btn){
        if(_private.idMenuPrincipal === 0){
            simpleScript.notify.error({
                content: lang.menu.VAL07
            });
        }else{
            simpleAjax.send({
                element: '#'+btn,
                dataType: 'html',
                root: _private.config.modulo + 'getNuevaOpcion',
                fnCallback: function(data){
                    $('#cont-modal').append(data);  /*los formularios con append*/
                    $('#'+diccionario.tabs.T3+'formNuevOpcion').modal('show');
                }
            });
        }
    };
    
    /*dominio a editar*/
    this.publico.getEditarDominio = function(){
        /*reset key modulo, menupri, opcion y sus contenedores*/
        configurarMenu.resetKeyModulo();
        configurarMenu.resetKeyMenuPrincipal();
        configurarMenu.resetKeyOpcion();
        configurarMenuScript.resetFromDominio();
                            
        _private.idDominio = simpleScript.getParam(arguments[0]);
        
        simpleAjax.send({
            dataType: 'html',
            gifProcess: true,
            data: '&_idDominio='+_private.idDominio,
            root: _private.config.modulo + 'getEditarDominio',
            fnCallback: function(data){
                $('#cont-modal').append(data);  /*los formularios con append*/
                $('#'+diccionario.tabs.T3+'formEditarDominio').modal('show');
            }
        });
    };
    
    /*modulo a editar*/
    this.publico.getEditarModulo = function(){
        /*reset key menupri, opcion y sus contenedores*/
        configurarMenu.resetKeyMenuPrincipal();
        configurarMenu.resetKeyOpcion();
        configurarMenuScript.resetFromModulo();
                            
        _private.idModulo = simpleScript.getParam(arguments[0]);
        
        simpleAjax.send({
            dataType: 'html',
            gifProcess: true,
            data: '&_idModulo='+_private.idModulo,
            root: _private.config.modulo + 'getEditarModulo',
            fnCallback: function(data){
                $('#cont-modal').append(data);  /*los formularios con append*/
                $('#'+diccionario.tabs.T3+'formEditarModulo').modal('show');
            }
        });
    };
    
    /*menu principal a editar*/
    this.publico.getEditarMenuPrincipal = function(){
        /*reset key opcion y sus contenedores*/
        configurarMenu.resetKeyOpcion();
        configurarMenuScript.resetFromOpcion();
                            
        _private.idMenuPrincipal = simpleScript.getParam(arguments[0]);
        
        simpleAjax.send({
            dataType: 'html',
            gifProcess: true,
            data: '&_idMenuPrincipal='+_private.idMenuPrincipal,
            root: _private.config.modulo + 'getEditarMenuPrincipal',
            fnCallback: function(data){
                $('#cont-modal').append(data);  /*los formularios con append*/
                $('#'+diccionario.tabs.T3+'formEditarMenuPrincipal').modal('show');
            }
        });
    };
    
    this.publico.getEditarOpcion = function(){
        _private.idOpcion = simpleScript.getParam(arguments[0]);
        
        simpleAjax.send({
            dataType: 'html',
            gifProcess: true,
            data: '&_idOpcion='+_private.idOpcion,
            root: _private.config.modulo + 'getEditarOpcion',
            fnCallback: function(data){
                $('#cont-modal').append(data);  /*los formularios con append*/
                $('#'+diccionario.tabs.T3+'formEditarOpcion').modal('show');
            }
        });
    };
    
    this.publico.postDominio = function(){
        simpleAjax.send({
            flag: 1,
            element: '#'+diccionario.tabs.T3+'btnGrabaDominio',
            root: _private.config.modulo + 'postDominio',
            form: '#'+diccionario.tabs.T3+'formNuevoDominio',
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            configurarMenu.getListaDominios();
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.menu.VAL06
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.error({
                        content: lang.mensajes.MSG_6
                    });
                }
            }
        });
    };
    
    this.publico.postEditarDominio = function(){
        simpleAjax.send({
            flag: 2,
            element: '#'+diccionario.tabs.T3+'btnEditaDominio',
            data: '&_idDominio='+_private.idDominio,
            root: _private.config.modulo + 'postDominio',
            form: '#'+diccionario.tabs.T3+'formEditarDominio',
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            _private.idDominio = 0;
                            configurarMenu.getListaDominios();
                            simpleScript.closeModal('#'+diccionario.tabs.T3+'formEditarDominio');
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.menu.VAL06
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.error({
                        content: lang.mensajes.MSG_6
                    });
                }
            }
        });
    };
    
    this.publico.postDeleteDominio = function(){
        var idDominio = simpleScript.getParam(arguments[0]);
        
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                /*reset key modulo, menupri, opcion y sus contenedores*/
                configurarMenu.resetKeyModulo();
                configurarMenu.resetKeyMenuPrincipal();
                configurarMenu.resetKeyOpcion();
                configurarMenuScript.resetFromDominio();
        
                simpleAjax.send({
                    flag: 3,
                    gifProcess: true,
                    data: '&_idDominio='+idDominio,
                    root: _private.config.modulo + 'postDeleteDominio',
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    configurarMenu.getListaDominios();
                                }
                            });
                        }
                    }
                });
            }
        });
    };
    
    this.publico.postModulo = function(){
        simpleAjax.send({
            flag: 1,
            element: '#'+diccionario.tabs.T3+'btnGrabaModulo',
            root: _private.config.modulo + 'postModulo',
            form: '#'+diccionario.tabs.T3+'formNuevoModulo',
            data: '&_idDominio='+_private.idDominio,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            configurarMenu.getModulos(_private.idDominio);
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.menu.VAL05
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.error({
                        content: lang.mensajes.MSG_6
                    });
                }
            }
        });
    };
    
    this.publico.postEditarModulo = function(){
        simpleAjax.send({
            flag: 2,
            element: '#'+diccionario.tabs.T3+'btnEditarModulo',
            data: '&_idModulo='+_private.idModulo+'&_idDominio='+_private.idDominio,
            root: _private.config.modulo + 'postModulo',
            form: '#'+diccionario.tabs.T3+'formEditarModulo',
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            configurarMenu.getModulos(_private.idDominio);
                            _private.idModulo = 0;
                            simpleScript.closeModal('#'+diccionario.tabs.T3+'formEditarModulo');
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.menu.VAL05
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.error({
                        content: lang.mensajes.MSG_6
                    });
                }
            }
        });
    };
    
    this.publico.postDeleteModulo = function(){
        var id = simpleScript.getParam(arguments[0]);
        
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                /*reset key menupri, opcion y sus contenedores*/
                configurarMenu.resetKeyMenuPrincipal();
                configurarMenu.resetKeyOpcion();
                configurarMenuScript.resetFromModulo();
        
                simpleAjax.send({
                    flag: 3,
                    gifProcess: true,
                    data: '&_idModulo='+id,
                    root: _private.config.modulo + 'postDeleteModulo',
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    configurarMenu.getModulos(_private.idDominio);
                                }
                            });
                        }
                    }
                });
            }
        });
    };
    
    this.publico.postMenuPrincipal = function(){
        simpleAjax.send({
            flag: 1,
            element: '#'+diccionario.tabs.T3+'btnGrabaMenuPri',
            root: _private.config.modulo + 'postMenuPrincipal',
            form: '#'+diccionario.tabs.T3+'formNuevoMenuPrincipal',
            data: '&_idModulo='+_private.idModulo,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            configurarMenu.getMenuPrincipal(_private.idModulo);
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.menu.VAL04
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.error({
                        content: lang.menu.VAL02
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 4){
                    simpleScript.notify.error({
                        content: lang.menu.VAL03
                    });
                }
            }
        });
    }; 
   
    this.publico.postEditarMenuPrincipal = function(){
        simpleAjax.send({
            flag: 2,
            element: '#'+diccionario.tabs.T3+'btnEditarMenuPri',
            data: '&_idModulo='+_private.idModulo+'&_idMenuPrincipal='+_private.idMenuPrincipal,
            root: _private.config.modulo + 'postMenuPrincipal',
            form: '#'+diccionario.tabs.T3+'formEditarMenuPrincipal',
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            configurarMenu.getMenuPrincipal(_private.idModulo);
                            _private.idMenuPrincipal = 0;
                            simpleScript.closeModal('#'+diccionario.tabs.T3+'formEditarMenuPrincipal');
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.menu.VAL04
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.error({
                        content: lang.menu.VAL02
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 4){
                    simpleScript.notify.error({
                        content: lang.menu.VAL03
                    });
                }
            }
        });
    };
    
    this.publico.postDeleteMenuPrincipal = function(){
        var id = simpleScript.getParam(arguments[0]);
        
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                /*reset key opcion y sus contenedores*/
                configurarMenu.resetKeyOpcion();
                configurarMenuScript.resetFromOpcion();
        
                simpleAjax.send({
                    flag: 3,
                    gifProcess: true,
                    data: '&_idMenuPrincipal='+id,
                    root: _private.config.modulo + 'postDeleteMenuPrincipal',
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    configurarMenu.getMenuPrincipal(_private.idModulo);
                                }
                            });
                        }
                    }
                });
            }
        });
    };
    
    this.publico.postOpcion = function(){
        simpleAjax.send({
            flag: 1,
            element: '#'+diccionario.tabs.T3+'btnGrabaOpcion',
            root: _private.config.modulo + 'postOpcion',
            form: '#'+diccionario.tabs.T3+'formNuevOpcion',
            data: '&_idMenuPrincipal='+_private.idMenuPrincipal,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            configurarMenu.getOpciones(_private.idMenuPrincipal);
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.menu.VAL01 
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.error({
                        content: lang.menu.VAL02
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 4){
                    simpleScript.notify.error({
                        content: lang.menu.VAL03 
                    });
                }
            }
        });
    };
    
    this.publico.postEditarOpcion = function(){
        simpleAjax.send({
            flag: 2,
            element: '#'+diccionario.tabs.T3+'btnEditaOpcion',
            data: '&_idOpcion='+_private.idOpcion+'&_idMenuPrincipal='+_private.idMenuPrincipal,
            root: _private.config.modulo + 'postOpcion',
            form: '#'+diccionario.tabs.T3+'formEditarOpcion',
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            configurarMenu.getOpciones(_private.idMenuPrincipal);
                            _private.idOpcion = 0;
                            simpleScript.closeModal('#'+diccionario.tabs.T3+'formEditarOpcion');
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.menu.VAL01 
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.error({
                        content: lang.menu.VAL02
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 4){
                    simpleScript.notify.error({
                        content: lang.menu.VAL03
                    });
                }
            }
        });
    };
    
    this.publico.postDeleteOpcion = function(){
        var id = simpleScript.getParam(arguments[0]);
        
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_5,
            callbackSI: function(){
                simpleAjax.send({
                    flag: 3,
                    gifProcess: true,
                    data: '&_idOpcion='+id,
                    root: _private.config.modulo + 'postDeleteOpcion',
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_6,
                                callback: function(){
                                    configurarMenu.getOpciones(_private.idMenuPrincipal);
                                }
                            });
                        }
                    }
                });
            }
        });
    };
    
    this.publico.postOrdenar = function(){
        var tipo = simpleScript.getParam(arguments[0]);
        var ids  = simpleScript.getParam(arguments[1]);

        switch(tipo){
            case 'DOM': /*ordenear modulos*/
                configurarMenu.postSortDominios(ids);  
                break;
            case 'MOD': /*ordenear modulos*/
                configurarMenu.postSortModulos(ids);  
                break;
            case 'MEP': /*ordenear menu principal*/
                configurarMenu.postSortMenuPrincipal(ids);  
                break;
            case 'OPC': /*ordenear opciones*/
                configurarMenu.postSortOpciones(ids);  
                break;
        }
    };
    
    this.publico.postSortDominios = function(){
        var ids  = simpleScript.getParam(arguments[0]);
        var textoAreaDividido = ids.split(",");
        var numeroPalabras = textoAreaDividido.length;
        
        simpleAjax.send({
            flag: 4,
            data: '&'+diccionario.tabs.T3+'txt_dominio='+ids+'&'+diccionario.tabs.T3+'txt_orden='+numeroPalabras,
            root: _private.config.modulo + 'postSortDominio'
        });
    };
    
    this.publico.postSortModulos = function(){
        var ids  = simpleScript.getParam(arguments[0]);
        var textoAreaDividido = ids.split(",");
        var numeroPalabras = textoAreaDividido.length;
        
        simpleAjax.send({
            flag: 4,
            data: '&'+diccionario.tabs.T3+'txt_modulo='+ids+'&'+diccionario.tabs.T3+'txt_orden='+numeroPalabras,
            root: _private.config.modulo + 'postOrdenarModulo'
        });
    };
    
    this.publico.postSortMenuPrincipal = function(){
        var ids  = simpleScript.getParam(arguments[0]);
        var textoAreaDividido = ids.split(",");
        var numeroPalabras = textoAreaDividido.length;
        
        simpleAjax.send({
            flag: 4,
            data: '&'+diccionario.tabs.T3+'txt_menu='+ids+'&'+diccionario.tabs.T3+'txt_orden='+numeroPalabras,
            root: _private.config.modulo + 'postOrdenarMenuPrincipal'
        });
    };
    
    this.publico.postSortOpciones = function(){
        var ids  = simpleScript.getParam(arguments[0]);
        var textoAreaDividido = ids.split(",");
        var numeroPalabras = textoAreaDividido.length;
        
        simpleAjax.send({
            flag: 4,
            data: '&'+diccionario.tabs.T3+'txt_opcion='+ids+'&'+diccionario.tabs.T3+'txt_orden='+numeroPalabras,
            root: _private.config.modulo + 'postSortOpciones'
        });
    };
    
    return this.publico;
    
};
var configurarMenu = new configurarMenu_();