var cajaCierreScript_ = function(){
    
      
    var _private = {};
       
    _private.totalEfectivo = 0;  
    _private.totalCaja = 0;  
    _private.index = 0;
    
    this.publico = {};
        
    this.publico.calculoTotal = function(){
        var collection = $('#'+diccionario.tabs.CAJAC+'gridBillete').find('tbody').find('tr');
        var t = 0;
        $.each(collection,function(){
            var tt = simpleScript.deleteComa($.trim($(this).find('td:eq(2)').text()));
            if(tt.length > 0){
                t += parseFloat(tt);
            }
        });
        var collection = $('#'+diccionario.tabs.CAJAC+'gridMoneda').find('tbody').find('tr');        
        $.each(collection,function(){
            var tt = simpleScript.deleteComa($.trim($(this).find('td:eq(2)').text()));
            if(tt.length > 0){
                t += parseFloat(tt);
            }
        });        
        _private.totalEfectivo = t;
        $('#'+diccionario.tabs.CAJAC+'1txt_importe').val(_private.totalEfectivo.toFixed(2));                                                     
        $('#'+diccionario.tabs.CAJAC+'txt_totalCaja').val(globalScript.number_format(_private.totalEfectivo,2));           
        var diferencia = 0, saldo=0;
        saldo = simpleScript.deleteComa($('#'+diccionario.tabs.CAJAC+'txt_saldoCaja').val());
        diferencia = saldo - _private.totalEfectivo;
        $('#'+diccionario.tabs.CAJAC+'txt_diferencia').val(globalScript.number_format(diferencia,2));           
                
        /*Total de Metodo de pago */
        var t = 0;
        _private.totalCaja = 0;
        var collection = $('#'+diccionario.tabs.CAJAC+'gridMetodoPago').find('tbody').find('tr');        
        $.each(collection,function(){
            var tt = simpleScript.deleteComa($.trim($(this).find('td:eq(1)').find('input:text').val()));
            if(tt.length > 0){
                t += parseFloat(tt);
            }
        });    
        _private.totalCaja = t;
        $('#'+diccionario.tabs.CAJAC+'txt_importeTotalCaja').val(globalScript.number_format(_private.totalCaja,2));          
    };
    
    this.publico.calculoTotalFilaUp = function(){
        /* Tabla de Billetes */
        var collection = $('#'+diccionario.tabs.CAJAC+'gridBillete').find('tbody').find('tr');        
        $.each(collection,function(){
            var tthis = $(this);
            /*keyup para cantidad*/
            $(this).find('td:eq(1)').find('input:text').keyup(function(){
                if(isNaN($(this).val()) || $(this).val() < 0 || $(this).val() == '' ){
                    $(this).val(0);
                }else{
                    var index = $(this).attr('data-index');
                    var cn = $(this).val();
                    var d = simpleScript.deleteComa($('#'+diccionario.tabs.CAJAC+index+'txt_denominacion').attr('data-cantidad'));
                    
                    cn = cn.replace(",","");
                    d = d.replace(",","");
                    
                    var ttc = parseFloat(cn) * parseFloat(d);                                                                                                                                          
                    tthis.find('td:eq(2)').html(ttc.toFixed(2));                    
                }
                cajaCierreScript.calculoTotal();
            });                       
            
        });
        /* Tabla de Monedas */
        var collection = $('#'+diccionario.tabs.CAJAC+'gridMoneda').find('tbody').find('tr');
        $.each(collection,function(){
            var tthis = $(this);
            /*keyup para cantidad*/
            $(this).find('td:eq(1)').find('input:text').keyup(function(){
                if(isNaN($(this).val()) || $(this).val() < 0 || $(this).val() == '' ){
                    $(this).val(0);
                }else{
                    var index = $(this).attr('data-index');
                    var cn = $(this).val();
                    var d = simpleScript.deleteComa($('#'+diccionario.tabs.CAJAC+index+'txt_denominacion').attr('data-cantidad'));
                    
                    cn = cn.replace(",","");
                    d = d.replace(",","");
                    
                    var ttc = parseFloat(cn) * parseFloat(d);                                                                                                                                          
                    tthis.find('td:eq(2)').html(ttc.toFixed(2));                    
                }
                cajaCierreScript.calculoTotal();
            });                       
        });
              
    };
       
    return this.publico;
    
};
var cajaCierreScript = new cajaCierreScript_();

