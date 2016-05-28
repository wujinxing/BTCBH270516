var configurarUsuariosScript_ = function(){
    
   
   this.public = {};
   
   this.public.validaFormUser = function(flag){
       var empleado = $('#'+diccionario.tabs.T4+'txt_empleado').val();
       var mail = $('#'+diccionario.tabs.T4+'txt_email').val();
       
       if(empleado === ''){
            simpleScript.notify.warning({
                content: lang.usuario.VAL03
            });
       }else if(mail.indexOf('@', 0) === -1 || mail.indexOf('.', 0) === -1) {
            simpleScript.notify.warning({
                content: lang.usuario.VAL04
            });
       }else{
            simpleScript.validaCheckBox({
                id: '#s2',
                msn: lang.usuario.VAL05,
                fnCallback: function(){
                    if(flag === 1){
                        configurarUsuarios.postNuevoUsuario();
                    }else{
                        configurarUsuarios.postEditarUsuario();
                    }
                }
            });
       }
   };
     this.public.validaChangePass = function(){  
        $("#fromchange_pass").validate({
            rules : {                   
                    txtNewClave : {
                            required : true,
                            minlength : 3,
                            maxlength : 20
                    }
            },
            messages : {                   
                    txtClave : {
                            required : 'Ingrese su nueva contraseña'
                    }
            },
            errorPlacement : function(error, element) {
                    error.insertAfter(element.parent());
            },
                    
            submitHandler: function(){
                 configurarUsuarios.postPass();
            }   
        }); 
    };
   return this.public;
    
};
var configurarUsuariosScript = new configurarUsuariosScript_();