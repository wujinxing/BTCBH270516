/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 07-11-2014 00:11:47 
* Descripcion : vproducto.js
* ---------------------------------------
*/
var vproducto_ = function(){
    
    /*metodos privados*/
    var _private = {};
    
    _private.idVproducto = 0;
    _private.mapaSitio = '';
    
    _private.config = {
        modulo: "ventas/vproducto/"
    };

    /*metodos publicos*/
    this.publico = {};
    
    /*crea tab : Vproducto*/
    this.publico.main = function(element){
        simpleScript.addTab({
            id : diccionario.tabs.VPROD,
            label: $(element).attr("title"),
            fnCallback: function(){
                _private.mapaSitio = $(element).data("sitemap"); 
                vproducto.getContenido();
            }
        });
    };
    
    /*contenido de tab: Vproducto*/
    this.publico.getContenido = function(){
        simpleAjax.send({
            dataType: "html",
            root: _private.config.modulo,
            data: '&_siteMap='+ _private.mapaSitio,
            fnCallback: function(data){
                $("#"+diccionario.tabs.VPROD+"_CONTAINER").html(data);
                vproducto.getGridVproducto();
            }
        });
    };
    
    this.publico.getGridVproducto = function (){
        var oTable = $("#"+diccionario.tabs.VPROD+"gridVproducto").dataTable({
            bProcessing: true,
            bServerSide: true,
            bDestroy: true,
            sPaginationType: "bootstrap_full", //two_button
            sServerMethod: "POST",
            bPaginate: true,
            iDisplayLength: 10,            
            aoColumns: [
                {sTitle: lang.generico.NRO, sWidth: "5%", sClass: "center"},
                {sTitle: lang.generico.DESCRIPCION, sWidth: "45%"},
                {sTitle: lang.generico.UM, sWidth: "20%"},
                {sTitle: lang.generico.PRECIO, sWidth: "15%", sClass: "right"},
                {sTitle: lang.generico.ESTADO, sWidth: "10%", sClass: "center"},
                {sTitle: lang.generico.ACCIONES, sWidth: "8%", sClass: "center", bSortable: false}
            ],
            aaSorting: [[1, "asc"]],
            sScrollY: "350px",
            sAjaxSource: _private.config.modulo+"getGridVproducto",
            fnDrawCallback: function() {
                $("#"+diccionario.tabs.VPROD+"gridVproducto_filter").find("input").attr("placeholder",lang.busqueda.VPROD).css("width","350px");
                simpleScript.enterSearch("#"+diccionario.tabs.VPROD+"gridVproducto",oTable);
                /*para hacer evento invisible*/
                simpleScript.removeAttr.click({
                    container: "#widget_"+diccionario.tabs.VPROD,
                    typeElement: "button"
                });               
           }
        });
        setup_widgets_desktop();
    };
    
    this.publico.getFormNewVproducto = function(btn){
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormNewVproducto",
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.VPROD+"formNewVproducto").modal("show");
            }
        });
    };
    
    this.publico.getFormEditVproducto = function(btn,id){
        _private.idVproducto = id;
            
        simpleAjax.send({
            element: btn,
            dataType: "html",
            root: _private.config.modulo + "getFormEditVproducto",
            data: "&_idVproducto="+_private.idVproducto,
            fnCallback: function(data){
                $("#cont-modal").append(data);  /*los formularios con append*/
                $("#"+diccionario.tabs.VPROD+"formEditVproducto").modal("show");
            }
        });
    };
    
    this.publico.postNewVproducto = function(){
        simpleAjax.send({             
            element: "#"+diccionario.tabs.VPROD+"btnGrVproducto",
            root: _private.config.modulo + "postNewVproducto",
            form: "#"+diccionario.tabs.VPROD+"formNewVproducto",
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_3,
                        callback: function(){
                            vproducto.getGridVproducto();
                            _private.idVproducto = 0;
                            setTimeout(function(){
                                $("#"+diccionario.tabs.VPROD+"lst_unidadMedida").val('');    
                            },100);
                            
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: "Producto ya existe."
                    });
                }
            }
        });
    };
    
    this.publico.postEditVproducto = function(){
        simpleAjax.send({             
            element: "#"+diccionario.tabs.VPROD+"btnEdVproducto",
            root: _private.config.modulo + "postEditVproducto",
            form: "#"+diccionario.tabs.VPROD+"formEditVproducto",
            data: "&_idVproducto="+_private.idVproducto,
            clear: true,
            fnCallback: function(data) {
                if(!isNaN(data.result) && parseInt(data.result) === 1){
                    simpleScript.notify.ok({
                        content: lang.mensajes.MSG_19,
                        callback: function(){
                            _private.idVproducto = 0;
                            simpleScript.closeModal("#"+diccionario.tabs.VPROD+"formEditVproducto");
                            simpleScript.reloadGrid("#"+diccionario.tabs.VPROD+"gridVproducto");
                        }
                    });
                }else if(!isNaN(data.result) && parseInt(data.result) === 2){
                    simpleScript.notify.error({
                        content: "Producto ya existe."
                    });
                }
            }
        });
    };       
    
    this.publico.postDeleteVproducto = function(btn, id){
    
        simpleScript.notify.confirm({
            content: lang.mensajes.MSG_7,
            callbackSI: function(){
                simpleAjax.send({
                    flag: 3, //si se usa SP usar flag, sino se puede eliminar esta linea
                    element: btn,
                    form: "#"+diccionario.tabs.VPROD+"formGridVproducto",
                    data: "&_idVproducto="+id,
                    root: _private.config.modulo + "postDeleteVproducto",
                    fnCallback: function(data) {
                        if(!isNaN(data.result) && parseInt(data.result) === 1){
                            simpleScript.notify.ok({
                                content: lang.mensajes.MSG_8,
                                callback: function(){
                                    vproducto.getGridVproducto();
                                }
                            });
                        }
                    }
                });
            }
        });
            
    };
    
    this.publico.postDesactivar = function(btn,id){
              simpleAjax.send({
                  element: btn,
                  root: _private.config.modulo + 'postDesactivar',
                  data: '&_idVproducto='+id,
                  clear: true,
                  fnCallback: function(data) {
                      if(!isNaN(data.result) && parseInt(data.result) === 1){
                          simpleScript.notify.ok({
                              content: 'Producto se desactivo correctamente',
                              callback: function(){
                                   simpleScript.reloadGrid("#"+diccionario.tabs.VPROD+"gridVproducto");
                              }
                          });
                      }
                  }
              });
          };

       this.publico.postActivar = function(btn,id){
           simpleAjax.send({
               element: btn,
               root: _private.config.modulo + 'postActivar',
               data: '&_idVproducto='+id,
               clear: true,
               fnCallback: function(data) {
                   if(!isNaN(data.result) && parseInt(data.result) === 1){
                       simpleScript.notify.ok({
                           content: 'Producto se activo correctamente',
                           callback: function(){
                               simpleScript.reloadGrid("#"+diccionario.tabs.VPROD+"gridVproducto");
                           }
                       });
                   }
               }
           });
       };         
        
    
    return this.publico;
    
};
var vproducto = new vproducto_();