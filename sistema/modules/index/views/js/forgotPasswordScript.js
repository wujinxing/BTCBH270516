var forgotPasswordScript_ = function(){
    
    var _private = {};
    
    this.publico = {};
    
    this.publico.validate = function(){    
        $("#login-form-password").validate({
                // Rules for form validation
                rules : {
                        txtUser : {
                                required : true,
                                email: true
                        }
                },

                messages : {
                        txtEmail : {
                                required : 'Ingrese su correo'
                        }
                },

                errorPlacement : function(error, element) {
                        error.insertAfter(element.parent());
                },

                submitHandler: function(){
                    forgotPassword.postAcceso();
                }   
            });  
      };     
        
    return this.publico;
};

var forgotPasswordScript = new forgotPasswordScript_();

forgotPasswordScript.validate();