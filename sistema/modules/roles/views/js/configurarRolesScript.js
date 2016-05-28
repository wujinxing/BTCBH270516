var configurarRolesScript_ = function(){
    
   
   this.publico = {};
   
   this.publico.validateRol = function(obj){
        $(obj.form).validate({
            rules : {
                    CRDCRtxt_rol : {
                            required : true,
                            regular: true,
                            minlength: 3
                    }
            },

            messages : {
                    CRDCRtxt_rol : {
                            required : lang.accion.VAL03,
                            regular: lang.accion.VAL04
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
   
   this.publico.showAcciones = function(idRolOpciones){
       $('.accionesOpcion').hide();
       $('#acc_'+idRolOpciones).fadeIn();
   };
   
   return this.publico;
    
};
var configurarRolesScript = new configurarRolesScript_();