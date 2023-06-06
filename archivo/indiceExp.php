<?
session_start();
$ruta_raiz = ".."; 
if (!$_SESSION['dependencia'])
	header ("Location: $ruta_raiz/cerrar_session.php");

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");
include_once ("$ruta_raiz/adodb/toexport.inc.php");

$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_NUM);

$exps="'".implode("','",array_keys($checkValue))."'";

$sql=<<<EOF

select 
  s.sgd_exp_numero as "Numero Expediente", 
  e.radi_nume_radi as "Numero radicado", 
  r.ra_asun as "Asunto radicado",
  r.radi_fech_radi as "Fecha radicado",
  e.sgd_exp_fech as "Fecha Incorporacion Expediente",
--  r.tdoc_codi,
  tp.sgd_tpr_descrip as "Tipo doc radicado",
  a.anex_codigo as "Codigo de anexo",
  a.anex_desc as "Descripcion de anexo",
--  a.sgd_tpr_codigo,
  tpa.sgd_tpr_descrip as "Tipo doc anexos",
  r.radi_nume_folio as "Folios"

from  sgd_sexp_secexpedientes s 
full join sgd_exp_expediente e on (s.sgd_exp_numero=e.sgd_exp_numero)
full join anexos a on (e.radi_nume_radi=a.anex_radi_nume and anex_borrado='N')
left join radicado r on (e.radi_nume_radi=r.radi_nume_radi)
full join sgd_tpr_tpdcumento tp on (tp.sgd_tpr_codigo=r.tdoc_codi)
full join sgd_tpr_tpdcumento tpa on (tpa.sgd_tpr_codigo=a.sgd_tpr_codigo)

where s.sgd_exp_numero in ($exps);

EOF;
//$db->conn->debug=true;
$rs=$db->conn->Execute($sql);

if($xml=="true"){
	header("Content-Type: text/xml");
	header("Content-Disposition: attachment; filename=indice.xml");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	//echo "<pre>$sql</pre>";
	$rs=$db->conn->GetArray($sql);
	foreach($rs as $data){
		$_indice[]=array(
			"EXPEDIENTE"=>array(
				"ID"=>$data["NUMERO EXPEDIENTE"],
				"RADICADO"=>array(
					"ID"=>$data["NUMERO RADICADO"],
					"ASUNTO"=>$data["ASUNTO RADICADO"],
					"TIPOLOGIA_DOCUMENTAL"=>$data["TIPO DOC RADICADO"],
					"FOLIOS"=>$data["FOLIOS"],
					"FECHA_INCORPORACION_EXPEDIENTE"=>$data["FECHA INCORPORACION EXPEDIENTE"],
					"ANEXO"=>array(
						"NUMERO"=>"".$data["CODIGO DE ANEXO"],
						"DESCRIPCION"=>$data["DESCRIPCION DE ANEXO"],
						"TIPOLOGIA_ANEXO"=>$data["TIPO DOC ANEXOS"],
					),
				),
			),
		);
		/*$indice[$data["NUMERO EXPEDIENTE"]][$data["NUMERO RADICADO"]]["ASUNTO"]=$data["ASUNTO RADICADO"];
		$indice[$data["NUMERO EXPEDIENTE"]][$data["NUMERO RADICADO"]]["TIPOLOGIA DOCUMENTAL"]=$data["TIPO DOC RADICADO"];
		$indice[$data["NUMERO EXPEDIENTE"]][$data["NUMERO RADICADO"]]["FOLIOS"]=$data["FOLIOS"];
		$indice[$data["NUMERO EXPEDIENTE"]][$data["NUMERO RADICADO"]]["FECHA INCORPORACION EXPEDIENTE"]=$data["FECHA INCORPORACION EXPEDIENTE"];
		$indice[$data["NUMERO EXPEDIENTE"]][$data["NUMERO RADICADO"]][$data["CODIGO DE ANEXO"]]["DESCRIPCION ANEXO"]=$data["DESCRIPCION DE ANEXO"];
		$indice[$data["NUMERO EXPEDIENTE"]][$data["NUMERO RADICADO"]][$data["CODIGO DE ANEXO"]]["TIPOLOGIA ANEXO"]=$data["TIPO DOC ANEXOS"];*/
		//var_dump($data);
	}
	//var_dump($indice);
	/*$xml = new SimpleXMLElement('<exp/>');
	array_walk_recursive($_indice, array ($xml, 'addChild'));
	print $xml->asXML();	*/
	unset($xml);
	$xml="<?xml version='1.0' encoding='UTF-8'?>"."\n";

	$xml.="<EXPEDIENTES>"."\n";
	foreach ($_indice as $key => $value){
		//$xml.="<$key>"."\n";
		if (is_array($value)){
			foreach($value as $k => $v){
				if (is_array($v)){
					$xml.="\t<$k>"."\n";
					foreach ($v as $kk => $vv){
						if (is_array($vv)){
							$xml.="\t\t<$kk>"."\n";
							foreach ($vv as $_kk => $_vv){
								if (is_array($_vv)){
									$xml.="\t\t\t<$_kk>"."\n";
									foreach ($_vv as $__kk => $__vv){
										if (is_array($__vv)){
										}else{
											$xml.="\t\t\t\t<$__kk>$__vv</$__kk>\n";
										}
									}
									$xml.="\t\t\t</$_kk>"."\n";
								}else{
									$xml.="\t\t\t<$_kk>$_vv</$_kk>\n";
								}
							}
							$xml.="\t\t</$kk>"."\n";
						}else{
							$xml.="\t\t<$kk>$vv</$kk>\n";
						}
					}
					$xml.="\t</$k>"."\n";
				}else{
					$xml.="<$k>$v</$k>\n";
				}
			}
		}else{
			$xml.="$value";
		}
		//$xml.="</$key>";
	}

	$xml.="</EXPEDIENTES>"."\n";
	echo $xml;
}else{
	header("Content-Type: text/csv");
	header("Content-Disposition: attachment; filename=indice.csv");
	print rs2csv($rs);
}
//$rs=array_column($db->conn->GetArray($sql),0);
/*$rs=$db->conn->GetArray($sql);

foreach ($rs as $key){
	$coleccion[$key[0]][$key[1]]["Asunto"]=$key[2];
	$coleccion[$key[0]][$key[1]]["Tipo doc"]=$key[3];
	$coleccion[$key[0]][$key[1]][$key[4]]["descripcion"]=$key[5];
	$coleccion[$key[0]][$key[1]][$key[4]]["tipo doc"]=$key[6];
	//$coleccion[$key[0]][$key[1]][]=$key[3];
}
foreach($coleccion as $key => $value){
	echo $key."";
	$rad=$value[$key];
	foreach ($rad as $rkey => $rvalue){
		echo "\t".$rkey;
		
	}
}*/


//echo "<pre>";
//echo $sql;
//var_dump($rs->fields);
//var_dump($coleccion);
//echo "</pre>";

?>
