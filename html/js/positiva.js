$(document).foundation();

$(function() {
    var opts = {
      lines: 9, // The number of lines to draw
      length: 2, // The length of each line
      width: 6, // The line thickness
      radius: 10, // The radius of the inner circle
      corners: 1, // Corner roundness (0..1)
      rotate: 0, // The rotation offset
      direction: 1, // 1: clockwise, -1: counterclockwise
      color: '#fff', // #rgb or #rrggbb or array of colors
      speed: 0.9, // Rounds per second
      trail: 61, // Afterglow percentage
      shadow: false, // Whether to render a shadow
      hwaccel: false, // Whether to use hardware acceleration
      className: 'spinner', // The CSS class to assign to the spinner
      zIndex: 2e9, // The z-index (defaults to 2000000000)
      top: 'auto', // Top position relative to parent in px
      left: 'auto' // Left position relative to parent in px
    };

    var target = document.getElementById('loading');
    var spinner = new Spinner(opts).spin(target);
});

$(function() {
    $(".fixed-footer-top-trigger").waypoint(function (d) {
        return "down" === d ? $(".fixed-footer").addClass("is-fixed") : $(".fixed-footer").removeClass("is-fixed");
    });

    $(".fixed-footer-bottom-trigger").waypoint(function (d) {
        return "down" === d ? $(".fixed-footer").removeClass("is-fixed") : $(".fixed-footer").addClass("is-fixed");
    }, { offset: "100%" });
});

$(function(){
  $('#modelo').prop('disabled', true);
  $('#version').prop('disabled', true);
  $('#ano').prop('disabled', true);
  $( "#distrito" ).prop('disabled', true);
});

$(function() {
    $contact = $("#contact");
    $locate = $("#locate");
    $contact_locate = $("#contact, #locate");
    $("#contact-option-1").click(function() {
        $contact_locate.show();
        $contact.removeClass("large-centered medium-centered").addClass("large-uncentered medium-uncentered");
        $('#contact-form').attr("action","/?/content/gracias");
        $('#form_button').html("DESCARGAR COTIZACIÓN EN PDF");
    });
    $("#contact-option-2").click(function() {
        $locate.hide();
        $contact.show();
        $('#contact-form').attr("action","/?/content/gracias_phone");
        $('#form_button').html("CONTÁCTENME");
        $('#form_button').css("width", "100%");
        $contact.removeClass("large-uncentered medium-uncentered").addClass("large-centered medium-centered");
    });
});

$(function () {
  $select_suma = $("#select-suma");
  $suma_form = $("#suma_form");
  $suma = $("#suma");
  $c1 = $("#c1");

  $select_suma.change(function() {
      $("#tipo_respci").val($("#select-suma option:selected").text());
      value = $c1.val();
      $("#info-price-first").html("S/." + $select_suma.val());
      $c1.val($select_suma.val());
      if($c1.is(":checked")) {
          $("#respci").val($select_suma.val());
          resultado = parseInt($suma_form.val()) - value + parseInt($select_suma.val());
          $suma.html('S/.' + resultado);
          $suma_form.val(resultado);
      }
      else{
          $("#respci").val("No Contratado");
      }
  });
});

$(function () {
  init();
});

function init(){
  resultado ="";

  $('#c1').attr('checked',true);
  $('#c2').attr('checked',false);
  $('#c3').attr('checked',true);
  $('#c4').attr('checked',false);

  $('#respci').val($('#c1').val());
  $('#ptr').val("No Contratado");
  $('#c3_form').val($('#c3').val());
  $('#c4_form').val("No Contratado");
  $("#tipo_respci").val($("#select-suma option:selected").text());
  resultado = parseInt($('#c1').val()) +parseInt($('#c3').val());
  $('#suma_form').val(resultado);
  $('#suma').html('S/.'+resultado);
  count("#c3");
  count("#c4");
  $("#contact-form").attr("action","/content/gracias");
}

