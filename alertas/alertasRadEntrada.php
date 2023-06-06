<?php
$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$codusuario     = $_SESSION["codusuario"];
if (!$_SESSION['dependencia']) header ("Location: $ruta_raiz/cerrar_session.php");

 $ruta_raiz = "..";
 include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");


$db = new ConnectionHandler($ruta_raiz);


//Numero de dias habiles para el calculo.
$numero_dias = 2;

//CALCULO DE FECHAS
$hoy = date('Y-m-d');
$anio = (date("Y")-2)."-01-01";

$whereJefe="";
if($codusuario!=1 and (empty($_SESSION["USUA_PERM_ENRUTADOR"]) || $_SESSION["USUA_PERM_ENRUTADOR"]==0)) {
   $whereJefe = " and r.radi_usua_actu=$codusuario ";

}
$iSql = "select r.sgd_trad_codigo, a.anex_estado, count(*) cantidad_radicados, count(renv.sgd_fenv_codigo) cantidad_enviados from radicado r
            left outer join 
              (select anex_radi_nume, max(anex_estado) anex_estado, max(anex_salida) anex_salida from anexos group by anex_radi_nume) a 
            on (r.radi_nume_radi=a.anex_radi_nume and a.anex_salida>=1)
            left outer join sgd_renv_regenvio renv on (r.radi_nume_radi = renv.radi_nume_sal and renv.sgd_fenv_codigo <>0)
            where r.sgd_trad_codigo in (2,4,5)
               and r.radi_depe_actu=$dependencia $whereJefe
              group by r.sgd_trad_codigo, a.anex_estado
              order by  r.sgd_trad_codigo, a.anex_estado
        ";
  //$db->conn->debug = true;
  $rs=$db->conn->query($iSql);
 //RECORRO DATO POR DATO, Y POR CADA UNO, ENVIO UN CORREO ELECTRONICO.
$arrRadicadosEstado = array();
for($i=0; $i<=4; $i++){
 $arrRadicadosEstado[2][$i]="0";
 $arrRadicadosEstado[4][$i]="0";
 $arrRadicadosEstado[5][$i]="0";
}
 while (!$rs->EOF) {//ESTE ES EL WHILE
   
   $db = new ConnectionHandler($ruta_raiz);
   $tipoRadicado       = $rs->fields['SGD_TRAD_CODIGO'];
   $cantidadRadicados  = $rs->fields['CANTIDAD_RADICADOS'];
   $cantidadEnviados   = $rs->fields['CANTIDAD_ENVIADOS'];
   if(!$rs->fields['ANEX_ESTADO']) $anexEstado=0; else $anexEstado = $rs->fields['ANEX_ESTADO'];
   if($anexEstado==0) 
     {

      if($cantidadEnviados>=1 and ($anexEstado==0 || !$anexEstado)) {
          $arrRadicadosEstado[$tipoRadicado][4] +=$cantidadEnviados ;
          $arrRadicadosEstado[$tipoRadicado][0] +=$cantidadRadicados-$cantidadEnviados;
       } else{
          $arrRadicadosEstado[$tipoRadicado][0] +=$cantidadRadicados ;
        }
      

   }else{
     $arrRadicadosEstado[$tipoRadicado][$anexEstado] +=$cantidadRadicados;
   }
  
   
   $radicadosEstado .= "$tipoRadicado  - $anexEstado - $cantidadRadicados<br>";
//$mailDestino = "cejebuto@gmail.com";
  $rs->MoveNext();
}//FIN DEL WHILE










$time = date("G:i:s");
$dia = date('Y-m-d');

$charRadicadosEstado = "
    var chartData = {
      labels: ['Entrada', 'Resoluciones','Autos'],
      datasets: [{
        type: 'bar',
        label: 'No Tramitados',
        fontSize: 6,
        backgroundColor: '#fd1004',
        borderWidth: 2,
        fill: false,
        data: [".$arrRadicadosEstado[2][0].",".$arrRadicadosEstado[5][0].",".$arrRadicadosEstado[4][0]."]
      },  {
        type: 'bar',
        label: 'Respuesta Sin Enviar',
        backgroundColor: '#fdf404',
        data: [".$arrRadicadosEstado[2][2].",".$arrRadicadosEstado[5][2].",".$arrRadicadosEstado[4][2]."]
      }, {
        type: 'bar',
        label: 'Por Enviar',
        backgroundColor: '#d1fd04',
        data: [".$arrRadicadosEstado[2][3].",".$arrRadicadosEstado[5][3].",".$arrRadicadosEstado[4][3]."]
      }, {
        type: 'bar',
        label: 'Enviado Ok',
        backgroundColor: '#04fd04',
        data: [".$arrRadicadosEstado[2][4].",".$arrRadicadosEstado[5][4].",".$arrRadicadosEstado[5][4]."]
      }]
    };
    
      var ctx = document.getElementById('chartRadicadosEstado').getContext('2d');
      window.myMixedChart = new Chart(ctx, {
        type: 'bar',
        data: chartData,
        options: {
          responsive: true,
          tooltips: {
            mode: 'index',
            intersect: true
          },
        legend: {
            display: true,
            labels: {
                fontSize: 12
            }
        }


,
        scales: {
          xAxes: [{
            ticks: {
              fontSize: 12,
              fontColor: 'black'
            }
          }],
          yAxes: [{
            ticks: {
              fontSize: 12,
              fontColor: 'black'
            }
          }]
        }



        }

      });
  ";
?>
