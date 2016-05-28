var generarVentaScript_ = function(){
    
      
    var _private = {};
    
    _private.productoAdd = [];
    
    _private.total = 0;
    
    _private.index = 0;
    
    this.publico = {};
    
    this.publico.addProducto = function(btn){
        simpleScript.validaCheckBox({
            element: btn,
            id: '#'+diccionario.tabs.VGEVE+'gridProductosFound',
            msn: lang.mensajes.MSG_9,
            fnCallback: function(){
                var collection = $('#'+diccionario.tabs.VGEVE+'gridProductosFound').find('tbody').find('tr'),
                    chk,cad,idProducto,descripcion,precio,um,cmulti,tr='',duplicado,importe=0, idx=0;
                 
                /*recorriendo productos seleccionados*/
                $.each(collection,function(index,value){
                    chk = $(this).find('td:eq(1)').find('input:checkbox');
                    if(chk.is(':checked')){
                        cad = chk.val().split('~');
                        idProducto = cad[0];
                        descripcion = cad[1];
                        precio = parseFloat(cad[2]).toFixed(2);
                        um = cad[3];
                        cmulti= cad[4];
                        duplicado = 0;                                
                        /*validanco duplicidad*/
                        /*if(_private.productoAdd.length > 0){//hay data
                            for(var x in _private.productoAdd){
                                if(_private.productoAdd[x] === simpleAjax.stringGet(idProducto)){
                                    duplicado = 1;
                                    simpleScript.notify.warning({
                                        content: descripcion+' no se puede agregar dos veces'
                                    });
                                }
                            }
                        }*/
                                                                                                           
                        if(duplicado === 0){//no duplicado, agregar
                            importe = precio;                                                        
                            /*guardo idProducto decifrado en temporal para validar ducplicidad*/
                            generarVentaScript.setIndex(); 
                            idx = generarVentaScript.getIndex();                                                                                   
                            
                            tr += '<tr id="'+diccionario.tabs.VGEVE+'tr_'+idx+'">';
                            tr += '    <td>';
                            tr += '        <input type="hidden" id="'+diccionario.tabs.VGEVE+idx+'hhddIdProducto" name="'+diccionario.tabs.VGEVE+'hhddIdProducto[]" value="'+idProducto+'">';
                            tr += '         <label class="textarea">';
                                if( simpleAjax.stringGet(idProducto) == 201600000001 ){
                                      tr +=  '<textarea class="editable" style="height:120px; resize: vertical;" autocomplete="on" id="'+diccionario.tabs.VGEVE+idx+'txt_descripcion" name="'+diccionario.tabs.VGEVE+'txt_descripcion[]" ></textarea>';
                                }else{
                                    tr +=  '<textarea style="background:#EFEFEF;" autocomplete="on" disabled id="'+diccionario.tabs.VGEVE+idx+'txt_descripcion" name="'+diccionario.tabs.VGEVE+'txt_descripcion[]" >'+descripcion+'</textarea>';
                                }
                            tr += '</label>';
                            tr += '    </td>';
                            tr += '    <td>'+um+'</td>';
                            tr += '    <td>';
                            tr += '        <label class="input"><input type="text" id="'+diccionario.tabs.VGEVE+idx+'txt_cantidad1" name="'+diccionario.tabs.VGEVE+'txt_cantidad1[]" value="1" style="text-align:right" data-index="'+idx+'"></label></td>';
                            
                            if (cmulti == '0'){
                                tr +=    '<td>';
                                tr += '    <label class="input"><input style="background:#EFEFEF;text-align:right;" type="text" id="'+diccionario.tabs.VGEVE+idx+'txt_cantidad2" name="'+diccionario.tabs.VGEVE+'txt_cantidad2[]" value="1" style="text-align:right" data-index="'+idx+'" readonly></label>';
                                tr += '</td>';
                            }else{
                                tr +=    '<td>';
                                tr += '    <label class="input"><input type="text" id="'+diccionario.tabs.VGEVE+idx+'txt_cantidad2" name="'+diccionario.tabs.VGEVE+'txt_cantidad2[]" value="1" style="text-align:right" data-index="'+idx+'"></label>';
                                tr += '</td>';
                            }
                            tr += '<td class="right">1.00</td>';
                            tr += '    <td class="right">';
                            tr += '        <label class="input"><input type="text" id="'+diccionario.tabs.VGEVE+idx+'txt_precio" name="'+diccionario.tabs.VGEVE+'txt_precio[]" value="'+precio+'" data-value="'+precio+'" data-index="'+idx+'" style="text-align:right"></label>';
                            tr += '    </td>';
                            tr += '    <td class="right">'+parseFloat(importe).toFixed(2)+'</td>';
                            tr += '    <td>';
                            tr += '        <button type="button" class="btn btn-danger btn-xs" onclick="generarVentaScript.removeItem(\''+idx+'\');"><i class="fa fa-trash-o"></i></a>';
                            tr += '    </td>';
                            tr += '</tr>';
                        }
                    }
                });
                
                if(tr !== ''){
                    /*agrego los registros*/
                    $('#'+diccionario.tabs.VGEVE+'gridProductos').find('tbody').append(tr);
                    
                   /*mensaje de cierre ventana*/
                   /*  simpleScript.notify.ok({
                        content: 'Productos se agregaron correctamente',
                        callback: function(){
                            simpleScript.closeModal('#'+diccionario.tabs.VGEVE+'formBuscarProductos');
                        }
                    });*/
                    simpleScript.closeModal('#'+diccionario.tabs.VGEVE+'formBuscarProductos');
                    generarVentaScript.calculoTotal();
                    generarVentaScript.calculoTotalFilaUp();                                       
                    
                    $('.editable').summernote({
                        height : 120,
                        focus : true,                  
                        lang: 'es-ES',
                        toolbar: [
                        ['font', ['bold','italic' ,'underline', 'clear','codeview' ]]
                      ]
                    });				
                    
                }
            }
        });
        simpleScript.removeAttr.click({
            container: '#'+diccionario.tabs.VGEVE+'gridProductos',
            typeElement: 'button'
        });
    };
    
    this.publico.removeItem = function(idx){
        /*quitar del array*/
        for(var x in _private.productoAdd){
            if(_private.productoAdd[x] === idx){
                _private.productoAdd[x] = null;
            }
        }
        $('#'+diccionario.tabs.VGEVE+'tr_'+idx).remove();
        generarVentaScript.calculoTotal();
    };
    
    this.publico.getIndex = function(){        
        return _private.index;
    };
    
    this.publico.setIndex = function(){
        _private.index = _private.index + 1;
        _private.productoAdd.push(_private.index);         
    };          
    
    this.publico.calculoTotal = function(){
        var collection = $('#'+diccionario.tabs.VGEVE+'gridProductos').find('tbody').find('tr');
        var t = 0;
        $.each(collection,function(){
            var tt = simpleScript.deleteComa($.trim($(this).find('td:eq(6)').text()));
            if(tt.length > 0){
                t += parseFloat(tt);
            }
        });
        _private.total = t;
        $('#'+diccionario.tabs.VGEVE+'txt_total').val(_private.total.toFixed(2));
        
        var imp = 1 + parseFloat($('#'+diccionario.tabs.VGEVE+'txt_porcentaje').val());
        $('#'+diccionario.tabs.VGEVE+'txt_venta').val(parseFloat(_private.total / imp).toFixed(2));
        $('#'+diccionario.tabs.VGEVE+'txt_impuesto').val(parseFloat(_private.total - (_private.total / imp)).toFixed(2));
        $('#'+diccionario.tabs.VGEVE+'txt_totalPago').val(parseFloat(_private.total).toFixed(2));
        $('#'+diccionario.tabs.VGEVE+'txt_totalF').val(parseFloat(_private.total).toFixed(2));
        $('#'+diccionario.tabs.VGEVE+'txt_pago').val(0);                           
        
    };
    
    this.publico.calculoTotalFilaUp = function(){
        var collection = $('#'+diccionario.tabs.VGEVE+'gridProductos').find('tbody').find('tr');
        
        $.each(collection,function(){
            var tthis = $(this);
            
            /*keyup para cantidad 1*/
            $(this).find('td:eq(2)').find('input:text').keyup(function(){
                if(isNaN($(this).val()) || $(this).val() < 0  || $(this).val() == '' ){
                    $(this).val(1);
                }
                var index = $(this).attr('data-index');
                var cn = $(this).val();
                var cn2 =  simpleScript.deleteComa($('#'+diccionario.tabs.VGEVE+index+'txt_cantidad2').val());                    
                var precio = simpleScript.deleteComa($('#'+diccionario.tabs.VGEVE+index+'txt_precio').val());

                cn = cn.replace(",","");
                cn2 = cn2.replace(",","");

                var ttc = parseFloat(cn) * parseFloat(cn2);                    
                 tthis.find('td:eq(4)').html(ttc.toFixed(2));

                var total = 0;
                total = parseFloat(precio) * parseFloat(ttc); 

                tthis.find('td:eq(6)').html(total.toFixed(2));
                
                generarVentaScript.calculoTotal();
                
            });
            
            /*keyup para Cantidad 2*/
            $(this).find('td:eq(3)').find('input:text').keyup(function(){
                if(isNaN($(this).val()) || $(this).val() < 0  || $(this).val() == '' ){
                    var d = $(this).attr('data-value');
                    $(this).val(d);
                }
                var index = $(this).attr('data-index');
                var cn = $(this).val();
                var cn2 =  simpleScript.deleteComa($('#'+diccionario.tabs.VGEVE+index+'txt_cantidad1').val());                  
                var precio = simpleScript.deleteComa($('#'+diccionario.tabs.VGEVE+index+'txt_precio').val());

                cn = cn.replace(",","");
                cn2 = cn2.replace(",","");

                var ttc = parseFloat(cn) * parseFloat(cn2);

                var total = 0;
                total = parseFloat(precio) * parseFloat(ttc); 

                tthis.find('td:eq(4)').html(ttc.toFixed(2));
                tthis.find('td:eq(6)').html(total.toFixed(2));
                generarVentaScript.calculoTotal();
            });
            
            /*keyup para precio*/
            $(this).find('td:eq(5)').find('input:text').keyup(function(){
                if(isNaN($(this).val()) || $(this).val() <= 0 || $(this).val() == ''  ){
                    var d = $(this).attr('data-value');
                    $(this).val(d);
                }
                var index = $(this).attr('data-index');
                var pr = $(this).val();                  
                var cn = simpleScript.deleteComa($('#'+diccionario.tabs.VGEVE+index+'txt_cantidad1').val());
                var cn2 = simpleScript.deleteComa($('#'+diccionario.tabs.VGEVE+index+'txt_cantidad2').val());
                var ttc = parseFloat(cn) * parseFloat(cn2);

                pr = pr.replace(",","");

                var total = 0;
                total = parseFloat(pr) * parseFloat(ttc);                   

                tthis.find('td:eq(6)').html(total.toFixed(2));
                generarVentaScript.calculoTotal();
            }); 
            
        });
        
    };
    
    this.publico.resetArrayProducto = function(){
        _private.productoAdd = [];
        _private.total = 0;
        _private.index = 0;
    };
    
    this.publico.calcularImpuesto = function(){
        var st = simpleScript.deleteComa($('#'+diccionario.tabs.VGEVE+'txt_venta').val()); /* Subtotal*/
        var t = simpleScript.deleteComa($('#'+diccionario.tabs.VGEVE+'txt_totalPago').val()); /* Total*/
                  
        var chk = $('#'+diccionario.tabs.VGEVE+'chk_impuesto').is(':checked');
        if (chk ){
            st = globalScript.number_format(st,2,'.', ',');
            $('#'+diccionario.tabs.VGEVE+'txt_totalF').val( st );
        }else{
            t = globalScript.number_format(t,2,'.', ',');
            $('#'+diccionario.tabs.VGEVE+'txt_totalF').val( t );
        }                      
    };
     
    this.publico.inclIGV = function(){
        
        var incl = $('#'+diccionario.tabs.VGEVE+'lst_igv').val()
        var neto = parseFloat($('#'+diccionario.tabs.VGEVE+'txt_total').val());
        
        var subtotal=0, impuesto = 0, total = 0;
        var imp =  parseFloat($('#'+diccionario.tabs.VGEVE+'txt_porcentaje').val());
        if (incl == 'S'){
            subtotal = neto / (1 +imp);
            impuesto = neto - subtotal;
            total = neto;
            $("#"+diccionario.tabs.VGEVE+"chk_impuesto").prop("checked", "");
            $("#"+diccionario.tabs.VGEVE+"chk_impuesto").attr("disabled","disabled"); 
        }else{            
           subtotal = neto; 
           impuesto = neto * imp;
           total = neto + impuesto;           
           $("#"+diccionario.tabs.VGEVE+"chk_impuesto").removeAttr("disabled");                       
        }
        
        subtotal = globalScript.number_format(subtotal,2,'.', ',');
        impuesto = globalScript.number_format(impuesto,2,'.', ',');
        total = globalScript.number_format(total,2,'.', ',');
        $('#'+diccionario.tabs.VGEVE+'txt_venta').val(subtotal);
        $('#'+diccionario.tabs.VGEVE+'txt_impuesto').val(impuesto);
        $('#'+diccionario.tabs.VGEVE+'txt_totalPago').val(total);
        $('#'+diccionario.tabs.VGEVE+'txt_totalF').val(total);
     };    
    
    return this.publico;
    
};
var generarVentaScript = new generarVentaScript_();