function count(checkbox){
     $(checkbox).click(function(){
         resultado="";
         if($( this ).is(':checked')){
             $( checkbox +'_form').val($(checkbox).val());
             resultado = parseInt($("#suma_form").val())+parseInt($(checkbox).val());
             $("#suma_form").val(resultado); 
             $('#suma').html('S/.'+resultado); 
         }
         else{
             resultado = parseInt($("#suma_form").val())-parseInt($(checkbox).val());
             $( checkbox +'_form').val("No Contratado");
             $("#suma_form").val(resultado); 
             $('#suma').html('S/.'+resultado); 
         }
     });
 }
 
 $("#c1").click(function(){
     resultado="";
     mensaje="";
     $("#info-price-first").html("S/." + $("#select-suma").val());
     $("c1").val($("#select-suma").val());

     if($( this ).is(':checked')){
         $("#respci").val($("#c1").val());
         resultado = parseInt($("#suma_form").val())+parseInt($("#c1").val());
         $("#suma_form").val(resultado); 
         $('#suma').html('S/.'+resultado);
         if($("#c2").is(':checked')){
            $("#ptr").val($("#c2").val());
            mensaje = '<p>LAS COBERTURAS SELECCIONADAS INCLUYEN <b>GRATIS</b> LA COBERTURA DE <b>ASISTENCIA VIAL</b>.</p>';
            $("#message").html(mensaje);
         }
         else{
            $("#ptr").val("No Contratado");
            mensaje = '<p>AGREGA LA COBERTURA DE <b>PÉRDIDA TOTAL POR ROBO</b> Y LLÉVATE <b>GRATIS</b> LA COBERTURA DE <b>ASISTENCIA VIAL</b>.</p>';
            $("#message").html(mensaje);
         } 
     }
     else{
         $("#respci").val("No Contratado");
         resultado = parseInt($("#suma_form").val())-parseInt($("#c1").val());
         $("#suma_form").val(resultado); 
         $('#suma').html('S/.'+resultado);
         if($("#c2").is(':checked')){
            $("#ptr").val($("#c2").val());
            mensaje = '<p>AGREGA LA COBERTURA DE <b>RESPONSABILIDAD CIVIL</b> Y LLÉVATE <b>GRATIS</b> LA COBERTURA DE <b>ASISTENCIA VIAL</b>.</p>';
            $("#message").html(mensaje);
         }
         else{
            $("#ptr").val("No Contratado");
            mensaje = '<p>SI SELECCIONAS LAS COBERTURAS DE <b>RESPONSABILIDAD CIVIL</b> + <b>PÉRDIDA TOTAL POR ROBO</b>, LLÉVATE <b>GRATIS</b> LA COBERTURA DE <b>ASISTENCIA VIAL</b>.</p>';
            $("#message").html(mensaje);      
         }  
     }
     $(document).foundation();
 });

 $("#c2").click(function(){
     resultado="";
     if($( this ).is(':checked')){
         $("#ptr").val($("#c2").val());
         resultado = parseInt($("#suma_form").val())+parseInt($("#c2").val());
         $("#suma_form").val(resultado); 
         $('#suma').html('S/.'+resultado);
         if($("#c1").is(':checked')){
             $("#respci").val($("#c1").val());
             mensaje = '<p>LAS COBERTURAS SELECCIONADAS INCLUYEN <b>GRATIS</b> LA COBERTURA DE <b>ASISTENCIA VIAL</b>.</p>';
             $("#message").html(mensaje);
         }
         else{
             $("#respci").val("No Contratado");
             mensaje = '<p>AGREGA LA COBERTURA DE <b>RESPONSABILIDAD CIVIL</b> Y LLÉVATE <b>GRATIS</b> LA COBERTURA DE <b>ASISTENCIA VIAL</b>.</p>';
             $("#message").html(mensaje);
         } 
     }
     else{
         $("#ptr").val("No Contratado");
         resultado = parseInt($("#suma_form").val())-parseInt($("#c2").val());
         $("#suma_form").val(resultado); 
         $('#suma').html('S/.'+resultado);
         if($("#c1").is(':checked')){
             $("#respci").val($("#c1").val());
             mensaje = '<p>AGREGA LA COBERTURA DE <b>PÉRDIDA TOTAL POR ROBO</b> Y LLÉVATE <b>GRATIS</b> LA COBERTURA DE <b>ASISTENCIA VIAL</b>.</p>';
             $("#message").html(mensaje);
         }
         else{
             $("#respci").val("No Contratado");
             mensaje = '<p>SI SELECCIONAS LAS COBERTURAS DE <b>RESPONSABILIDAD CIVIL</b> + <b>PÉRDIDA TOTAL POR ROBO</b>, LLÉVATE <b>GRATIS</b> LA COBERTURA DE <b>ASISTENCIA VIAL</b>.</p>';
             $("#message").html(mensaje); 
         }  
     }
     $(document).foundation();
 });


$( "#distrito" ).change(function (evt) {
var str = "";
$( "#distrito option:selected" ).each(function() {
str = $( this ).val();
if(str=='DISTRITO'){
}
else{
str = str.replace(/-/gi," ");
str = "distrito="+str;
evt.preventDefault();
$.ajax({
       type: "POST",
       url: "/?/content/agencia/",
       data: str, // serializes the form's elements.
       success: function(response)
       { // show response from the php script.
         $('#result').html(response);
       }
     });
}
});
});

$( "#region" ).change(function (evt) {
  var str = "";
  $('#distrito option:eq(0)').prop('selected', true);
  $( "#region option:selected" ).each(function() {
    str = $( this ).val();
    if(str=='REGIÓN'){
      $('#distrito option:eq(0)').prop('selected', true);
    }
    else{
    str = str.replace(/-/gi," ");
    str = "region="+str;
    evt.preventDefault();
    $.ajax({
           type: "POST",
           url: "/?/content/distrito/",
           data: str, // serializes the form's elements.
           success: function(response)
           { // show response from the php script.
             $('#distrito').html(response);
             $('#distrito').prop('disabled', false);
             /*$('#loading').css('visibility', 'hidden');*/
           }
         });
    }
  });
});

