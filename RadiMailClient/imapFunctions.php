<?php
include_once("functionsNimeshrmr.php");

#@author COGEINSAS Wilson Hernández Ortiz <-wilsonhernandezortiz@gmail.com->

# Las funciones aqui presentes han sido desarrolladas por COGEINSAS como una contribucion a la comunidad OrfeoGPL y a la fundacion Correlibre.

function getSection($structure){
	if (isset($structure->parts[0]->parts)){
		if (isset($structure->parts[0]->parts[0]->parts)){
			$section=1.1;# Si tiene imagenes embebidas  &&  adjuntos
		}
		else{
			$section=1.2;# Si tiene imagenes embebidas  ||   adjuntos
		}
	}
	elseif($structure->parts[1]->disposition=="ATTACHMENT"){
		$section=1;#Solo tiene adjuntos
	}elseif($structure->subtype=="HTML" or $structure->subtype=="PLAIN"){
		$section=1;#Solo tiene adjuntos
	}else{
		$section=2;#Si NO tiene adjuntos
	}
	return $section;
}
function getBody($inbox,$msgNo,$section,$charset){
	switch ($charset){
		case "UTF-8":
			$body =  utf8_decode(imap_qprint(imap_fetchbody($inbox,$msgNo,$section)));
			break;
		case "ISO-8859-1":
			$body =  utf8_encode(imap_qprint(imap_fetchbody($inbox,$msgNo,$section)));
			break;
		default:
			$body =  imap_qprint(imap_fetchbody($inbox,$msgNo,$section));
			break;
	}
	$body =  utf8_decode(imap_qprint(imap_fetchbody($inbox,$msgNo,$section)));
	if(!mb_detect_encoding($body, 'UTF-8', TRUE)) $body=utf8_encode($body);
	if($section=='1.1'){
		$body =  strstr ($body,"<div");# Limpia el string de salida.
	}
	if($section=='1.1' || $section=='1.2'){
		$inline=getInline($inbox,$msgNo,$section);
		$dom = new DOMDocument();
		$dom->loadHTML($body);

		//Evaluate Anchor tag in HTML
		$xpath = new DOMXPath($dom);
		$imgs = $xpath->evaluate("/html/body//img");

		for ($i = 0; $i < $imgs->length; $i++) {
			$img = $imgs->item($i);
			//remove and set target attribute       
			$isCid=explode(':',$img->getAttribute('src'));
			if ($isCid[0]=='cid'){
				$img->removeAttribute('src');
				$img->setAttribute("src", "data:image;base64,".$inline[$i]);
			}
		}
		// save html
		$body=$dom->saveHTML();
	}
	return $body;
}
function getInline($inbox,$msgNo,$section){
	#retorna array con las imagenes embebidas
	#returns array with the embedded images
	$structure =  imap_fetchstructure($inbox,$msgNo);
	if($section=='1.1'){
		$countInline=count($structure->parts[0]->parts);
		for ($i=$section+.1;$i<$section+.1*$countInline;$i=$i+.1){
			$inline[]=imap_fetchbody($inbox,$msgNo,$i);
		}
	}elseif($section=='1.2'){
		if ($structure->parts[1]->disposition=="INLINE")
			$countInline=count($structure->parts);
		for ($i=2;$i<=$countInline;$i++){
			$inline[]=imap_fetchbody($inbox,$msgNo,$i);
		}
	}
	return array_reverse($inline);	
}
function fileAdttachments($db,$nurad,$user,$filename,$attachNumber,$dependence){
	$ext=strtolower(array_pop(explode(".",$filename)));
	$type = "SELECT ANEX_TIPO_CODI FROM ANEXOS_TIPO WHERE ANEX_TIPO_EXT = '$ext'";
	$type = $db->conn->query($type);
	$type = $type->fields["ANEX_TIPO_CODI"];
	if(!$type) $type = 99;
	$attachNumber=str_pad($attachNumber, 5, "0", STR_PAD_LEFT);
	$code = "$nurad$attachNumber";
	$anexName = $nurad."_$attachNumber.$ext";
	$record["ANEX_RADI_NUME"]    = $nurad;
	$record["ANEX_CODIGO"]       = "'$code'";
	$record["ANEX_SOLO_LECT"]    = "'S'";
	$record["ANEX_CREADOR"]      = "'$user'";
	$record["ANEX_DESC"]         = "' Archivo:.". $filename."'";
	$record["ANEX_NUMERO"]       = $attachNumber;
	$record["ANEX_NOMB_ARCHIVO"] = "'$anexName'";
	$record["ANEX_BORRADO"]      = "'N'";
	$record["ANEX_DEPE_CREADOR"] = $dependence;
	$record["SGD_TPR_CODIGO"]    = '0';
	$record["ANEX_TIPO"]         = $type;
	$sqlDate=$db->conn->DBDate(Date("Y-m-d"));
	$record["ANEX_FECH_ANEX"]    = $sqlDate;
	$anex['name']=$anexName;
	$anex['code']=$code;
	if ($db->insert("anexos", $record, "true")){
		return $anex;
	}
	return false;
	
}
function getCharset($section,$structure){
	if($section=='1.1'){
		$charset=$structure->parts[0]->parts[0]->parts[1]->parameters[0]->value;
	}elseif($section=='1.2'){
		if (is_array($structure->parts[0]->parts[1]->parameters)){
			$charset=$structure->parts[0]->parts[1]->parameters[0]->value;
		}else{
			return "No charset";
		}
	}elseif($section=='2'){
		$charset=$structure->parts[1]->parameters[0]->value;
	}
	return $charset;
}

?>
