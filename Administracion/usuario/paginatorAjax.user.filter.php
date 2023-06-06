<?php 
/**
*PARA METRIZACION DE PAGINACION
*@author Cesar Buelvas
*@mail cejebuto@gmail.com
*@date 01/02/2017
*/

/**
* Este archivo recibe el AJAX de todas las preguntas, en
* esta parte es recomendable hacer validaciones  
*/
$ruta_raiz = "../..";
require_once("$ruta_raiz/include/db/ConnectionHandler.php");
require_once "PaginationAjax.filter.class.php";

//Incluyo opción para Paginar
try {

		$db    = new ConnectionHandler($ruta_raiz);
		//$roles = new Roles($db);

		#echo "hello"; exit; 

	    #Bloque para parametrizar la paginación por defecto.
	    if(!$_POST['Page']){$_POST['Page']=1;}
	    if(!$_POST['Size_page']){$_POST['Size_page']=10;}
	    if(!$_POST['Order']){$_POST['Order']=2;}
	    if(!$_POST['By']){$_POST['By']=1;} 
	    if(!$_POST['Sql']){die('No se pudo obtener la consulta Sql ');} 
	    if(!$_POST['filter']){$_POST['filter']='%';} 
	    
		#PROCESO QUE SE INCLUYE EN LA CONSULTA PARA PAGINAR.
	    $Page     = intval($_POST['Page']);              //Page 
	    $SizePage = intval($_POST['Size_page']);         //Limit
	    $Order    = intval($_POST['Order']);             //Orden
	    $By       = intval($_POST['By']);                //By - number for order
	    $Sql      = $db->satinize($_POST['Sql']);
	    $filter   = $db->satinize($_POST['filter']);
	    $filter   = str_replace(" ", "%",$filter);    
	    
	    //Parametrizamos 
	    if ($SizePage == 0){$SizePage=10;}
	    if ($By==0){$By=1;}
	    if ($Order==0){$Order=1;} if($Order==1){$_Order=" DESC ";}else{$_Order=" ASC ";}
	    if ($Page<=1){$StartPage = 0;$Page = 1;} else {$StartPage = ($Page - 1) * $SizePage;}
	    
	    #echo "--->".$_Order; exit;  

	//Incluyo opción para Paginar  
	try {

		$pA = new PaginationAjax($db); 

		#Desencriptamos el sql, de esta manera evitamos la injeccion SQL   
		$Sql = $pA->dscr($Sql,'Kerpq12'); 

		#Obtenemos la respuesta del paginador 
		$sql_response = $pA->pagination($Sql,$_Order,$By,$SizePage,$StartPage,$filter); 
		
		//Respondemos la data. 
		echo json_encode($sql_response);

	} catch (Exception $e) {
		echo json_encode("Error ".$e->getMessage());
	} 


} catch (Exception $e) {
	echo json_encode("Error ".$e->getMessage());
} 
  
   
?>