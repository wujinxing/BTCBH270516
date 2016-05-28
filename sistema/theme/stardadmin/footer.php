<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)
<script data-pace-options='{ "restartOnRequestAfter": true }' src="js/plugin/pace/pace.min.js"></script>-->
<?php if (DB_ENTORNO == 'P') $min = '.min'; ?>
<script src="<?php echo $rutaLayout['_js']; ?>plugin/pace/pace.min.js"></script>

<script src="<?php echo $rutaLayout['_js']; ?>libs/jquery-2.0.2.min.js"></script>
<script src="<?php echo $rutaLayout['_js']; ?>libs/jquery-ui-1.10.3.min.js"></script>

<!-- JS TOUCH : include this plugin for mobile drag / drop touch events
<script src="<?php echo $rutaLayout['_js']; ?>plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script> -->

<!-- BOOTSTRAP JS -->
<script src="<?php echo $rutaLayout['_js']; ?>bootstrap/bootstrap.min.js"></script>

<!-- CUSTOM NOTIFICATION -->
<script async src="<?php echo $rutaLayout['_js']; ?>notification/SmartNotification.min.js"></script>

<!-- JARVIS WIDGETS -->
<script src="<?php echo $rutaLayout['_js']; ?>smartwidgets/jarvis.widget.min.js"></script>

<!-- EASY PIE CHARTS -->
<script src="<?php echo $rutaLayout['_js']; ?>plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>

<!-- WIZARD -->
<script src="<?php echo $rutaLayout['_js']; ?>plugin/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
<script src="<?php echo $rutaLayout['_js']; ?>plugin/fuelux/wizard/wizard.js"></script>

<!-- SPARKLINES -->
<script async src="<?php echo $rutaLayout['_js']; ?>plugin/sparkline/jquery.sparkline.min.js"></script>

<!-- JQUERY VALIDATE -->
<script src="<?php echo $rutaLayout['_js']; ?>plugin/jquery-validate/jquery.validate.min.js"></script>

<!-- JQUERY MASKED INPUT -->
<script async src="<?php echo $rutaLayout['_js']; ?>plugin/masked-input/jquery.maskedinput.min.js"></script>

<!-- JQUERY SELECT2 INPUT -->
<script async src="<?php echo $rutaLayout['_js']; ?>plugin/select2/select2.js"></script>

<!-- JQUERY DROPZONE -->
<script async src="<?php echo $rutaLayout['_js']; ?>plugin/dropzone/dropzone.min.js"></script>

<!-- JQUERY UI + Bootstrap Slider -->
<script src="<?php echo $rutaLayout['_js']; ?>plugin/bootstrap-slider/bootstrap-slider.min.js"></script>

<script src="<?php echo $rutaLayout['_js']; ?>plugin/jquery-timepicker/jquery.timepicker.min.js"></script>
<script src="<?php echo $rutaLayout['_js']; ?>plugin/jquery-datepair/dist/datepair.min.js"></script>
<script src="<?php echo $rutaLayout['_js']; ?>plugin/jquery-datepair/dist/jquery.datepair.min.js"></script>

<script src="<?php echo $rutaLayout['_js']; ?>plugin/bootstrap-tags/bootstrap-tagsinput.min.js"></script>

<!-- GRAFICOS -->
<script src="<?php echo BASE_URL ?>public/js/amcharts/amcharts.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL ?>public/js/amcharts/serial.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL ?>public/js/amcharts/pie.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL ?>public/js/amcharts/funnel.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL ?>public/js/amcharts/gauge.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL ?>public/js/amcharts/radar.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL ?>public/js/amcharts/xy.js" type="text/javascript"></script>

<script src="<?php echo BASE_URL ?>public/js/amcharts/themes/black.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL ?>public/js/amcharts/themes/chalk.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL ?>public/js/amcharts/themes/dark.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL ?>public/js/amcharts/themes/light.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL ?>public/js/amcharts/themes/patterns.js" type="text/javascript"></script>

<!-- scripts for exporting chart as an image -->
<!-- Exporting to image works on all modern browsers except IE9 (IE10 works fine) -->
<!-- Note, the exporting will work only if you view the file from web server -->
<!--[if (!IE) | (gte IE 10)]> -->
<script src="<?php echo BASE_URL ?>public/js/amcharts/exporting/amexport.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL ?>public/js/amcharts/exporting/rgbcolor.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL ?>public/js/amcharts/exporting/canvg.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL ?>public/js/amcharts/exporting/jspdf.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL ?>public/js/amcharts/exporting/filesaver.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL ?>public/js/amcharts/exporting/jspdf.plugin.addimage.js" type="text/javascript"></script>