$("#ano").change(function ( evt ){
var str = "";
var url = "";
  $( "#ano option:selected" ).each(function() {
    str = $( this ).text();
    $("#form_ano").val(str);
    option = $( this ).val();
    option = option.replace(/,/gi,"");
    if(option == 0){
      url = "#";
    }else{
      if(option >= 15000){
        url = "/?/content/mas_de_15k";
      }
      else{
        if(option >= 10000){
          url = "/?/content/entre_10k_y_15k";
        }
        else{
          url = "/?/content/menor_de_10k";
        }
      }
    }
    $('#cotizar').attr('action', url); //this fails silently
  });
});

$( "#version" ).change(function (evt) {
  var str = "";
  $('#ano option:eq(0)').prop('selected', true);
  $('#ano').prop('disabled', true);
  $("#cotizar").attr("action","/#");
  $( "#version option:selected" ).each(function() {
    str = $( this ).val();
    if(str=='VERSIÓN'){
      $('#ano option:eq(0)').prop('selected', true);
      $('#form_ano').val("");
      $('#ano').prop('disabled', true);
      $("#cotizar").attr("action","#");
    }
    else{
    str = str.replace(/-/gi," ");
    $("#form_version").val(str);
    str = "version="+str+"&modelo="+$('#modelo').val()+"&marca="+$('#marca').val();
    evt.preventDefault();
    $.ajax({
           type: "POST",
           url: "/?/content/ano/",
           data: str, // serializes the form's elements.
           beforeSend : function (){
               $('#loading').css('visibility', 'visible');
            },
           success: function(response)
           { // show response from the php script.
             $('#ano').html(response);
             $('#ano').prop('disabled', false);
           },
           complete : function (){
                $('#loading').css('visibility', 'hidden');
            }
         });
    }
  });
});

$( "#modelo" ).change(function (evt) {
  var str = "";
  $('#version option:eq(0)').prop('selected', true);
  $('#ano option:eq(0)').prop('selected', true);
  $('#version').prop('disabled', true);
  $('#ano').prop('disabled', true);
  $("#cotizar").attr("action","/#");
  $( "#modelo option:selected" ).each(function() {
    str = $( this ).val();
    if(str==='MARCA'){
      $('#version option:eq(0)').prop('selected', true);
      $('#ano option:eq(0)').prop('selected', true);
      $('#version').prop('disabled', true);
      $('#ano').prop('disabled', true);
      $("#cotizar").attr("action","#");
    }
    else{
    str = str.replace(/-/gi," ");
    $("#form_modelo").val(str);
    str = "modelo="+str+"&marca="+$('#marca').val();
    evt.preventDefault();
    $.ajax({
           type: "POST",
           url: "/?/content/versiones/",
           data: str, // serializes the form's elements.
           beforeSend : function (){
               $('#loading').css('visibility', 'visible');
            },
           success: function(response)
           { // show response from the php script.
             $('#version').html(response);
             $('#version').prop('disabled', false);
           },
           complete : function (){
                $('#loading').css('visibility', 'hidden');
            }
         });
    }
  });
});

$( "#marca" )
.change(function (evt) {
  var str = "";
  $('#modelo option:eq(0)').prop('selected', true);
  $('#version option:eq(0)').prop('selected', true);
  $('#ano option:eq(0)').prop('selected', true);
  $('#modelo').prop('disabled', true);
  $('#version').prop('disabled', true);
  $('#ano').prop('disabled', true);
  $("#cotizar").attr("action","/#");
  $( "#marca option:selected" ).each(function() {
    str = $( this ).val();
    if(str=='MARCA'){
      $('#modelo option:eq(0)').prop('selected', true);
      $('#version option:eq(0)').prop('selected', true);
      $('#ano option:eq(0)').prop('selected', true);
      $('#modelo').prop('disabled', true);
      $('#version').prop('disabled', true);
      $('#ano').prop('disabled', true);
      $("#cotizar").attr("action","/#");
    }
    else{
    str = str.replace(/-/gi," ");
    $("#form_marca").val(str);
    str = "marca="+str;
    evt.preventDefault();
    $.ajax({
           type: "POST",
           url: "/?/content/modelos/",
           data: str, // serializes the form's elements.
           beforeSend : function (){
               $('#loading').css('visibility', 'visible');
            },
           success: function(response)
           { // show response from the php script.
             $('#modelo').html(response);
             $('#modelo').prop('disabled', false);
           },
           complete : function (){
                $('#loading').css('visibility', 'hidden');
            }
         });
    }
  });
});