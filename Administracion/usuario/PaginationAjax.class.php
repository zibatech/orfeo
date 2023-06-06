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


	function pagination($Sql,$Order,$By,$SizePage,$StartPage){ 

		//$By = 'p8';   

		/* validacion */			
		if($Sql == ''){
			die('No se obtubo un sql para ejecutar');
		}

		$originalSql = $Sql;	 
		/* Obtenemos el codigo sql */ 
		$Sql = $Sql." ORDER BY ".$By." ".$Order;
		#echo $Sql; exit;

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
