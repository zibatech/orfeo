<?php
/**
* @author Cesar Gonzalez <aurigadl@gmail.com>
* @author Correlibre.org
* @license  GNU AFFERO GENERAL PUBLIC LICENSE
* @copyright

OrfeoGpl Models are the data definition of OrfeoGpl Information System
Copyright (C) 2013 Infometrika Ltda.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

session_start();
$ruta_raiz = "..";

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

$krdold = $krd;

if(!$krd) $krd = $krdold;

if (!$nurad) $nurad= $rad;
if($nurad){
    $ent = substr($nurad,-1);
}

include_once("$ruta_raiz/include/db/ConnectionHandler.php");
include_once "$ruta_raiz/include/tx/Historico.php";
include_once ("$ruta_raiz/class_control/TipoDocumental.php");
include_once "$ruta_raiz/include/tx/Expediente.php";

$db = new ConnectionHandler("$ruta_raiz");
$trd = new TipoDocumental($db);
$encabezadol = "$PHP_SELF?".session_name()."=".session_id()."&
    opcionExp=$opcionExp&numeroExpediente=$numeroExpediente&
    dependencia=$dependencia&krd=$krd&nurad=$nurad&coddepe=$coddepe&
    codusua=$codusua&depende=$depende&ent=$ent&tdoc=$tdoc&
    codiTRDModi=$codiTRDModi&codiTRDEli=$codiTRDEli&
    codserie=$codserie&tsub=$tsub&ind_ProcAnex=$ind_ProcAnex";

$dependencia=$_SESSION['dependencia'];
$codusuario=$_SESSION['codusuario'];

if ($Actualizar && $tsub !=0 && $codserie !=0 ){
    if(!$digCheck){
        $digCheck = "E";
    }
    $codiSRD    = $codserie;
    $codiSBRD   = $tsub;
    $trdExp     = substr("00".$codiSRD,-2) . substr("00".$codiSBRD,-2);
    $expediente = new Expediente($db);

    if(!$expManual) {
        $secExp = $expediente->secExpediente($dependencia,$codiSRD,$codiSBRD,$anoExp);
    }else {
        $secExp = $consecutivoExp;
    }

    $consecutivoExp   = substr("00000".$secExp,-5);
    $numeroExpediente = $anoExp . $dependencia . $trdExp . $consecutivoExp . $digCheck;


    /**  Procedimiento que Crea el Numero de  Expediente
     *  @param $numeroExpediente String  Numero Tentativo del expediente
     *  que recordar que en la creacion busca la ultima secuencia creada.
     *  @param $nurad  Numeric Numero de radicado que se insertara en un expediente.
     */

    $numeroExpedienteE = $expediente->crearExpediente( $numeroExpediente,$nurad,$dependencia,$codusuario,$usua_doc,$usuaDocExp,$codiSRD,$codiSBRD,'false',$fechaExp );
    if($numeroExpedienteE==0){
        $mensaje_err .= "El expediente que intento crear ya existe.";
    }else{
        $insercionExp = $expediente->insertar_expediente( $numeroExpediente,$nurad,$dependencia,$codusuario,$usua_doc);
    }
    $codiTRDS = $codiTRD;
    $i++;
    $TRD = $codiTRD;
    $observa = "*TRD*".$codserie."/".$codiSBRD." (Creacion de Expediente.)";
    include_once "$ruta_raiz/include/tx/Historico.php";
    $radicados[] = $nurad;
    $tipoTx = 51;
    $Historico = new Historico($db);
    $Historico->insertarHistoricoExp($numeroExpediente,$radicados, $dependencia,$codusuario, $observa, $tipoTx,0);
}