<!-- browser msie issue fix -->
<script src="<?php echo $rutaLayout['_js']; ?>plugin/msie-fix/jquery.mb.browser.min.js"></script>

<script src="<?php echo $rutaLayout['_js']; ?>plugin/datatables/jquery.dataTables-cust.js"></script> 
<script src="<?php echo $rutaLayout['_js']; ?>plugin/datatables/ColReorder.min.js"></script> 
<script src="<?php echo $rutaLayout['_js']; ?>plugin/datatables/FixedColumns.min.js"></script> 
<script src="<?php echo $rutaLayout['_js']; ?>plugin/datatables/ColVis.min.js"></script> 
<script src="<?php echo $rutaLayout['_js']; ?>plugin/datatables/ZeroClipboard.js"></script> 
<script src="<?php echo $rutaLayout['_js']; ?>plugin/datatables/media/js/TableTools.min.js"></script> 
<script src="<?php echo $rutaLayout['_js']; ?>plugin/datatables/DT_bootstrap.js"></script> 

<!-- FastClick: For mobile devices: you can disable this in app.js-->
<script src="<?php echo $rutaLayout['_js']; ?>plugin/fastclick/fastclick.js"></script> 

<!--[if IE 7]>

<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>

<![endif]-->
<!-- Demo purpose only -->
<script src="<?php echo $rutaLayout['_js']; ?>demo<?php echo $min;?>.js"></script>
<!-- MAIN APP JS FILE -->
<script src="<?php echo $rutaLayout['_js']; ?>app<?php echo $min;?>.js"></script>
<script async src="<?php echo BASE_URL ?>public/js/cropit/dist/jquery.cropit.js"></script>
<script src="<?php echo BASE_URL ?>lang/js/lang_es.js"></script>

<script src="<?php echo BASE_URL ?>libs/Aes/js/aes<?php echo $min;?>.js"></script>
<script src="<?php echo BASE_URL ?>libs/Aes/js/aesctr<?php echo $min;?>.js"></script>
<script src="<?php echo BASE_URL ?>libs/Aes/js/base64<?php echo $min;?>.js"></script>
<script src="<?php echo BASE_URL ?>libs/Aes/js/utf8<?php echo $min;?>.js"></script>

<?php require_once (ROOT . 'app' . DS . 'ConstantesJsD.php'); ?>
<script src="<?php echo BASE_URL ?>public/js/simpleAjax<?php echo $min;?>.js"></script>
<script src="<?php echo BASE_URL ?>public/js/simpleScript<?php echo $min;?>.js"></script>

<script src="<?php echo BASE_URL ?>public/js/scrollTable<?php echo $min;?>.js"></script>
<script async src="<?php echo BASE_URL;?>libs/jquery.PrintArea<?php echo $min;?>.js" type="text/javascript"></script>
<script async src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=<?php echo KEY_APP_GOOGLE; ?>"></script>

<?php
Obj::run()->Autoload->js('modules/', true);
?><script>
    pageSetUp();
    $('input[type="checkbox"]#smart-fixed-nav').click();
    $('input[type="checkbox"]#smart-fixed-ribbon').click();
    /*$('input[type="checkbox"]#smart-fixed-navigation').click();*/        
</script>
<script>
    function noneEvt(){
        /*para hacer evento invisible*/
        simpleScript.removeAttr.click({
            container: '#shortcut',
            typeElement: 'li a'
        });
    }
    setTimeout('noneEvt()',10000);
</script>
<?php if(Session::get('sys_usuario')):?>
<script>
    <?php if (isset($_GET['sincr']) && $_GET['sincr'] == 'ok' &&  Session::get('sys_pic') !== 'X' ): ?>
        simpleScript.asyncJs({fn: function(){
                simpleScript.notify.ok({
                    content: lang.mensajes.MSG_53
                });                                
        }, tiempo:2000 });
        
    <?php endif; ?>
    var activityTimeout = 0;
    function test() {
        var activityTimeout = null;
        $(document).mousemove(function(event) {
            if (activityTimeout) {
                clearTimeout(activityTimeout);
            }
            activityTimeout = setTimeout(function() {
                index.inactividad();
            }, <?php echo  Session::get('sys_tiempoBloqueo');?> );
        });
    }    
  test();    
</script>
<?php endif; ?>
</body>  
</html>
<?php ob_end_flush(); ?>                   