<?php
  session_start();
  $ruta_raiz  = '..';

  include_once("$ruta_raiz/include/db/ConnectionHandler.php");
  $db     = new ConnectionHandler("$ruta_raiz");

  include '../processConfig.php';
  include '../include/tx/Tx.php';
  
  $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
  //$db->conn->debug =true;

  $codCarpeta = $_POST["codCarpeta"];
  $dato       = substr($codCarpeta,0,2);
  $rads       = $_POST["rads"];
  $krd        = $_SESSION["krd"];
  
  $carpetaPersonal = ($dato == 11)? 1 : 0;  
  $carpetaDestino = substr($codCarpeta,2,6);
  
  //  changeFolder( $radicados, $usuaLogin,$carpetaDestino,$carpetaTipo,$tomarNivel,$observa)


  if(trim($rads)) {
    $tx = new Tx($db);
    $rtaMove =  $tx->changeFolder($rads.'0', $krd, $carpetaDestino, $carpetaPersonal,0,''  );
    $msg = '';
  }else{
    $msg = "No vienen Radicados";
  }
  
  if ($rtaMove == 1) {
    echo "<div class='alert alert-success fade in'>
            <button class='close' data-dismiss='alert'> × </button>
            <!--i class='fa-fw fa fa-times'></i-->
            <strong>Movimiento Entre Carpetas Exitoso. </strong>
            Se movieron los radicados $rads de carpeta.
            <span id=refresh class='btn btn-ribbon' data-reset-msg='Recargar Pagina' data-html='true' rel='tooltip' data-title='refresh' data-action='resetWidgets'>
            <script>setTimeout(function(){location.reload()}, 5000); </script>
          </div>";
  } else {
    echo "<div class='alert alert-danger fade in'>
            <button class='close' data-dismiss='alert'> × </button>
            <!--i class='fa-fw fa fa-times'></i-->
            <strong>Error en el Movimiento Entre Carpetas.</strong>
            Radicados $rads $msg de carpeta.
            <script>setTimeout(function(){location.reload()}, 3000); </script>
          </div>";
  }
?>
