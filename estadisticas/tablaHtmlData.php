<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.dataTables.min.css">
  
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.flash.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.print.min.js"></script>

  <section id="widget-grid">
      <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="false">
            <div class="widget-body">
            <?php
             
            foreach ($_GET as $key => $valor)   ${$key} = $valor;
            
              require_once($ruta_raiz."/include/myPaginadorData.inc.php");
              $paginador=new myPaginador($db,$queryE,$orden);

              //$_SESSION["tipoEstadistica"] = $tipoEstadistica;
              $paginador->moreLinks = "&tipoEstadistica=".$_GET['tipoEstadistica']."&";

              if(!isset($_GET['genDetalle'])){
                $orden=isset($orden)?$orden:"";
                $paginador->setFuncionFilas("pintarEstadistica");
              } else {
                $paginador->setFuncionFilas("pintarEstadisticaDetalle");
              }
              $paginador->setImagenASC($ruta_raiz."iconos/flechaasc.gif");
              $paginador->setImagenDESC($ruta_raiz."iconos/flechadesc.gif");
              //$paginador->setPie($pie);
              echo $paginador->generarPagina($titulos,"titulos3");

              if(!isset($_GET['genDetalle'])&& $paginador->getTotal() > 0){
                $total=$paginador->getId()."_total";
                if(!isset($_REQUEST[$total])) {
                    $db->conn->SetFetchMode(ADODB_FETCH_NUM);
                $res = $db->conn->query($queryE);
                $datos=0;

                while(!$res->EOF){
                  $data1y[]=$res->fields[1];
                  $nombUs[]=$res->fields[0];
                  $res->MoveNext();
                }

                $nombYAxis=substr($titulos[1],strpos($titulos[1],"#")+1);
                $nombXAxis=substr($titulos[2],strpos($titulos[2],"#")+1);
                  $nume_ale = rand(1,100);
                $nombreGraficaTmp = $ruta_raiz."bodega/tmp/E_$nume_ale.png";
                $rutaImagen = $nombreGraficaTmp;
                if(file_exists($rutaImagen)){
                  unlink($rutaImagen);
                }
                $notaSubtitulo = $subtituloE[$tipoEstadistica]."\n";
                $tituloGraph = $tituloE[$tipoEstadistica];
                //include "genBarras1.php";
              }
            }

          if ($tipoEstadistica!=1000 )  {
                  if ($genTodosDetalle != 1 or $genDetalle != 1) {
                  echo "<center><a href=\"genEstadistica.php?$datosEnvioDetalle&genTodosDetalle=1&$datosaenviar&genDetalle=1\" Target=\"VerDetalle".date("dmYHis")."\" class=\"btn btn-sm btn-primary\"> Ver todos los detalles</a></center><br/>";
                  }
                }
            ?>
           </div>
        </div>
      </article>
  </section>
 
</body>
</html>

<script> 

$(document).ready(function() {
    $('#muchasid').DataTable( {
        dom: 'Bfrtip',
        lengthMenu: [[1000, 2000, 3000, -1], [1000, 2000, 3000, "All"]],
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        "footerCallback": function(row, data, start, end, display) {
            var api = this.api(),
                data;

            // Remove the formatting to get integer data for summation
            var intVal = function(i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,.]/g, '') * 1 :
                    typeof i === 'number' ?
                    i : 0;
            };

            // Total over all pages
            total = api
                .column(2)
                .data()
                .reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

          
            // Update footer
            $(api.column(2).footer()).html(
                'Total: ' + number_format(total) + ''
            );

        }
    } );

    
    function number_format(amount, decimals) {

      amount += ''; // por si pasan un numero en vez de un string
      amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

      decimals = decimals || 0; // por si la variable no fue fue pasada

      // si no es un numero o es igual a cero retorno el mismo cero
      if (isNaN(amount) || amount === 0)
          return parseFloat(0).toFixed(decimals);

      // si es mayor o menor que cero retorno el valor formateado como numero
      amount = '' + amount.toFixed(decimals);

      var amount_parts = amount.split('.'),
          regexp = /(\d+)(\d{3})/;

      while (regexp.test(amount_parts[0]))
          amount_parts[0] = amount_parts[0].replace(regexp, '$1' + '.' + '$2');

      return amount_parts.join('.');
    }

} );
</script>