if($grbNivel and $numRad) {
    $query = "UPDATE RADICADO SET SGD_SPUB_CODIGO=$nivelRad where radi_nume_radi=$numRad";
    if($nivelRad==0){
        $observa = "Radicado Publico";
    }
    elseif ($nivelRad == 1) {
        $observa = "Reservado: Solo la dependencia.";
    }
    elseif ($nivelRad == 2) {
        $observa = "Clasificado: Usuario que proyectó, Jéfe y usuario actual del radicado.";
    }

    if($db->conn->Execute($query)) {
        $message = "El nivel de seguridad se actualiz&oacute; correctamente.";
        include_once "$ruta_raiz/include/tx/Historico.php";
        $codiRegH = "";
        $Historico = new Historico($db);
        $codiRegE[0] = $numRad;
        $radiModi = $Historico->insertarHistorico($codiRegE, $dependencia, $codusuario, $dependencia, $codusuario, $observa, 54);
    }else {
        $mensaje_err .=  "!No se pudo actualizar el nivel de seguridad!";
    }
}

$error = $mess = '';

if($mensaje_err){
    $error = "
    <div class='alert alert-block alert-danger'>
        <a class='close' data-dismiss='alert' href='#'>×</a>
        <h4 class='alert-heading'>{$mensaje_err}</h4>
    </div>";
}

if($message){
    $mess = "
        <div class='alert alert-block alert-success'>
            <a class='close' data-dismiss='alert' href='#'>×</a>
            <h4 class='alert-heading'>$message</h4>
        </div>";
}

?>
<html>

<head>
    <title>Tipificar</title>
    <?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
</head>

<body>
    <div class="container-fluid">
        <div class="col-sm-12">
            <form method="post"
               action="radicado.php?krd=<?=$krd?>&numRad=<?=$numRad?>"
               name="TipoDocu">
               <section id="widget-grid">
                    <article>
                      <!-- Widget ID (each widget will need unique ID)-->
                      <div  class="jarviswidget jarviswidget-color-darken"
                            id="wid-id-1" data-widget-editbutton="false">
                          <header>
                            <h2>
                                Nivel de Seguridad del radicado No.<?=$numRad?>
                            </h2>
                          </header>
                          <div class="widget-body">
                                <div style="padding-top:30px" class="panel-body" >
                                    <div class="col-md-12">
                                       <?=$error?>
                                       <?=$mess?>
                                       <div class="form-group">
                                         <label>Nivel:</label>
                                            <select name=nivelRad class="form-control">
                                            <?
                                            if($nivelRad==0)  $datoss = " selected "; else $datoss = "";
                                            ?>
                                            <option value=0 <?=$datoss?>>P&uacute;blico</option>
                                            <?
                                            if($nivelRad==1)  $datoss = " selected "; else $datoss = "";
                                            ?>
                                            <option value=1 <?=$datoss?>>Pública Reservada: Solo la dependencia</option>
                                            <?
                                            if($nivelRad==2)  $datoss = " selected "; else $datoss = "";
                                            ?>
                                            <option value=2 <?=$datoss?>>Pública Clasificada: Usuario que proyectó, Jéfe y usuario actual del radicado</option>
                                            </select>
                                       </div>
                                      <p id="emailHelp" class="form-text text-muted">
                                        Pública Clasificada, el acceso al documento y a su
                                        informaci&oacute;n se restringe seg&uacute;n
                                        reglas del negocio.
                                      </p>

                                      <div style="margin-top:10px" class="form-group">
                                          <!-- Button -->
                                          <div class="col-sm-12 controls">
                                            <input type="submit" class="btn btn-success"
                                            name=grbNivel value="Grabar Nivel">

                                            <input name="Cerrar" type="button"
                                            class="btn" id="envia22"
                                            onClick="opener.regresar();window.close();"
                                            value="Cerrar">

                                          </div>
                                      </div>
                                      <p><?=$descTipoExpediente?>  <?=$expDesc?></p>
                                    </div>
                                </div>
                          </div>
                      </div>
                    </article>
                </section>
            </form>
        </div>
    </div>

    <script language="JavaScript" type="text/JavaScript">
        function regresar(){
            document.TipoDocu.submit();
        }
    </script>

</body>
</html>
