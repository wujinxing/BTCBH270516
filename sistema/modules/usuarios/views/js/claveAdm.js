var claveAdm_ = function(){
    
    var _private = {};
    
    _private.config = {
        modulo: 'usuarios/claveAdm/'
    };
    
    this.publico = {};
    
    this.publico.main = function(){
        simpleAjax.send({
            dataType: 'html',
            root: _private.config.modulo,
            fnCallback: function(data){
                $('#cont-modal').append(data);  /*los formularios con append*/
                $('#'+diccionario.tabs.CLADM+'indexClaveAdm').modal('show');
            }
        });
    };
    
    this.publico.postCambiarClave = function(){
        simpleAjax.send({
            element: '#'+diccionario.tabs.CLADM+'btnEClav',
            root: _private.config.modulo + 'postCambiarClave',
            form: '#'+diccionario.tabs.CLADM+'indexClaveAdm',
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19
                    });
                    simpleScript.closeModal('#'+diccionario.tabs.CLADM+'indexClaveAdm');
                }
            }
        });
    };
    
    return this.publico;
    
};
var claveAdm = new claveAdm_();