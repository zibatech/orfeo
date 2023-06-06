<?php
session_start();

    $ruta_raiz = "..";
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");
/**
 * Programa que lista los radicados que hacen parte de un grupo de masiva. Desde este listado es posible sacar los radicados del grupo
 * que no seran enviados, es llamado desde cuerpo_masiva.php
 * @author      Sixto Angel Pinzon
 * @version     1.0
 */
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$codusuario     = $_SESSION["codusuario"];

include_once "../class_control/Radicado.php"; 
require_once("$ruta_raiz/include/db/ConnectionHandler.php");
include_once "$ruta_raiz/class_control/GrupoMasiva.php"; 

if (!$db)
    $db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);	 

//variable que referencia un objeto tipo radicado
$rad =  new Radicado($db);
//variable que referencia un objeto tipo grupo massiva
$grupoMas = new GrupoMasiva($db);
//if (strlen($dep_sel)<1)
//	$dep_sel=$dependencia;
//variable que contiene un arrego de radicados de un grupo de masiva
$radsGrupo=$grupoMas->obtenerGrupo($dep_sel,$grupo,$busq_radicados);

?>
<html><head>
<link rel="stylesheet" href="../estilos/orfeo.css">
<style type="text/css">
  .btn-oculto{
    font-size: x-small;
    padding: 3px;
    background-color: #56565578;
    border-radius: 6px;
    margin-left: 5px;
  }
</style>
<script>

/** 
* Env�a el formulario hacia el programa que realiza la edici�n del grupo de radicados
*/
function enviar() {
document.formSacarGrupo.submit();
}

</script>

<script src='../dist/js/jquery-3.5.1.js'></script>
<script src='../dist/js/jquery.dataTables.min.js'></script>
<script src='../dist/js/dataTables.buttons.min.js'></script>
<script src='../dist/js/jszip.min.js'></script>
<script src='../dist/js/pdfmake.min.js'></script>
<script src='../dist/js/vfs_fonts.js'></script>
<script src='../dist/js/buttons.html5.min.js'></script>
<script src='../dist/js/buttons.print.min.js'></script>
<script src='../dist/js/buttons.colVis.min.js'></script>


