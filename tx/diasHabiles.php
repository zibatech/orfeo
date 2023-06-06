<?
//-------------------------------
// FUNCION PARA CALCULAR LA FECHA DE VENCIMIENTO
//-------------------------------  
CLASS FechaHabil
{
var $db;


function __construct($db){
  $this->FechaHabil($db);
}

function FechaHabil($db)
{
	$this->db = $db;
}


function diasHabiles($fechaIni,$fechaFin)
{
	/*echo $fechaIni."--".$fechaFin; exit;  */
	if ($fechaIni>$fechaFin){
		$a=$fechaIni;
		$fechaIni=$fechaFin;
		$fechaFin=$a;
	}   
	$cDiasAdicionL = 0;
	$termino = $fldTERMINO;
	$fechVenc = $fldF1;
	$cDiasCalen = 1;
	$cDiasHabil = 0;
	$fechaIni = str_replace("-","/",$fechaIni);
	$fechaFin = str_replace("-","/",$fechaFin);
	$isql_busca = " 
		select count(1) DIASH
		from sgd_noh_nohabiles
		where to_char(noh_fecha,'YYYY/MM/DD') >= '$fechaIni'
		and to_char(noh_fecha,'YYYY/MM/DD') <= '$fechaFin'
		";
	$rs = $this->db->query($isql_busca);
	$diasFestivos = 0;
	if(!$rs->EOF )
	{   
		$diasFestivos =  $rs->fields["DIASH"];
	}   
	$fechaTMP = $fechaIni;
	$ii = 0;
	$diasFinSemana = 0;
	$kDia = 0;
	$diasCalendario = 0;
	while($fechaTMP<$fechaFin and $ii<=300)
	{   
		$dia = substr($fechaTMP,-2);
		$mes = substr($fechaTMP,5,2);
		$ano = substr($fechaTMP,0,4);
		$fechaTMP = mktime(0, 0, 0, $mes  , $dia+$kDia, $ano);
		$diaDelMes = date("w",mktime(0, 0, 0, $mes  , $dia+$kDia, $ano));
		$fechaTMP1 = date('m/d/Y', $fechaTMP);
		$fechaTMP = date('Y/m/d', $fechaTMP);
		/*echo "$fechaTMP<=$fechaFin<hr>";      */
		if($diaDelMes==0 or $diaDelMes==6) 
		{   
			$diasFinSemana++;
		}
		$kDia = 1;
		$diasCalendario++;
		$ii++;
	}
	/*echo "<br> Dias calendarios = ".$diasCalendario;*/
	/*echo "<br> Dias Festivos =".$diasFestivos; exit;*/
	$cDiasSumar = $diasCalendario - ($diasFestivos + $diasFinSemana);
	/*$cDiasSumar = $diasCalendario - ($diasFestivos );*/

	return (isset($a))?-$cDiasSumar+1:$cDiasSumar ;
}


function diaFinHabil($fechaIni,$diasTermino)
{	
	$cDiasAdicionL = 0;
	$termino = $fldTERMINO;
	$fechVenc = $fldF1;
	$cDiasCalen = 1;
	$cDiasHabil = 0;
	

	$fechaTMP = $fechaIni;
 $ii = 0;
 $diasFinSemana = 0;
 $diasTMP = 0;
	while($diasTMP<=$diasTermino and $ii<=50)
	{
		$dia = substr($fechaTMP,-2);
		$mes = substr($fechaTMP,5,2);
		$ano = substr($fechaTMP,0,4);
		$fechaTMP = mktime(0, 0, 0, $mes  , $dia+1, $ano);
		$fechaTMP = date('Y/m/d', $fechaTMP);
		if(date('w',$fechaTMP)==0 or date('w',$fechaTMP)==6) $diasTMP++;
	
		$isql_busca = "
			select count(1) DIASH
			from sgd_noh_nohabiles
			where to_char(noh_fecha,'YYYY/MM/DD') >= '$fechaIni'
			and to_char(noh_fecha,'YYYY/MM/DD') <= '$fechaFin'
		";
		$rs = $this->db->query($isql_busca);
	$diasFestivos = 0;
	if(!$rs->EOF )
  	{
		 $diasFestivos =  $rs->fields["DIASH"];
		}

		$diasFinSemana++;
	}
	
	$cDiasSumar = $diasFestivos + $diasFinSemana;
	return $cDiasSumar;
}
function suma_fechas($fecha,$ndias)
{

$fecha = date('Y-m-j');
$nuevafecha = strtotime ( '+2 day' , strtotime ( $fecha ) ) ;
$nuevafecha = date ( 'Y-m-j' , $nuevafecha );

	//list($dia,$mes,$a?o)=split("-",$fecha);
/*	$dia="01";
	$mes="02";
	$dia="2005";
	if ($mes == 'JAN') $mesn = 1;
	elseif ($mes == 'FEB') $mesn = 2;
	elseif ($mes == 'MAR') $mesn = 3;
	elseif ($mes == 'APR') $mesn = 4;
	elseif ($mes == 'MAY') $mesn = 5;
	elseif ($mes == 'JUN') $mesn = 6;
	elseif ($mes == 'JUL') $mesn = 7;
	elseif ($mes == 'AUG') $mesn = 8;
	elseif ($mes == 'SEP') $mesn = 9;
	elseif ($mes == 'OCT') $mesn = 10;
	elseif ($mes == 'NOV') $mesn = 11;
	elseif ($mes == 'DEC') $mesn = 12;
	
*/
   return ($nuevafecha);
}

/*****FUNCION PARA OBTENER LA FECHA DE VENCIMIENTO DE UN RADICADO TIPIFICADO - CESAR BUELVAS *****/
function getFVencimiento($fechaIni,$termino)
{
//Fecha del radicado y el termino
//echo "--------------->".$fechaIni."--".$termino."<br><br>" ; 
$fecha_radicado = $fechaIni;
$noDiasFestivos = 0;
$diasHabiles = 0;

for ($i = 1; $i <= $termino*3; $i++) {

if ($diasHabiles < $termino ){

		$isql_busca = "
			select count(distinct 1) DIASH
			from sgd_noh_nohabiles
			where noh_fecha = '$fechaIni'
		"; 

		$rs = $this->db->query($isql_busca);

	$diasFestivos = 0;
	if(!$rs->EOF )
  	{
	$diasFestivos =  $rs->fields["DIASH"];
        	 if ($diasFestivos > 0){
		$noDiasFestivos = $noDiasFestivos + 1;
		}else{
	 	$diasHabiles = $diasHabiles + 1;
		}
	}
 $fechaIni = strtotime ( '+1 day' , strtotime ( $fechaIni ) ) ;
 $fechaIni = date ( 'Y-m-j' , $fechaIni );

$Parar = false;

}else{
if ($Parar == false ){
$isql_busca = "
             select count(distinct 1) DIASH
             from sgd_noh_nohabiles
             where noh_fecha = '$fechaIni'";
	     $rs = $this->db->query($isql_busca);

         if(!$rs->EOF )
         {
         $diasFestivos =  $rs->fields["DIASH"];
                  if ($diasFestivos > 0){
                 $noDiasFestivos = $noDiasFestivos + 1;
                 }else{
                 $Parar = true;
                 }
         }
 $fechaIni = strtotime ( '+1 day' , strtotime ( $fechaIni ) ) ;
 $fechaIni = date ( 'Y-m-j' , $fechaIni );

}//fin Parar = false
	}//fin IF
}//fin del For

 $dias_sumar = $noDiasFestivos + $termino;
 
 $nuevafecha = strtotime ( "+$dias_sumar day" , strtotime ( $fecha_radicado ) ) ;
 $nuevafecha = date ( 'Y-m-j' , $nuevafecha );

/*Devolvemos la nueva fecha*/
return($nuevafecha);

/*Con esto retornamos los dias festivos que hay desde la fecha del radicado , hasta la fecha de vencimiento*/
//return ($noDiasFestivos);

}
/**Obtengo el termino de un radicado - CESAR BUELVAS**/
function getTermino($radicado)
{
 //echo $numeroRadicado.">".$fech_vcmto; exit;
 
 $sql_getcodtpr ="select TDOC_CODI from radicado where radi_nume_radi ='$radicado'";
 $_exec_getcodtpr = $this->db->query($sql_getcodtpr);
 $codigo_tpr = $_exec_getcodtpr ->fields["TDOC_CODI"];

if ($codigo_tpr <> 0){

 $sql_getcodtpr ="select SGD_TPR_TERMINO  as Termino from sgd_tpr_tpdcumento where SGD_TPR_CODIGO = '$codigo_tpr'";
 $_exec_getcodtpr = $this->db->query($sql_getcodtpr);

 $termino = $_exec_getcodtpr ->fields["TERMINO"];
}else{
$termino = 0;
}
return($termino);
}

/***Obtengo la fecha de radicado de un radicado **/
function getFechRadicado($radicado)
 {
  //echo $numeroRadicado.">".$fech_vcmto; exit;
  
  $sql_getrfr ="select RADI_FECH_RADI from radicado where radi_nume_radi ='$radicado'";
  $_exec_getrfr = $this->db->query($sql_getrfr);
  $fecha_radi = $_exec_getrfr ->fields["RADI_FECH_RADI"];
  
 if (!$fecha_radi){
  $fecha_radi = 0;
 }
 return($fecha_radi);
 }

/**Obtengo los dias RESTANTES verdaderos, solo con el numero de radicado - CESAR BUELVAS***/
function getDiasRestantes($radicado,$fecha_ven,$trd)
{ 
//MIENTRAS SE BUSCA SOLUCION MAS RAPIDA PARA DIAS HABILES SE MODIFICA LA FUNCION

if ($trd == "NO DEFINIDO"){
//SI NO TIENE TRD DEVUELVE UN "-" EN SALIDA
$dias_restantes = "-";
}else{
//COMPROBAMOS SI TIENE FECHA DE VENCIMIENTO, EN CASO CONTRARIO RETORNAMOS "N/A"

	if (!$fecha_ven){
		$dias_restantes = "N/A ó termino no definido";
	}else{
		//$dias_restantes = $this->diasHabiles(date("Y-m-d"),$fecha_ven);
	$fechaIni = date("Y-m-d");
		$fechaFin = $fecha_ven;
	        if ($fechaIni>$fechaFin){
	            $a=$fechaIni;
	            $fechaIni=$fechaFin;
	            $fechaFin=$a;
	         }
		$auxiliar = $this->dias_transcurridos($fechaIni,$fechaFin);
		if($a){$dias_restantes = $auxiliar * -1;}else{$dias_restantes = $auxiliar;} 
	}
}

return($dias_restantes);

//*********_____________________***************______________**************//

/*
$fecha_radicado = $this->getFechRadicado($radicado);
$termino = $this->getTermino($radicado);
$fecha_vencimiento = $this->getFVencimiento($fecha_radicado,$termino);
$dias_festivos = $this->getDiasFestivos($fecha_radicado,$termino);
    if ($fecha_radicado>$fecha_vencimiento){
            $a=$fecha_radicado;
            $fecha_radicado=$fecha_vencimiento;
            $fecha_vencimiento=$a;
       }

// Calulo los dias calndarios
$dias_totales = $this->dias_transcurridos($fecha_radicado,$fecha_vencimiento);

if ($a){$dias_totales = $dias_totales *-1;}

$dias_restantes = $dias_totales - $dias_festivos ; 
$dias_restantes = $this->diasHabiles(date("Y-m-d"),$fecha_vencimiento);

if ($termino==0){$dias_restantes= "";}

return($dias_restantes);
*/

 }
/* Dias calendarios entre dos fechas */
function dias_transcurridos($fecha_i,$fecha_f)
{
	$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
	$dias 	= abs($dias); $dias = floor($dias);		
	return $dias;
}

/*** FUNCION PARA OBTENER LOS DIAS FESTIVOS ENTRE 1 FECHA y su termino***/
function getDiasFestivos($fechaIni,$termino)
{
//Fecha del radicado y el termino
//echo "--------------->".$fechaIni."--".$termino."<br><br>" ; 
$fecha_radicado = $fechaIni;
$noDiasFestivos = 0;
$diasHabiles = 0;

for ($i = 1; $i <= $termino*3; $i++) {

if ($diasHabiles < $termino ){

		$isql_busca = "
			select count(distinct 1) DIASH
			from sgd_noh_nohabiles
			where noh_fecha = '$fechaIni'
		"; 

		$rs = $this->db->query($isql_busca);

	$diasFestivos = 0;
	if(!$rs->EOF )
  	{
	$diasFestivos =  $rs->fields["DIASH"];
        	 if ($diasFestivos > 0){
		$noDiasFestivos = $noDiasFestivos + 1;
		}else{
	 	$diasHabiles = $diasHabiles + 1;
		}
	}
 $fechaIni = strtotime ( '+1 day' , strtotime ( $fechaIni ) ) ;
 $fechaIni = date ( 'Y-m-j' , $fechaIni );

$Parar = false;

}else{
if ($Parar == false ){
$isql_busca = "
             select count(distinct 1) DIASH
             from sgd_noh_nohabiles
             where noh_fecha = '$fechaIni'";
	     $rs = $this->db->query($isql_busca);

         if(!$rs->EOF )
         {
         $diasFestivos =  $rs->fields["DIASH"];
                  if ($diasFestivos > 0){
                 $noDiasFestivos = $noDiasFestivos + 1;
                 }else{
                 $Parar = true;
                 }
         }
 $fechaIni = strtotime ( '+1 day' , strtotime ( $fechaIni ) ) ;
 $fechaIni = date ( 'Y-m-j' , $fechaIni );

}//fin Parar = false
	}//fin IF
}//fin del For

 $dias_sumar = $noDiasFestivos + $termino;
 
 $nuevafecha = strtotime ( "+$dias_sumar day" , strtotime ( $fecha_radicado ) ) ;
 $nuevafecha = date ( 'Y-m-j' , $nuevafecha );

/*Con esto retornamos los dias festivos que hay desde la fecha del radicado , hasta la fecha de vencimiento*/
return ($noDiasFestivos);

}



}
?>
