var vGenerarCotizacionScript_ = function(){
    
    var _private = {};
    
    _private.productoAdd = [];
    
    _private.total = 0;
    
    _private.index = 0;
    
    this.publico = {};
    
    this.publico.addProducto = function(){
        simpleScript.validaCheckBox({
            id: '#'+diccionario.tabs.VCOTI+'gridProductosFound',
            msn: lang.mensajes.MSG_9,
            fnCallback: function(){
                var collection = $('#'+diccionario.tabs.VCOTI+'gridProductosFound').find('tbody').find('tr'),
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
                            vGenerarCotizacionScript.setIndex(); 
                            idx = vGenerarCotizacionScript.getIndex();                                                                                   
                            
                            tr += '<tr id="'+diccionario.tabs.VCOTI+'tr_'+idx+'">';
                            tr += '    <td>';
                            tr += '        <input type="hidden" id="'+diccionario.tabs.VCOTI+idx+'hhddIdProducto" name="'+diccionario.tabs.VCOTI+'hhddIdProducto[]" value="'+idProducto+'">';
                            tr += '         <label class="textarea">';
                            if( simpleAjax.stringGet(idProducto) == 201600000001 ){
                                  tr +=  '<textarea class="editable" style="height:120px; resize: vertical;" autocomplete="on" id="'+diccionario.tabs.VCOTI+idx+'txt_descripcion" name="'+diccionario.tabs.VCOTI+'txt_descripcion[]" ></textarea>';
                            }else{
                                tr +=  '<textarea style="background:#EFEFEF;" autocomplete="on" disabled id="'+diccionario.tabs.VCOTI+idx+'txt_descripcion" name="'+diccionario.tabs.VCOTI+'txt_descripcion[]" >'+descripcion+'</textarea>';
                            }
                            tr += '</label>';
                            tr += '    </td>';
                            tr += '    <td>'+um+'</td>';
                            tr += '    <td>';
                            tr += '        <label class="input"><input type="text" id="'+diccionario.tabs.VCOTI+idx+'txt_cantidad1" name="'+diccionario.tabs.VCOTI+'txt_cantidad1[]" value="1" style="text-align:right" data-index="'+idx+'"></label></td>';                            
                            if (cmulti == '0'){
                                tr +=    '<td>';
                                tr += '        <label class="input"><input style="background:#EFEFEF;text-align:right;" type="text" id="'+diccionario.tabs.VCOTI+idx+'txt_cantidad2" name="'+diccionario.tabs.VCOTI+'txt_cantidad2[]" value="1" style="text-align:right" data-index="'+idx+'" readonly></label>';
                                tr += '    </td>';
                            }else{
                                tr +=    '<td>';
                                tr += '        <label class="input"><input type="text" id="'+diccionario.tabs.VCOTI+idx+'txt_cantidad2" name="'+diccionario.tabs.VCOTI+'txt_cantidad2[]" value="1" style="text-align:right" data-index="'+idx+'"></label>';
                                tr += '    </td>';
                            }
                            tr += '<td class="right">1.00</td>';
                            tr += '    <td class="right">';
                            tr += '        <label class="input"><input type="text" id="'+diccionario.tabs.VCOTI+idx+'txt_precio" name="'+diccionario.tabs.VCOTI+'txt_precio[]" value="'+precio+'" data-value="'+precio+'" data-index="'+idx+'" style="text-align:right"></label>';
                            tr += '    </td>';
                            tr += '    <td class="right">'+parseFloat(importe).toFixed(2)+'</td>';
                            tr += '    <td>';
                            tr += '        <button type="button" class="btn btn-danger btn-xs" onclick="vGenerarCotizacionScript.removeItem(\''+idx+'\');"><i class="fa fa-trash-o"></i></a>';
                            tr += '    </td>';
                            tr += '</tr>';
                        }
                    }
                });
                
                if(tr !== ''){
                    /*agrego los registros*/
                    $('#'+diccionario.tabs.VCOTI+'gridProductos').find('tbody').append(tr);
                    
                   /*mensaje de cierre ventana*/
                   /*  simpleScript.notify.ok({
                        content: 'Productos se agregaron correctamente',
                        callback: function(){
                            simpleScript.closeModal('#'+diccionario.tabs.VCOTI+'formBuscarProductos');
                        }
                    });*/
                    simpleScript.closeModal('#'+diccionario.tabs.VCOTI+'formBuscarProductos');
                    vGenerarCotizacionScript.calculoTotal();
                    vGenerarCotizacionScript.calculoTotalFilaUp();                                       
                    
                    $('.editable').summernote({
                        height : 100,
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
            container: '#'+diccionario.tabs.VCOTI+'gridProductos',
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
        $('#'+diccionario.tabs.VCOTI+'tr_'+idx).remove();
        vGenerarCotizacionScript.calculoTotal();
    };
    
    this.publico.getIndex = function(){        
        return _private.index;
    };
    
    this.publico.setIndex = function(){
        _private.index = _private.index + 1;
        _private.productoAdd.push(_private.index);         
    };            
    
    this.publico.calculoTotal = function(){
        var collection = $('#'+diccionario.tabs.VCOTI+'gridProductos').find('tbody').find('tr');
        var t = 0;
        $.each(collection,function(){
            var tt = simpleScript.deleteComa($.trim($(this).find('td:eq(6)').text()));
            if(tt.length > 0){
                t += parseFloat(tt);
            }
        });
        _private.total = t;
        $('#'+diccionario.tabs.VCOTI+'txt_total').val(_private.total.toFixed(2));
    };
    
    this.publico.calculoTotalFilaUp = function(){
        var collection = $('#'+diccionario.tabs.VCOTI+'gridProductos').find('tbody').find('tr');
        
        $.each(collection,function(){
            var tthis = $(this);
            
            /*keyup para cantidad 1*/
            $(this).find('td:eq(2)').find('input:text').keyup(function(){
                if(isNaN($(this).val()) || $(this).val() < 0  || $(this).val() == '' ){
                    $(this).val(1);
                }
                var index = $(this).attr('data-index');
                var cn = $(this).val();
                var cn2 =  simpleScript.deleteComa($('#'+diccionario.tabs.VCOTI+index+'txt_cantidad2').val());                    
                var precio = simpleScript.deleteComa($('#'+diccionario.tabs.VCOTI+index+'txt_precio').val());

                cn = cn.replace(",","");
                cn2 = cn2.replace(",","");

                var ttc = parseFloat(cn) * parseFloat(cn2);                    
                 tthis.find('td:eq(4)').html(ttc.toFixed(2));

                var total = 0;
                total = parseFloat(precio) * parseFloat(ttc); 

                tthis.find('td:eq(6)').html(total.toFixed(2));

                vGenerarCotizacionScript.calculoTotal();
                
            });
            
            /*keyup para Cantidad 2*/
            $(this).find('td:eq(3)').find('input:text').keyup(function(){
                if(isNaN($(this).val()) || $(this).val() < 0  || $(this).val() == '' ){
                    var d = $(this).attr('data-value');
                    $(this).val(d);
                }
                var index = $(this).attr('data-index');
                var cn = $(this).val();
                var cn2 =  simpleScript.deleteComa($('#'+diccionario.tabs.VCOTI+index+'txt_cantidad1').val());                  
                var precio = simpleScript.deleteComa($('#'+diccionario.tabs.VCOTI+index+'txt_precio').val());

                cn = cn.replace(",","");
                cn2 = cn2.replace(",","");

                var ttc = parseFloat(cn) * parseFloat(cn2);

                var total = 0;
                total = parseFloat(precio) * parseFloat(ttc); 

                tthis.find('td:eq(4)').html(ttc.toFixed(2));
                tthis.find('td:eq(6)').html(total.toFixed(2));
                vGenerarCotizacionScript.calculoTotal();
                
            });
            
            /*keyup para precio*/
            $(this).find('td:eq(5)').find('input:text').keyup(function(){
                if(isNaN($(this).val()) || $(this).val() <= 0 || $(this).val() == ''  ){
                    var d = $(this).attr('data-value');
                    $(this).val(d);
                }
                var index = $(this).attr('data-index');
                var pr = $(this).val();                  
                var cn = simpleScript.deleteComa($('#'+diccionario.tabs.VCOTI+index+'txt_cantidad1').val());
                var cn2 = simpleScript.deleteComa($('#'+diccionario.tabs.VCOTI+index+'txt_cantidad2').val());
                var ttc = parseFloat(cn) * parseFloat(cn2);

                pr = pr.replace(",","");

                var total = 0;
                total = parseFloat(pr) * parseFloat(ttc);                   

                tthis.find('td:eq(6)').html(total.toFixed(2));
                vGenerarCotizacionScript.calculoTotal();
            }); 
        });
    };
    
    this.publico.resetArrayProducto = function(){
        _private.productoAdd = [];
        _private.total = 0;
        _private.index = 0;
    };
    
    return this.publico;
    
};
var vGenerarCotizacionScript = new vGenerarCotizacionScript_();