<link rel="stylesheet" type="text/css" href="../dist/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="../dist/css/buttons.dataTables.min.css">
</head>
<body  topmargin="0" bgcolor="#ffffff">
        <?
	   		$nomcarpeta="EDICION DE RADICADOS DEL GRUPO <b> $grupo </b> <BR> DE RADICACION MASIVA ";
		?>     
  <table BORDER=0  cellpad=2 cellspacing='0' WIDTH=98% class='t_bordeGris' valign='top' align='center' >
		<TR>
		 <td width='35%' >
      <table width='100%' border='0' cellspacing='1' cellpadding='0'>
        <tr> 
          <td height="20" bgcolor="377584"><div align="left" class="titulo1">LISTADO DE: </div></td>
        </tr>
		<tr class="info">
          <td height="30"><?=$nomcarpeta ?></td>
        </tr>
      </table>
    </td>	
		
     <td width='35%' >
      <table width='100%' border='0' cellspacing='1' cellpadding='0'>
        <tr> 
          <td height="20" bgcolor="377584"><div align="left" class="titulo1">USUARIO </div></td>
        </tr>
		<tr class="info">
          <td height="30"><?=$nombusuario ?></td>
        </tr>
      </table>
    </td>	
	 <td width='35%' >
      <table width='100%' border='0' cellspacing='1' cellpadding='0'>
        <tr> 
          <td height="20" bgcolor="377584"><div align="left" class="titulo1">DEPENDENCIA </div></td>
        </tr>
		<tr class="info">
          <td height="30"><?=$depe_nomb ?></td>
        </tr>
      </table>
    </td>
	</table>        
	<table width="98%" align="center" cellspacing="0" cellpadding="0" >
    	<tr class="tablas">
      	<TD  > 
					
           	<FORM name=form_busq_rad action='lista_sacar_grupo.php?<?=session_name()."=".trim(session_id())?>' method=post>
	                        <input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'> 
                Buscar radicado(s) (Separados por coma)<input name="busq_radicados" type="text" size="70" class="tex_area" value="<?=$busq_radicados?>">
							<input type=submit name=buscar valign='middle' class='botones'  value="Buscar" />
							<input name="grupo" type="hidden" value="<?=$grupo?>" />
							<input name="dep_sel" type="hidden" value="<?=$dep_sel?>" />
							<input name="krd" type="hidden" value="<?=$krd?>" />
                            <?php
                                //almacena los elementos de sesi�n
								$encabezado="&".session_name()."=".session_id()."&krd=$krd&carpeta=$carpeta&tipo_carp=$tipo_carp&fechah=$fechah&ascdesc=$ascdesc";
								$encabezado.="&agendado=$agendado&mostrar_opc_envio=$mostrar_opc_envio&chk_carpeta=$chk_carpeta&busq_radicados=$busq_radicados&nomcarpeta=$nomcarpeta&orno=";
							?>
      			</form>
				</td>
		  </tr>
	</table>
  <br>
  <br>
    <br><br>
    <table id='datatable_masiva'>
              	<thead>
                  <tr>
                 <th class="titulos3"> 
                      NUMERO RADICADO</a> 
                  </th>
                  <th class="titulos3" width="10%">  
                    FECHA RADICADO</th>
                    <th class="titulos3" width="15%">  
                    ASUNTO</th>
                  <th class="titulos3" width="8%">  
                    NOMBRE DESTINATARIO</th>
                     <th class="titulos3" width="8%">  
                    DOCUMENTO</th>
                  <th class="titulos3" width="15%">  
                   EMAIL</th>
                  <th class="titulos3" width="8%">  
                    DIRECCION</th>
                  <th class="titulos3" width="8%">  
                    DEPARTAMENTO</th>
                  <th class="titulos3" width="8%">  
                    MUNICIPIO </th>
                     <th class="titulos3" width="8%">  
                    FECHA ENVIO </th>
                    <th class="titulos3" width="8%">  
                    ACUSES </th>
                    <th class="titulos3" width="8%">  
                    ESTADO </th>
                  
                </thead>
                <tbody>
                <?php
								  //var que recoge el n�mero de radicados del grupo
									$num = count($radsGrupo);
									$i = 0;
									// var que recoge todos os radicados que fueron retirados
									$retirados="";
									//var de indicador del rango de registros que ha de mostrarse
								  $registro=$pagina*5000;
									
									//Recorre el arreglo de registros que hacen parte del grupo y va imprimiendolos
									while ($i < $num) {
										
									  //Imprime solo los 20 de la p�gina seleccionada
										if($i>=$registro and $i<($registro+5000)){
									
											//Decice el fromato gr�fico de cada registro
											if (($i%2)==0)
												 $clase ="listado2";
											else
												$clase="listado1";
												//obtiene los datos del radicado
												$datosRad=$rad->radicado_codigo($radsGrupo[$i]);
												$datosRad=$rad->getDatosRemitente();
												$chequeado="";
												
												//Si el radicado fue retirado del grupo entonces lo marca como tal
												if ($grupoMas->radicadoRetirado($grupo,$radsGrupo[$i])){
													$retirados=$retirados.";".$radsGrupo[$i].";";
												  $chequeado="checked";
												}
											
								?>
                <tr class="<?=$clase?>"> 
                  <td class="leidos"> <span class="tpar"> 
                    '<?=$radsGrupo[$i]?>
                    </span> </td>
                  <td class="leidos"><font size="1"><span class="tpar"> 
                    <?=$rad->getRadi_fech_radi()?>
                    </span></td>
                  <td class="leidos"> 
                    <?=$rad->getAsuntoRad()?>
                  </td>
                  <td class="leidos"> 
                    <?=$datosRad["nombre"]?>
                  </td>
                   <td class="leidos"> 
                    <?=$datosRad["documento_ciu"]?>
                  </td>
                  <td class="leidos"> 
                    <?=$datosRad["email"]?>
                  </td>
                  <td class="leidos"> 
                    <?=$datosRad["direccion"]?>
                  </td>
                  <td class="leidos"> 
                    <?=$datosRad["deptoNombre"]?>
                  </td>
                  <td class="leidos"> 
                    <?=$datosRad["muniNombre"]?>
                  </td>
                   <td class="leidos"> 
                    <?=$rad->getFechaEnvio()?>
                  </td>
                   <td class="leidos"> 
                    <?=$rad->getAcuses($db)?>
                  </td>
                  <td class="leidos"> 
                    <?= intval($rad->getEstado()) == 4?'Enviado':''?> 
                    <?= intval($rad->getEstado()) == 3?'por enviar':''?>
                    <?= intval($rad->getEstado()) == 2?'Devuelto':''?>
                  </td>
                 
                </tr>
                <?
										}
										$i++; 
									}
								?>
          </tbody> 
        </table>
    <input name="retirados" type="hidden" id="retirados" value="<?php echo $retirados ?>">
	

<script type="text/javascript">
      
  $(function() {
    var table = $('#datatable_masiva').DataTable({
        "language": {"url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"}, dom: 'Bfrtip',
        lengthMenu: [
            [ 50, 100, 500, -1 ],
            [ '50 registros', '100 registros', '500 rows', 'todos all' ]
        ],
        buttons: ['copyHtml5','excelHtml5','csvHtml5',{extend: 'pdfHtml5',orientation: 'landscape',pageSize: 'A2'},'pageLength',  'colvis']
      });
    });

 </script>
<br>
<br> 
</body>
</html>
