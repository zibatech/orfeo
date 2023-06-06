<?php
define('ADODB_FETCH_ASSOC',2);
if(!$no_planilla_Inicial or intval($no_planilla_Inicial) == 0) die ("<table width='100%'><tr><td ><center>Debe colocar un Numero de Planilla v&aacute;lido</center></td></tr></table>");
if($generar_listado){
  $ruta_raiz = "..";
  include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
  include_once ("$ruta_raiz/adodb/toexport.inc.php");
  include_once ("$ruta_raiz/adodb/adodb.inc.php");
  include_once ("$ruta_raiz/class_control/planillas-class.php");

  //$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
  //$db->conn->debug = true;
  //$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC; 

  /*NOMBRE	DIRECCION DESTINATARIO	
   * CIUDAD DESTINATARIO	DEPARTAMENTO	
   * PESO	ADICIONAL 1*/
 
 $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
 
 $isqlSipos = "SELECT
                SGD_RENV_NOMBRE,
                SGD_RENV_DIR, 
                SGD_RENV_PAIS,
                SGD_RENV_MPIO,
                SGD_RENV_DEPTO, 
                SGD_RENV_PESO,
                RADI_NUME_SAL,
                sgd_dir_tipo
            FROM 
                SGD_RENV_REGENVIO 
            WHERE 
                SGD_RENV_PLANILLA = '" .$no_planilla_Inicial."' 
                AND SGD_RENV_DESTINO != 'Local' and SGD_RENV_DESTINO != 'Nacional'
                AND SGD_FENV_CODIGO = $tipo_envio
                AND DEPE_CODI=  $dependencia 
                ";
 
  if(!empty($fecha_mes )){
        $isqlSipos .= " AND ".$sqlChar." = ".$fecha_mes;
  }

  $isqlSipos         = $isqlSipos . " order by SGD_RENV_DEPTO, SGD_RENV_MPIO";
  $rsSipos         = $db->conn->Execute($isqlSipos);
  //$encabezado = 'NOMBRE;DIRECCION DESTINATARIO;CIUDAD DESTINATARIO;DEPARTAMENTO;PESO;ADICIONAL 1;ADICIONAL 2';
  //NOMBRE DESTINATARIO	DIRECCION	CIUDAD	DEPARTAMENTO	PESO	Observaciones	Referencia No
  $encabezado = 'NOMBRE DESTINATARIO;DIRECCION;CIUDAD;Referencia;PESO;';
  while(!$rsSipos->EOF){
      $nom  = $rsSipos->fields['SGD_RENV_NOMBRE'];
      $pais = $rsSipos->fields['SGD_RENV_PAIS'];

      $data[] = array('NOMBRE DESTINATARIO' => $rsSipos->fields["SGD_RENV_NOMBRE"], 'DIRECCION' => $rsSipos->fields['SGD_RENV_DIR'],
                'CIUDAD'=> strtoupper($rsSipos->fields['SGD_RENV_MPIO']) ."-". strtoupper($rsSipos->fields['SGD_RENV_DEPTO']),
                'Referencia' => $rsSipos->fields["RADI_NUME_SAL"] ,'PESO' => $rsSipos->fields['SGD_RENV_PESO']);
			
			/** $data[] = array('NOMBRE DESTINATARIO' => $rsSipos->fields['SGD_RENV_NOMBRE'], 'DIRECCION' => $rsSipos->fields['SGD_RENV_DIR'],
                'CIUDAD'=> $rsSipos->fields['SGD_RENV_MPIO'] ."-". $rsSipos->fields['SGD_RENV_DEPTO'],
                'Referencia' => $rsSipos->fields['RADI_NUME_SAL'] ,'PESO' => $rsSipos->fields['SGD_RENV_PESO'],'Referencia No' => $rsSipos->fields['sgd_dir_tipo']);
      */
     $rsSipos->MoveNext();
  } 

  $numPlanilla=$no_planilla_Inicial . "_".$krd ;
  $planillaGEn=new panillasClass();
  $planillaGEn->setRuta_raiz('..');
  $planillaGEn->setData($data);
  $planillaGEn->setNumPlanilla($numPlanilla);
  $planillaGEn->setRutaArchivo('');
  $planillaGEn->setRutaArchivo("$ruta_raiz/bodega/pdfs");
  $planillaGEn->setEncabezado($encabezado);
$g=$planillaGEn->generarSipos();
}
?>
xxxx
<table width='200' align="center" ><tr><td ><center><b>Archivos Planos </b>
  <td><a href='<?php echo $g['csv'];?>'  >CSV</a></td> 
  <td><a href='<?=$g['txt']?>' target='<?=date("dmYh").time("his")?>'>TXT</a></td>
</center></td></tr></table>
