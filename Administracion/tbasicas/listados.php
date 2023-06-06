<?php
session_start();
    $ruta_raiz = "../..";
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");
/*  Visualizador de Listados.
*	Creado por: Ing. Hollman Ladino Paredes.
*	Para el proyecto ORFEO.
*
*	Permite la visualizacion general de paises, departemntos, municipios, tarifas, etc.
*	Es una idea basica. Aun esta bajo desarrollo.
*/

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$dependencia_nombre = $_SESSION["depe_nomb"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
$nivelus=$_SESSION["nivelus"];
$tip3Nombre=$_SESSION["tip3Nombre"];
$tip3desc = $_SESSION["tip3desc"];
$tip3img =$_SESSION["tip3img"];
include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
switch ($_GET['var'])
{	case 'tar'	:
		{	$titulo = "LISTADO GENERAL DE TARIFAS";
			$tit_columnas = array('Forma Envio','Nal / InterNal.','C&oacute;d. Tarifa','Desc. Tarifa','Valor Local/America','Valor Nal./Resto');
			$valor1 = $db->conn->IfNull('SGD_TAR_TARIFAS.SGD_TAR_VALENV1', 'SGD_TAR_TARIFAS.SGD_TAR_VALENV1G1');
			$valor2 = $db->conn->IfNull('SGD_TAR_TARIFAS.SGD_TAR_VALENV2', 'SGD_TAR_TARIFAS.SGD_TAR_VALENV2G2');
			$isql =	"SELECT SGD_FENV_FRMENVIO.SGD_FENV_DESCRIP, SGD_CLTA_CLSTARIF.SGD_CLTA_CODSER, SGD_CLTA_CLSTARIF.SGD_TAR_CODIGO, SGD_CLTA_CLSTARIF.SGD_CLTA_DESCRIP, 
                      $valor1 AS VALOR1, $valor2 AS VALOR2 
					FROM SGD_CLTA_CLSTARIF, SGD_TAR_TARIFAS, SGD_FENV_FRMENVIO 
					WHERE SGD_CLTA_CLSTARIF.SGD_FENV_CODIGO = SGD_TAR_TARIFAS.SGD_FENV_CODIGO AND 
                      SGD_CLTA_CLSTARIF.SGD_TAR_CODIGO = SGD_TAR_TARIFAS.SGD_TAR_CODIGO AND 
                      SGD_CLTA_CLSTARIF.SGD_CLTA_CODSER = SGD_TAR_TARIFAS.SGD_CLTA_CODSER AND
					  SGD_CLTA_CLSTARIF.SGD_FENV_CODIGO = SGD_FENV_FRMENVIO.SGD_FENV_CODIGO
					ORDER BY SGD_CLTA_CLSTARIF.SGD_CLTA_CODSER, SGD_CLTA_CLSTARIF.SGD_FENV_CODIGO, 
					SGD_CLTA_CLSTARIF.SGD_TAR_CODIGO";			
		}break;
	case 'pai'	:
		{	$titulo = "LISTADO GENERAL DE PAISES";
			$tit_columnas = array('Continente','Id Pa&iacute;s','Nombre Pa&iacute;s');
			$isql =	"SELECT SGD_DEF_CONTINENTES.NOMBRE_CONT, SGD_DEF_PAISES.ID_PAIS, SGD_DEF_PAISES.NOMBRE_PAIS 
					FROM SGD_DEF_PAISES, SGD_DEF_CONTINENTES WHERE SGD_DEF_PAISES.ID_CONT = SGD_DEF_CONTINENTES.ID_CONT
					ORDER BY SGD_DEF_CONTINENTES.NOMBRE_CONT, SGD_DEF_PAISES.NOMBRE_PAIS";
			
		}break;
	case 'tpr'	:
		{	$titulo = "LISTADO GENERAL DE TIPOS DE RADICADOS";
			$tit_columnas = array('Id T.R.','Nombre','Genera Rad. Salida?');
			$isql =	'SELECT SGD_TRAD_CODIGO as "Id T.R.", SGD_TRAD_DESCR as "Nombre", 
					SGD_TRAD_GENRADSAL as "Genera Rad. Salida?" 
					FROM SGD_TRAD_TIPORAD ORDER BY SGD_TRAD_CODIGO';
		}break;
	case 'fnv'	:
		{	$titulo = "LISTADO GENERAL DE FORMAS DE ENVIO";
			$tit_columnas = array('Id','Nombre','Estado','Genera Planilla?');
			$isql =	"SELECT SGD_FENV_CODIGO, SGD_FENV_DESCRIP,
					 (CASE WHEN SGD_FENV_ESTADO = 0 THEN 'INACTIVO' WHEN SGD_FENV_ESTADO = 1 THEN 'ACTIVO' END),
					 (CASE WHEN SGD_FENV_PLANILLA = 0 THEN 'NO' WHEN SGD_FENV_PLANILLA = 1 THEN 'SI' END) 
					FROM SGD_FENV_FRMENVIO ORDER BY SGD_FENV_DESCRIP";
		}break;
	case 'lcd'	:
		{	$titulo = "LISTADO GENERAL DE RESOLUCIONES";
			$tit_columnas = array('Id','Resoluci&oacute;n');
			$isql =	"SELECT SGD_TRES_CODIGO, SGD_TRES_DESCRIP FROM SGD_TRES_TPRESOLUCION ORDER BY SGD_TRES_CODIGO";
			
		}break;
	case 'dpt'	:
		{	$titulo = "LISTADO GENERAL DE DEPARTAMENTOS";
			$tit_columnas = array('Continente','Nombre País','Id Dpto','Nombre Dpto');
			if($pas >0 or $cont>0){
				$isql =	"SELECT SGD_DEF_CONTINENTES.NOMBRE_CONT, SGD_DEF_PAISES.NOMBRE_PAIS, DEPARTAMENTO.DPTO_CODI, DEPARTAMENTO.DPTO_NOMB
						FROM SGD_DEF_PAISES, SGD_DEF_CONTINENTES, DEPARTAMENTO 
						WHERE SGD_DEF_PAISES.ID_CONT = SGD_DEF_CONTINENTES.ID_CONT AND 
							SGD_DEF_PAISES.ID_PAIS = DEPARTAMENTO.id_pais AND 
							SGD_DEF_PAISES.ID_CONT = DEPARTAMENTO.id_cont AND 
							SGD_DEF_PAISES.ID_CONT = ".$cont;
				if($pas >0){
					$isql .=" AND SGD_DEF_PAISES.ID_PAIS = ".$pas;
				}
				$isql .=" ORDER BY SGD_DEF_CONTINENTES.NOMBRE_CONT, SGD_DEF_PAISES.NOMBRE_PAIS, DEPARTAMENTO.DPTO_NOMB";
	
			}else{
			$isql =	"SELECT SGD_DEF_CONTINENTES.NOMBRE_CONT, SGD_DEF_PAISES.NOMBRE_PAIS, DEPARTAMENTO.DPTO_CODI, DEPARTAMENTO.DPTO_NOMB
					FROM SGD_DEF_PAISES, SGD_DEF_CONTINENTES, DEPARTAMENTO 
					WHERE SGD_DEF_PAISES.ID_CONT = SGD_DEF_CONTINENTES.ID_CONT AND 
						SGD_DEF_PAISES.ID_PAIS = DEPARTAMENTO.id_pais AND 
						SGD_DEF_PAISES.ID_CONT = DEPARTAMENTO.id_cont
					ORDER BY SGD_DEF_CONTINENTES.NOMBRE_CONT, SGD_DEF_PAISES.NOMBRE_PAIS, DEPARTAMENTO.DPTO_NOMB";
			}			
		}break;
	case 'dpc'	:
		{	$titulo = "LISTADO GENERAL DE DEPENDENCIAS";
			$tit_columnas = array('Id','Nombre','Sigla','Estado','Nombre Dpto');
			$isql =	"SELECT DEPE_CODI, DEPE_NOMB, DEP_SIGLA, DEPE_ESTADO	
					FROM DEPENDENCIA 
					ORDER BY DEPE_CODI";	
		}break;
	case 'cau'	:
		{	$titulo = "LISTADO GENERAL DE CAUSALES";
			$tit_columnas = array('Id','Nombre');
			$isql =	"SELECT SGD_CAU_CODIGO, SGD_CAU_DESCRIP FROM SGD_CAU_CAUSAL ORDER BY 1";
		}break;
	case 'mdv'	:
		{	$titulo = "LISTADO GENERAL DE MOTIVOS DE DEVOLUCI&Oacute;N";
			$tit_columnas = array('Id','Nombre');
			$isql = 'SELECT SGD_DEVE_CODIGO AS "ID", SGD_DEVE_DESC AS "MOTIVO" FROM SGD_DEVE_DEV_ENVIO ORDER BY 1';
		}break;
	case 'tma'	:
		{	$titulo = "LISTADO GENERAL DE TEMAS";
			$tit_columnas = array('Id','Nombre','Dependencia Vinculada');
			$isql =	"SELECT t.SGD_TMA_CODIGO, t.SGD_TMA_DESCRIP, d.DEPE_NOMB 
					FROM DEPENDENCIA d, SGD_TMA_TEMAS t, SGD_TMD_TEMADEPE td
					WHERE td.SGD_TMA_CODIGO=t.SGD_TMA_CODIGO AND td.depe_codi=d.depe_codi
					ORDER BY t.SGD_TMA_DESCRIP, d.DEPE_NOMB";
		}break;
	case 'ctt'	:
		{	$titulo = "LISTADO GENERAL DE CONTACTOS";
			$tit_columnas = array('Tipo Contacto','Empresa/Entidad','Id Contacto','Nombre Contacto','Cargo Contacto','Telefono Contacto');
			$isql =	"SELECT c.CTT_ID_TIPO,b.NOMBRE_DE_LA_EMPRESA,c.CTT_ID, c.CTT_NOMBRE, c.CTT_CARGO, c.CTT_TELEFONO 
					FROM SGD_DEF_CONTACTOS c, BODEGA_EMPRESAS b
					WHERE c.CTT_ID_EMPRESA = b.IDENTIFICADOR_EMPRESA AND c.CTT_ID_TIPO=0
					UNION 
					SELECT c.CTT_ID_TIPO,b.SGD_OEM_OEMPRESA,c.CTT_ID, c.CTT_NOMBRE, c.CTT_CARGO, c.CTT_TELEFONO 
					FROM SGD_DEF_CONTACTOS c, SGD_OEM_OEMPRESAS b
					WHERE c.CTT_ID_EMPRESA = b.SGD_OEM_CODIGO AND c.CTT_ID_TIPO=1
					ORDER BY 1,2,4";			
		}break;
	case 'bge'	:
		{	$titulo = "LISTADO GENERAL DE ESP";
			$tit_columnas = array('Empresa','Sigla','Correo E', 'Tel&eacute;fonos' , 'NIT', 'NIUR', 'Id Empresa');
			$isql =	"SELECT NOMBRE_DE_LA_EMPRESA, SIGLA_DE_LA_EMPRESA, EMAIL, TELEFONO_1, NIT_DE_LA_EMPRESA,
					NUIR, IDENTIFICADOR_EMPRESA 
					FROM BODEGA_EMPRESAS
					ORDER BY NOMBRE_DE_LA_EMPRESA, SIGLA_DE_LA_EMPRESA";
			
		}break;
	case 'sts'	:
		{	$titulo = "LISTADO GENERAL DE SECTORES";
			$tit_columnas = array('Id Sector','Nombre');
			$isql =	"SELECT PAR_SERV_SECUE, PAR_SERV_NOMBRE FROM PAR_SERV_SERVICIOS ORDER BY PAR_SERV_SECUE";
			
		}break;
	default		:
		{	$titulo = "LISTADO GENERAL DE MUNICIPIOS";
			if($conti>0 or $pais>0 or $dept>0){
				$isql = "SELECT CON.NOMBRE_CONT, PAS.NOMBRE_PAIS,DEPT.DPTO_NOMB, MUN.MUNI_CODI,MUN.MUNI_NOMB FROM SGD_DEF_CONTINENTES AS CON LEFT JOIN SGD_DEF_PAISES AS PAS ON CON.ID_CONT = PAS.ID_CONT LEFT JOIN DEPARTAMENTO DEPT ON DEPT.ID_CONT = CON.ID_CONT AND DEPT.ID_PAIS=PAS.ID_PAIS LEFT JOIN MUNICIPIO MUN ON MUN.ID_CONT = PAS.ID_CONT AND MUN.ID_PAIS=PAS.ID_PAIS AND DEPT.DPTO_CODI=MUN.DPTO_CODI WHERE ";
				if($conti>0){
					$isql .= "MUN.ID_CONT=".$conti;
				}
				if($pais>0){
					$isql .= " AND MUN.ID_PAIS=".$pais;
				}
				if($dept>0 and strpos($dept,'-')>0){
					$dept=explode('-',$dept)[1];
					$isql .= " AND MUN.DPTO_CODI=".$dept;
				}
				$isql .=" ORDER BY CON.NOMBRE_CONT, PAS.NOMBRE_PAIS,DEPT.DPTO_NOMB, MUN.MUNI_NOMB ";
			}else{
				$isql = "SELECT CON.NOMBRE_CONT, PAS.NOMBRE_PAIS,DEPT.DPTO_NOMB, MUN.MUNI_CODI,MUN.MUNI_NOMB FROM SGD_DEF_CONTINENTES AS CON LEFT JOIN SGD_DEF_PAISES AS PAS ON CON.ID_CONT = PAS.ID_CONT LEFT JOIN DEPARTAMENTO DEPT ON DEPT.ID_CONT = CON.ID_CONT AND DEPT.ID_PAIS=PAS.ID_PAIS LEFT JOIN MUNICIPIO MUN ON MUN.ID_CONT = PAS.ID_CONT AND MUN.ID_PAIS=PAS.ID_PAIS AND DEPT.DPTO_CODI=MUN.DPTO_CODI
					ORDER BY CON.NOMBRE_CONT, PAS.NOMBRE_PAIS, 
                      DEPT.DPTO_NOMB, MUN.MUNI_NOMB ";
		      }
		}break;
}
$Rs_clta = $db->conn->Execute($isql); 

?>
<html>
<head>
<title><?= $titulo ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php include_once "../../htmlheader.inc.php";?>
</head>
<body>
<?php
switch ($_GET['var'])
{	case 'tar'	:
	case 'pai'	:
	case 'tpr'	:
	case 'fnv'	:
	case 'lcd'	:
	case 'dpc'	:
	case 'cau'	:
	case 'mdv'	:
	case 'tma'	:
	case 'ctt'	:
	case 'bge'	:
	case 'sts'	:
	case 'dpc'	:	
		{
            $sess = "&".session_name()."=".session_id();
            $html = rs2html(
                $db
                ,$Rs_clta
                ,'border=1 cellpadding=0 align=center'
                ,$tit_columnas
                ,true
                ,false
                ,$sess
                ,''
                ,''
                ,$rutaRaiz
                ,$checkAll=false
                ,$checkTitulo=false
                ,''
                ,'');
			$pos1 = strpos($html,"</TABLE>\n\n");
			$cnt_tmp = substr_count($html,"</TH>\n</tr>");
			if($cnt_tmp > 1)
			while(--$cnt_tmp)
			{
				$pos1 = strpos($html,"</TABLE>\n\n");
				$pos2 = strpos($html,"</TH>\n</tr>",$pos1)+11;
				$html = substr($html,0,$pos1) . substr($html,$pos2,strlen($html));
			}
			echo $html;
		}break;

	case 'dpt'	:
		{
			//var_dump( $Rs_clta); 
			echo '<style>table, th, td {border: 1px solid black;
						    border-collapse: collapse;
					            font-family: arial, helvetica, clean, sans-serif;
						    font-style: normal;}
				      td,th { text-align: center;} </style>';
//			$pager = new ADODB_Pager($db,$isql);
//                      $pager->Render($rows_per_page=20);
			echo '<table id="dt_basic" class="table table-bordered table-hover dataTable no-footer smart-form" style="width:100%">
			    <thead>	<tr><th>Continente</th><th>País</th><th>Id_Dpto</th><th> Departamento </th></tr></thead><tbody> ';
			while(!$Rs_clta->EOF){
			echo '<tr><td>'.$Rs_clta->fields["NOMBRE_CONT"].'</td>';
			echo '<td>'.$Rs_clta->fields["NOMBRE_PAIS"].'</td>';
			echo '<td>'.$Rs_clta->fields["DPTO_CODI"].'</td>';
			echo '<td>'.$Rs_clta->fields["DPTO_NOMB"].'</td></tr> ';
			$Rs_clta->MoveNext();
			}
		 	echo '</tbody></table></div>';
			$xsql = serialize($isql);
			$_SESSION['xsql']=$xsql;
			echo "<div><a style='border:0px' href='../../adodb/adodb-doc.inc.php?'".session_name()."=".session_id()."' target='_blank'><img src='../../adodb/compfile.png' width='40' heigth='40' border='0'></a>";
			echo "<a style='border:0px' href='../../adodb/adodb-xls.inc.php?'".session_name()."=".session_id()."' target='_blank'><img src='../../adodb/spreadsheet.png' width='40' heigth='40' border='0'></a></div>";
			echo '<script type="text/javascript">
    				// DO NOT REMOVE : GLOBAL FUNCTIONS!
    				pageSetUp();

    				// PAGE RELATED SCRIPTS

    				loadDataTableScripts();
    				function loadDataTableScripts() {

      				loadScript("../../js/plugin/datatables/jquery.dataTables-cust.js", dt_2);
      				function dt_2() {
          				loadScript("../../js/plugin/datatables/ColReorder.min.js", dt_3);
      				}
      				function dt_3() {
          				loadScript("../../js/plugin/datatables/FixedColumns.min.js", dt_4);
      				}
      				function dt_4() {
          				loadScript("../../js/plugin/datatables/ColVis.min.js", dt_5);
      				}
      				function dt_5() {
          				loadScript("../../js/plugin/datatables/ZeroClipboard.js", dt_6);
      				}
      				function dt_6() {
          				loadScript("../../js/plugin/datatables/media/js/TableTools.min.js", dt_7);
      				}
      				function dt_7() {
          				loadScript("../../js/plugin/datatables/DT_bootstrap.js", runDataTables);
      				}
  				}
    				function runDataTables() {
				       $("#dt_basic").dataTable({
             				"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            				 "iDisplayLength" : 20,
        			});
      				  /* Add the events etc before DataTables hides a column */
        			$("#datatable_fixed_column thead input").keyup(function() {
            				oTable.fnFilter(this.value, oTable.oApi._fnVisibleToColumnIndex(oTable.fnSettings(), $("thead input").index(this)));
        			});
        			$("#datatable_fixed_column thead input").each(function(i) {
           				 this.initVal = this.value;
        			});
        			$("#datatable_fixed_column thead input").focus(function() {
            				if (this.className == "search_init") {
                				this.className = "";
                				this.value = "";
            				}
        			});
        			$("#datatable_fixed_column thead input").blur(function(i) {
            				if (this.value == "") {
                				this.className = "search_init";
                				this.value = this.initVal;
            				}
       				 });
        			var oTable = $("#datatable_fixed_column").dataTable({
            				"sDom" : "<\'dt-top-row\'><\'dt-wrapper\'t><\'dt-row dt-bottom-row\'<\'row\'<\'col-sm-6\'i><\'col-sm-6 text-right\'p>>",
            			        "oLanguage" : {
                				"sSearch" : "Search all columns:"
            				},
            				"bSortCellsTop" : true
        			});
        			$("#datatable_col_reorder").dataTable({
            				"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            				"sDom" : "R<\'dt-top-row\'Clf>r<\'dt-wrapper\'t><\'dt-row dt-bottom-row\'<\'row\'<\'col-sm-6\'i><\'col-sm-6 text-right\'p>>",
            				"fnInitComplete" : function(oSettings, json) {
                				$(".ColVis_Button").addClass("btn btn-default btn-sm").html(\'Columns <i class="icon-arrow-down"></i>\');
            				}
        			});
        			$("#datatable_tabletools").dataTable({
            				"sDom" : "<\'dt-top-row\'Tlf>r<\'dt-wrapper\'t><\'dt-row dt-bottom-row\'<\'row\'<\'col-sm-6\'i><\'col-sm-6 text-right\'p>>",
            				"oTableTools" : {
                				"aButtons" : ["copy", "print", {
                					"sExtends" : "collection",
                					"sButtonText" : \'Save <span class="caret" />\',
                					"aButtons" : ["csv", "xls", "pdf"]
                				}],
                				"sSwfPath" : "js/plugin/datatables/media/swf/copy_csv_xls_pdf.swf"
            				},
            			"fnInitComplete" : function(oSettings, json) {
                			$(this).closest(\'#dt_table_tools_wrapper\').find(\'.DTTT.btn-group\').addClass(\'table_tools_group\').children(\'a.btn\').each(function() {
                    $(this).addClass("btn-sm btn-default");
                });
            }
        });
}
</script>';
		}break;
	default		: 
		{	
			 echo '<style>table, th, td {border: 1px solid black;
                                                    border-collapse: collapse;
                                                    font-family: arial, helvetica, clean, sans-serif;
                                                    font-style: normal;}
                                      td,th { text-align: center;} </style>';

			echo '<table id="dt_basic" class="table table-bordered table-hover dataTable no-footer smart-form"   style="width:100%">
			     	<thead><tr><th>Continente</th><th>País</th><th> Departamento </th><th>Muni_codi</th><th>Nombre Municipio</th></tr></thead><tbody> ';
			while(!$Rs_clta->EOF){
			echo '<tr><td>'.$Rs_clta->fields["NOMBRE_CONT"].'</td>';
			echo '<td>'.$Rs_clta->fields["NOMBRE_PAIS"].'</td>';
			echo '<td>'.$Rs_clta->fields["DPTO_NOMB"].'</td>';
			echo '<td>'.$Rs_clta->fields["MUNI_CODI"].'</td>';
			echo '<td>'.$Rs_clta->fields["MUNI_NOMB"].'</td></tr>';
			$Rs_clta->MoveNext();
			}
			echo '</tbody></table></div>';
			$xsql = serialize($isql);
			$_SESSION['xsql']=$xsql;
			echo "<div><a style='border:0px' href='../../adodb/adodb-doc.inc.php?'".session_name()."=".session_id()."' target='_blank'><img src='../../adodb/compfile.png' width='40' heigth='40' border='0'></a>";
			echo "<a style='border:0px' href='../../adodb/adodb-xls.inc.php?'".session_name()."=".session_id()."' target='_blank'><img src='../../adodb/spreadsheet.png' width='40' heigth='40' border='0'></a></div>";
			echo '<script type="text/javascript">
    				// DO NOT REMOVE : GLOBAL FUNCTIONS!
    				pageSetUp();

    				// PAGE RELATED SCRIPTS

    				loadDataTableScripts();
    				function loadDataTableScripts() {

      				loadScript("../../js/plugin/datatables/jquery.dataTables-cust.js", dt_2);
      				function dt_2() {
          				loadScript("../../js/plugin/datatables/ColReorder.min.js", dt_3);
      				}
      				function dt_3() {
          				loadScript("../../js/plugin/datatables/FixedColumns.min.js", dt_4);
      				}
      				function dt_4() {
          				loadScript("../../js/plugin/datatables/ColVis.min.js", dt_5);
      				}
      				function dt_5() {
          				loadScript("../../js/plugin/datatables/ZeroClipboard.js", dt_6);
      				}
      				function dt_6() {
          				loadScript("../../js/plugin/datatables/media/js/TableTools.min.js", dt_7);
      				}
      				function dt_7() {
          				loadScript("../../js/plugin/datatables/DT_bootstrap.js", runDataTables);
      				}
  				}
    				function runDataTables() {
				       $("#dt_basic").dataTable({
             				"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            				 "iDisplayLength" : 20,
        			});
      				  /* Add the events etc before DataTables hides a column */
        			$("#datatable_fixed_column thead input").keyup(function() {
            				oTable.fnFilter(this.value, oTable.oApi._fnVisibleToColumnIndex(oTable.fnSettings(), $("thead input").index(this)));
        			});
        			$("#datatable_fixed_column thead input").each(function(i) {
           				 this.initVal = this.value;
        			});
        			$("#datatable_fixed_column thead input").focus(function() {
            				if (this.className == "search_init") {
                				this.className = "";
                				this.value = "";
            				}
        			});
        			$("#datatable_fixed_column thead input").blur(function(i) {
            				if (this.value == "") {
                				this.className = "search_init";
                				this.value = this.initVal;
            				}
       				 });
        			var oTable = $("#datatable_fixed_column").dataTable({
            				"sDom" : "<\'dt-top-row\'><\'dt-wrapper\'t><\'dt-row dt-bottom-row\'<\'row\'<\'col-sm-6\'i><\'col-sm-6 text-right\'p>>",
            			        "oLanguage" : {
                				"sSearch" : "Search all columns:"
            				},
            				"bSortCellsTop" : true
        			});
        			$("#datatable_col_reorder").dataTable({
            				"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            				"sDom" : "R<\'dt-top-row\'Clf>r<\'dt-wrapper\'t><\'dt-row dt-bottom-row\'<\'row\'<\'col-sm-6\'i><\'col-sm-6 text-right\'p>>",
            				"fnInitComplete" : function(oSettings, json) {
                				$(".ColVis_Button").addClass("btn btn-default btn-sm").html(\'Columns <i class="icon-arrow-down"></i>\');
            				}
        			});
        			$("#datatable_tabletools").dataTable({
            				"sDom" : "<\'dt-top-row\'Tlf>r<\'dt-wrapper\'t><\'dt-row dt-bottom-row\'<\'row\'<\'col-sm-6\'i><\'col-sm-6 text-right\'p>>",
            				"oTableTools" : {
                				"aButtons" : ["copy", "print", {
                					"sExtends" : "collection",
                					"sButtonText" : \'Save <span class="caret" />\',
                					"aButtons" : ["csv", "xls", "pdf"]
                				}],
                				"sSwfPath" : "js/plugin/datatables/media/swf/copy_csv_xls_pdf.swf"
            				},
            			"fnInitComplete" : function(oSettings, json) {
                			$(this).closest(\'#dt_table_tools_wrapper\').find(\'.DTTT.btn-group\').addClass(\'table_tools_group\').children(\'a.btn\').each(function() {
                    $(this).addClass("btn-sm btn-default");
     		           });
           		 }
		        });
		}
		</script>';
		//$pager = new ADODB_Pager($db,$isql);
			//$pager->Render($rows_per_page=20);
			//echo "HOLS";
			break;
		}
}

?>
</body>
</html>
