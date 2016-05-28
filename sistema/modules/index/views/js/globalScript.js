var globalScript_ = function(){
    
    var _private = {};
    
    _private.config = {
        modulo: 'index/index/'
    };
        
    this.publico = {};

    this.publico.number_format = function(number, decimals, dec_point, thousands_sep) {
        //   example 1: number_format(1234.56);
        //   returns 1: '1,235'
        //   example 2: number_format(1234.56, 2, ',', ' ');
        //   returns 2: '1 234,56'
        //   example 3: number_format(1234.5678, 2, '.', '');
        //   returns 3: '1234.57'
        //   example 4: number_format(67, 2, ',', '.');
        //   returns 4: '67,00'
        //   example 5: number_format(1000);
        //   returns 5: '1,000'
        //   example 6: number_format(67.311, 2);
        //   returns 6: '67.31'
        //   example 7: number_format(1000.55, 1);
        //   returns 7: '1,000.6'
        //   example 8: number_format(67000, 5, ',', '.');
        //   returns 8: '67.000,00000'
        //   example 9: number_format(0.9, 0);
        //   returns 9: '1'
        //  example 10: number_format('1.20', 2);
        //  returns 10: '1.20'
        //  example 11: number_format('1.20', 4);
        //  returns 11: '1.2000'
        //  example 12: number_format('1.2000', 3);
        //  returns 12: '1.200'
        //  example 13: number_format('1 000,50', 2, '.', ' ');
        //  returns 13: '100 050.00'
        //  example 14: number_format(1e-8, 8, '.', '');
        //  returns 14: '0.00000001'

        number = (number + '')
          .replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
          prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
          sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
          dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
          s = '',
          toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k)
              .toFixed(prec);
          };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
          .split('.');
        if (s[0].length > 3) {
          s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '')
          .length < prec) {
          s[1] = s[1] || '';
          s[1] += new Array(prec - s[1].length + 1)
            .join('0');
        }
        return s.join(dec);
      }
    
    
   this.publico.convertirHora12 = function(time) {
        // Check correct time format and split into components
        time = time.toString ().match (/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];

        if (time.length > 1) { // If time format correct
          time = time.slice (1);  // Remove full string match value
          time[5] = +time[0] < 12 ? 'AM' : 'PM'; // Set AM/PM
          time[0] = +time[0] % 12 || 12; // Adjust hours
        }
        return time.join (''); // return adjusted time or original string
    }
    
    this.publico.convertirHora24 = function(time) {           
        
        var hours = Number(time.match(/^(\d\d?)/)[1]);
        var minutes = Number(time.match(/:(\d\d?)/)[1]);
        var AMPM = $.trim(time.match(/(AM|PM)$/i)[1]);
        if (AMPM === 'PM' && hours < 12){ 
            hours = hours + 12;
        }else if (AMPM === 'AM' && hours === 12){
            hours = hours - 12;               
        }        
               
        var sHours = hours.toString();
        var sMinutes = minutes.toString();
        
        if(hours<10)
            sHours = "0" + sHours;
        else if(minutes<10){    
            sMinutes = "0" + sMinutes;                        
        }
            
        
        return sHours +':'+ sMinutes; 
    }
    
    this.publico.limitAttach = function(tField,iType) { 
        var file = tField.value; 
        var extArray, allowSubmit ;
        if (iType==1) { 
            extArray = new Array(".gif",".jpg",".jpeg",".png",".GIF",".JPG",".PNG",".JPEG");            
        } 
        if (iType==2) { 
            extArray = new Array(".swf"); 
        } 
        if (iType==3) { 
            extArray = new Array(".exe",".sit",".zip",".tar",".swf",".mov",".hqx",".ra",".wmf",".mp3",".qt",".med",".et"); 
        } 
        if (iType==4) { 
            extArray = new Array(".mov",".ra",".wmf",".mp3",".qt",".med",".et",".wav"); 
        } 
        if (iType==5) { 
            extArray = new Array(".html",".htm",".shtml"); 
        } 
        if (iType==6) { 
            extArray = new Array(".doc",".xls",".ppt"); 
        } 
        allowSubmit = false; 
        if (!file) return; 
        while (file.indexOf("\\") != -1) file = file.slice(file.indexOf("\\") + 1); 
            ext = file.slice(file.indexOf(".")).toLowerCase(); 
        for (var i = 0; i < extArray.length; i++) { 
            if (extArray[i] == ext) { 
                allowSubmit = true; 
                break; 
            } 
        } 
        if (!allowSubmit){            
            simpleScript.notify.warning({
                content: "Usted sólo puede subir archivos con extensiones " + (extArray.join(" ")) + "\nPor favor seleccione un nuevo archivo"
            });            
        } 
    }  
    
    this.publico.generarLink = function(t){                        
        var from = "ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÑñÇç&{}()^~[]/", 
             to   = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuunnccy         ",
             mapping = {},
             r = '';

        for(var i = 0, j = from.length; i < j; i++ )
            mapping[ from.charAt( i ) ] = to.charAt( i );
     
        var str = $.trim($(t).val());
        var ret = [];
        for( var i = 0, j = str.length; i < j; i++ ) {
            var c = str.charAt( i );
            if( mapping.hasOwnProperty( str.charAt( i ) ) )
                ret.push( mapping[ c ] );
            else
                ret.push( c );
        }
        r = ret.join( '' ).replace( /[^-A-Za-z0-9]+/g, '-' ).toLowerCase();;            
        return r;                      
    };    
    
    
    this.publico.deleteArchivo = function(archivo){
        setTimeout(function(){
            simpleAjax.send({
                root: _private.config.modulo + 'deleteArchivo',
                data: '&_archivo='+archivo
            });
        },56000);
    };    

    return this.publico;
    
};
var globalScript = new globalScript_();
