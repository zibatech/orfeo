<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title></title>
  <link rel="stylesheet" href="//apps.bdimg.com/libs/jqueryui/1.10.4/css/jquery-ui.min.css">
  <script src="//apps.bdimg.com/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="//apps.bdimg.com/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
  <style>
  .ui-autocomplete-loading {
    background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat;
  }
  #city { width: 25em; }
  </style>
  <script>
  $(function() {
    function log( message ) {
      $( "<div>" ).text( message ).prependTo( "#log" );
      $( "#log" ).scrollTop( 0 );
    }
 
    $( "#city" ).autocomplete({
      source: function( request, response ) {
      query = $( "#tags" ).val();
        $.ajax({
          url: "../radicacion/ajax_buscarDivipola.php",
          //dataType: "jsonp",
          type: "POST",
          data: {
            action: 'diviPola', 
            featureClass: "P",
            style: "full",
            maxRows: 12,
            search: request.term
          },
          success: function( data ) {
            response( $.map( data, function( item ) {
              return {
                label: item.MUNI_NOMB + ", " + item.DPTO_NOMB + ", " + item.NOMBRE_PAIS,
                value: item.MUNI_NOMB + ", " + item.DPTO_NOMB + ", " + item.NOMBRE_PAIS,
              }
            }));
          }
        });
      },
      minLength: 2,
      select: function( event, ui ) {
        log( ui.item ?
          "Selected: " + ui.item.label :
          "Nothing selected, input was " + this.value);
      },
      open: function() {
        $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
      },
      close: function() {
        $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
      }
    });
  });
  </script>
</head>
<body>
 
<div class="ui-widget">
  <label for="city"></label>
  <input id="city">
</div>
</body>
</html>