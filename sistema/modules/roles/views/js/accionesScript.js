var accionesScript_ = function(){
    
    this.publico = {};
    
    this.publico.validateAccion = function(obj){
        $(obj.form).validate({
            rules : {
                    CRDACtxt_accion : {
                            required : true,
                            regular: true,
                            minlength: 3
                    },
                    CRDACtxt_alias : {
                            required : true,
                            regular: true,
                            minlength: 2,
                            maxlength: 5
                    },
                    CRDACtxt_icono : {
                            required : true,
                            regular: true,
                            minlength: 3
                    },
                    CRDACtxt_theme : {
                            required : true,
                            regular: true,
                            minlength: 3
                    }
            },
            
            errorPlacement : function(error, element) {
                    error.insertAfter(element.parent());
            },
                    
            submitHandler: function(){
                eval(obj.evento);
            }   
        });
    };
    
   return this.publico;
   
};
var accionesScript = new accionesScript_();