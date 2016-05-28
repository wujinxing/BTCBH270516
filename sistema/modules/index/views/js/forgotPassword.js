var forgotPassword_  = function(){
    
    var _private = {};
    
    _private.config = {
       modulo: '../../index/login/'
    };
    
    this.publico = {};
    
   
    this.publico.postAcceso = function(){
        simpleAjax.send({
            element: '#btnClave',
            root: _private.config.modulo+'postAcceso',
            form: '#login-form-password',
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.clave.OK
                    });
                    setTimeout(function(){
                        simpleScript.redirect('../../');
                    }, 1000);                                        
                } else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: lang.clave.ER1
                    });
                } else if(!isNaN(data.result) && parseInt(data.result) === 3){
                    simpleScript.notify.error({
                        content: lang.clave.ER2
                    });
                }
            }
        });
    };
    return this.publico;
};

var forgotPassword = new forgotPassword_();