var login_  = function(){
    
    var _private = {};
    
    _private.config = {
        moduloIndex: 'index'
    };
    
    this.publico = {};
    
    this.publico.postEntrar = function(){
           simpleAjax.send({
                flag: 1,
                element: '#btnEntrar',
                root: _private.config.moduloIndex + '/login',
                //form: '#login-form',
                data: '&_user='+simpleAjax.stringPost($('#txtUser').val())+'&_clave='+simpleAjax.stringPost($('#txtClave').val()),
                fnCallback: function(data) {
                    if(!isNaN(data.id_usuario) && data.id_usuario > 0){
                        simpleScript.notify.ok({
                            content: lang.index.OK,
                            callback: function(){
                                simpleScript.redirect('index');
                            }
                        });
                    }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                        simpleScript.notify.error({
                            content: lang.index.E2
                        });                
                    }else if(!isNaN(data.result) && parseInt(data.result) === 3){
                        simpleScript.notify.error({
                            content: lang.index.E3
                        });                                  
                    }else{
                        simpleScript.notify.error({
                            content: lang.index.E1
                        });
                    }
                }
            });
    };
    
    this.publico.postEntrar2 = function(){
           simpleAjax.send({
                element: '#btnEntrar',
                root: _private.config.moduloIndex + '/login/getClave',
                data: '&_clave='+simpleAjax.stringPost($('#txtClaveAdmin').val()),
                fnCallback: function(data) {
                    if(!isNaN(data.result) && parseInt(data.result) === 1 ){
                        simpleScript.notify.ok({
                            content: lang.index.OK,
                            callback: function(){
                                simpleScript.redirect('index');
                            }
                        });
                    }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                        simpleScript.notify.error({
                            content: lang.index.E4
                        });                
                    }
                }
            });
    };    
   
    return this.publico;
};

var login = new login_();