<?php 
/**
* Clase que permite Guardar en el Formulario de una pregunta del formulario
*@author Cesar Buelvas
*@mail cejebuto@gmail.com
*@date 01/02/2017
*/ 
#require_once "../../survey/class/SaveSurvey.class.php";  
#require_once "../libs/Config.php";
#require_once "../libs/AjaxAdodb.php";
require_once "AbstractModel.php";
#require_once "../class/Form.class.php";



class PaginationAjax extends AbstractModel
{	 


	function pagination($Sql,$Order,$By,$SizePage,$StartPage,$filter){ 

		//$By = 'p8';   

		/* validacion */			
		if($Sql == ''){
			die('No se obtubo un sql para ejecutar');
		}

		$selectores = explode(" from ", $Sql);
 		$arraySelectores= explode(",", $selectores[0]);
 		$arraySelectores = self::getNameFields($arraySelectores);

 		#Se plantea el like
 		$filter = "'%".$filter."%'";
 		#mayusculas el filtro
 		$filter = " LIKE ".self::upperCase($filter);


 		$where_filter = '';
 		#Recorremos
 		foreach ($arraySelectores as $key => $value) {
 			#Casteamos los valores a string y los convertimos todos en mayuscula
 			$value = trim(ltrim(strtoupper($value),"SELECT "));
 			$value = self::castText($value);
 			$value = self::upperCase($value);

 			$where_filter .= $value." ".$filter. " OR ";
 			#ARMAR WHERE CON ESTO Y PEGARLO AL SQL
 		}
 		$where_filter = rtrim($where_filter,"OR ");
 		$Sql = strtoupper($Sql);

 		#PREGUNTAR SI EXISTE O NO EL WHERE 
		$pos = strpos($Sql, ' WHERE ');

		// Nótese el uso de ===. Puesto que == simple no funcionará como se espera
		// porque la posición de 'a' está en el 1° (primer) caracter.
		if ($pos === false) {
			$where_filter = ' WHERE '.$where_filter;
		} else {
			$where_filter = ' AND ('.$where_filter.' ) ';
		}
		$Sql = $Sql." ".$where_filter;

		/* Obtenemos el codigo sql */ 
		$Sql = $Sql." ORDER BY ".$By." ".$Order;
		$originalSql = $Sql;	

		//Ejecuto la consulta 
		$sla = self::SelectLimitArray($Sql,$SizePage,$StartPage);  //APLICANDO LIMITES
			$rs = $sla[0];
			$showdata = $sla[1];
			$conteo =  $sla[2]; 

		//Compruebo la consulta  
		if (!$rs){
		    
		    //Compruebo si tengo que mostrar el debug ó el mensaje, por la variable $_error_debug del config 
		    if ($_error_debug == true){
		        self::SelectLimitArray($Sql,$SizePage,$StartPage,true);
		    }else{
		        $_class_msg = "danger";
		        $_Msg_response = "Ocurrió un error procesando los datos, Por favor contacte con el administrador del sistema ; ERR:BC01";
		    }

		}else{

		    //Si se construyó correctamente la consulta

		    //Cuento las Filas.
		    if ($showdata == true){ $num_row_query = $conteo;} else {$num_row_query = 0;}

		        if( $num_row_query >= 1 ) { // Si hay por lo menos un solo registro procedo a ingresar al aplicativo
		            $_class_msg = "success";
		            $_Msg_response = true;
		          
		            //SE USA ESTE BLOQUE PARA PAGINNAR SI SE REQUIERE -----
		            //$rs_paginate = self::db->Execute($Sql); //CAMBIAR POR UN SELECT COUNT   
		            $NumRowTotal = self::rowCountSql($originalSql);
		            //calculo el total de páginas
		            $Total_pages = ceil($NumRowTotal / $SizePage);
		            //--------------------

		        } else{
		            $_class_msg = "warning";
		           $_Msg_response = "No hay registros para mostrar.";
		           $rs = null;
		        }// fin de contar filas 

		    } // fin de comprobar la consulta 

 
		//Retornamos -> $_Msg_response; 
		$sql_response[0] = $rs;
		$sql_response[1] = $_Msg_response;
		$sql_response[2] = $_class_msg;
		$sql_response[3] = $Total_pages;
		$sql_response[4] = $StartPage;
		$sql_response[5] = $NumRowTotal;

		return $sql_response;

	}





	function testing() {
		//return 'prueba';
		$tipoSelect = self::getAll($select,$where,null,'FIELDS');

		$form = new Form();
		$sql = $form->getSqlChoice($field_choicename,$choice_type_id);

		die('prueba');
	}

	/* function refresSelect(){

	    $id_encuesta = $_POST['id_encuesta'];
	    $idObject = $_POST['idObject'];
	    $whereFinal = $_POST['whereFinal'];

		$select = ["FIELD_CHOICENAME","CHOICE_TYPE_ID"];
		$where = ["project_id"=>$id_encuesta,"field_id"=>$idObject ];
		$tipoSelect = self::getAll($select,$where,null,'FIELDS');
	    
	    //self::tprint($tipoSelect); 

	    $field_choicename = $tipoSelect[0]['field_choicename'];
	    $choice_type_id = $tipoSelect[0]['choice_type_id'];

		//echo "->".$field_choicename." / ".$choice_type_id; exit;

		$form = new Form();
		$sql = $form->getSqlChoice($field_choicename,$choice_type_id);

		//Complementamos con el where 
		if ($whereFinal != ""){
			$sql .= " 	WHERE ".$whereFinal;
		}
		//echo ">".$sql; exit;

		$arrayOption = self::getArrayKeyValue($sql);

		#Convertirlo en json
		#$arrayOption = json_encode($arrayOption);

		$respuesta[0] = true;
		$respuesta[1] = $arrayOption;
		//self::tprint($arrayOption); exit; 

		return $respuesta;

	} */
}
