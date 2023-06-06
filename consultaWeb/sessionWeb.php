<?

//inicio de coneccion a BD
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_NUM);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

define('ADODB_ASSOC_CASE', 1);
$krd = strtoupper($krd);
$fechah = date("Ymd") . "_" . time("hms");
$check = 1;
$numeroa = 0;
$numero = 0;
$numeros = 0;
$numerot = 0;
$numerop = 0;
$numeroh = 0;
$ValidacionKrd = "";
$query = "select 
						a.SGD_TRAD_CODIGO
						,a.SGD_TRAD_DESCR
						,a.SGD_TRAD_ICONO
						from SGD_TRAD_TIPORAD a
						order by a.SGD_TRAD_CODIGO
						";

$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$rs = $db->query($query);
//$numRegs = "! ".$rs->RecordCount();
$varQuery = $query;
$comentarioDev = ' Busca todos los tipos de Radicado Existentes ';
include "$ruta_raiz/include/tx/ComentarioTx.php";
$iTpRad = 0;
$queryTip3 = "";
$tpNumRad = array();
$tpDescRad = array();
$tpImgRad = array();
while (!$rs->EOF) {
	$numTp = $rs->fields["SGD_TRAD_CODIGO"];
	$descTp = $rs->fields["SGD_TRAD_DESCR"];
	$imgTp = $rs->fields["SGD_TRAD_ICONO"];
	$queryTRad .= ",a.USUA_PRAD_TP$numTp";
	$queryDepeRad .= ",b.DEPE_RAD_TP$numTp";
	$queryTip3 .= ",a.SGD_TPR_TP$numTp";
	$tpNumRad[$iTpRad] = $numTp;
	$tpDescRad[$iTpRad] = $descTp;
	$tpImgRad[$iTpRad] = $imgTp;
	$iTpRad++;
	$rs->MoveNext();
}
/** 	
 * 	BUSQUEDA DE ICONOS Y NOMBRES PARA LOS TERCEROS (Remitentes/Destinarios) AL RADICAR
 * 	@param	$tip3[][][]  Array  Contiene los tipos de radicacion existentes.  En la primera dimencion indica la posicion dependiendo del tipo de rad. (ej. salida -> 1, ...). En la segunda dimencion almacenara los datos de nombre del tipo de rad. inidicado, Para la tercera dimencion indicara la descripcion del tercero y en la cuarta dim. contiene el nombre del archio imagen del tipo de tercero.
 */
$query = "select 
					a.SGD_DIR_TIPO
					,a.SGD_TIP3_CODIGO
					,a.SGD_TIP3_NOMBRE
					,a.SGD_TIP3_DESC
					,a.SGD_TIP3_IMGPESTANA
					$queryTip3
					from SGD_TIP3_TIPOTERCERO a";
$rs = $db->query($query);
while (!$rs->EOF) {
	$dirTipo = $rs->fields["SGD_DIR_TIPO"];
	$nombTip3 = $rs->fields["SGD_TIP3_NOMBRE"];
	$descTip3 = $rs->fields["SGD_TIP3_DESC"];
	$imgTip3 = $rs->fields["SGD_TIP3_IMGPESTANA"];
	for ($iTp = 0; $iTp <= $iTpRad; $iTp++) {
		$numTp = $tpNumRad[$iTp];
		$campoTip3 = "SGD_TPR_TP$numTp";
		$numTpExiste = $rs->fields[$campoTip3];
		if ($numTpExiste >= 1) {
			$tip3Nombre[$dirTipo][$numTp] = $nombTip3;
			$tip3desc[$dirTipo][$numTp] = $descTip3;
			$tip3img[$dirTipo][$numTp] = $imgTip3;
			//echo "<hr> $ tip3img[$dirTipo][$numTp] =". $tip3img[$dirTipo][$numTp] ."<hr>";
		}
	}
	$rs->MoveNext();
}

if ($recOrfeo != "Seguridad") {
	$queryRec = "AND USUA_PASW ='" . SUBSTR(md5($drd), 1, 26)."'";
} else {
	$queryRec = "AND USUA_SESION='" . str_replace(".", "o", $REMOTE_ADDR) . "o$krd' ";
}
$query = "select a.*
				,b.DEPE_NOMB
				,a.USUA_ESTA
				,a.USUA_CODI
				,a.USUA_LOGIN
				,b.DEPE_CODI_TERRITORIAL
				,b.DEPE_CODI_PADRE
				,a.USUA_PERM_ENVIOS
				,a.USUA_PERM_MODIFICA
				$queryTRad
				$queryDepeRad
					from usuario a
						,DEPENDENCIA b
					where
						USUA_LOGIN ='$krd' and  a.depe_codi=b.depe_codi
						$queryRec";

/** Procedimiento forech que encuentra los numeros de secuencia para las radiciones
 * 	 @param tpDepeRad[]	array 	Muestra las dependencias que contienen las secuencias para radicion.
 */
$varQuery = $query;
$comentarioDev = ' Busca Permisos de Usuarios ...';
include "$ruta_raiz/include/tx/ComentarioTx.php";
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$rs = $db->query($query);
$perm_radi_salida_tp = 0;

foreach ($tpNumRad as $key => $valueTp) {
	$campo = "DEPE_RAD_TP$valueTp";
	$campoPer = "USUA_PRAD_TP$valueTp";
	$tpDepeRad[$valueTp] = $rs->fields[$campo];
	$tpPerRad[$valueTp] = $rs->fields[$campoPer];
	if ($tpPerRad[$valueTp] >= 1) {
		$perm_radi_salida_tp = 1;
	}
	$tpDependencias .= "<" . $rs->fields[$campo] . ">";
}
?>
