<?
	foreach ($_GET as $key => $valor)   ${$key} = $valor;
	foreach ($_POST as $key => $valor)   ${$key} = $valor;
	foreach ($_SESSION as $key => $valor)   ${$key} = $valor;
/**
* CONSULTA VERIFICACION PREVIA A LA RADICACION
*/

if($depen!="")$where_isql2 = " AND R.RADI_DEPE_ACTU = '".$depen."'";
//. and radi_fech_radi >= $time1 and radi_fech_radi <= $time2";
$where_isql1 = " WHERE DE.DEPE_CODI=R.RADI_DEPE_ACTU AND D.RADI_NUME_RADI=R.RADI_NUME_RADI AND R.sgd_trad_codigo =2 ";

$where_isql1 .=" and R.DEPE_CODI = ".$dependencia;
if($_GET['radini'] != "" ) $where_isql1 .= " and R.radi_nume_radi >= '".$_GET['radini']."' ";
if($_GET['radfini'] !="" ) $where_isql1 .=" and R.radi_nume_radi <= '".$_GET['radfini']."' ";
if($codusu!="")$where_isql1.=" and R.RADI_USUA_ACTU = $codusu ";
if(isset($_SESSION['pla']))$where_isql1.=" and radi_usua_radi in
(
select usua_codi from usuario where usua_prad_tp2 >0 and depe_codi=radi_depe_radi
)";

$where_isql1.=" and TO_CHAR(R.RADI_FECH_RADI, 'YYYY/mm/dd') BETWEEN '".$_GET['fecha_ini']."' and '".$_GET['fecha_fin']."'";

if( $_GET['incReasignados'] != 1 )
{
		$where_isql1 .= " AND ( R.radi_usu_ante = '' OR R.radi_usu_ante IS NULL ) ";
}

switch($db->driver)
{
	case 'mssql':
		{	
	$query = "select distinct (convert(char(15),R.RADI_NUME_RADI))	as RADICADO,";
	if($dependencia==521 or $dependencia==522 or $dependencia==523 or $dependencia==524 or $dependencia==525)$query .="R.RADI_ARCH3 AS DP,";
	else $query .="R.RADI_DEPER AS DP,";
	$query .="D.SGD_DIR_NOMREMDES as REMITENTE,
		R.RA_ASUN as ASUNTO,
		DE.DEPE_NOMB as ENTREGADO_A,
		R.RADI_DESC_FOLIOS AS FOLIOS,
		R.RADI_DESC_ANEX as ANEXOS,
		R.RADI_FECH_RADI as FECHA_RADICADO,
		DA.DEPE_NOMB AS DEPE_ANTE
		from SGD_DIR_DRECCIONES D, DEPENDENCIA DE, RADICADO R
		LEFT JOIN USUARIO U ON R.RADI_USU_ANTE = U.USUA_LOGIN
		LEFT JOIN DEPENDENCIA DA ON U.DEPE_CODI = DA.DEPE_CODI AND DA.DEPE_CODI <> R.RADI_DEPE_ACTU ";
		}break;
	case 'oracle':
	case 'oci8':
	case 'oci805':	
	case 'postgres':
		$query ="select distinct( R.RADI_NUME_RADI ) as RADICADO,
		T.SGD_TPR_DESCRIP AS TIPO,
			     TO_CHAR(R.RADI_FECH_RADI,'YYYY-MM-DD HH24:MI') as FECHA_RADICADO,
			     D.SGD_DIR_NOMREMDES as ENTIDAD_REMITENTE,
			     R.RA_ASUN as ASUNTO,
			     R.radi_nume_folio AS FOLIOS,
			     DE.DEPE_NOMB as DIRIGIDO_A
			     from SGD_DIR_DRECCIONES D, DEPENDENCIA DE, RADICADO R
                             LEFT JOIN sgd_tpr_tpdcumento T ON T.SGD_TPR_CODIGO=R.TDOC_CODI
			     ";
                $queryc ="select count(R.radi_nume_radi)
			     from SGD_DIR_DRECCIONES D, DEPENDENCIA DE, RADICADO R
                             LEFT JOIN sgd_tpr_tpdcumento T ON T.SGD_TPR_CODIGO=R.TDOC_CODI
			    ";
		break;
	}
?>
