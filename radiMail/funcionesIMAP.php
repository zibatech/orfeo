<?
function getSection($structure){
	if (isset($structure->parts[0]->parts)){
		if (isset($structure->parts[0]->parts[0]->parts)){
			$section=1.1;# Si tiene imagenes embebidas  &&  adjuntos
		}
		else{
			$section=1.2;# Si tiene imagenes embebidas  ||   adjuntos
		}
	}
	else{
		$section=2;#Si NO tiene adjuntos
	}
	return $section;
}
/* 
function getBody($inbox,$msgNo,$section,$charset){
	switch ($charset){
		case "Windows-1252":
			$body =  iconv('UTF-8', 'UTF-8', utf8_decode(imap_qprint(imap_fetchbody($inbox,$msgNo,$section))));
			break;
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
*/
function getBody($uid, $imap) {
    $body = get_part($imap, $uid, "TEXT/HTML");
    // if HTML body is empty, try getting text body
    if ($body == "") {
        $body =  get_part($imap, $uid, "TEXT/PLAIN");
        
    }
		if(!mb_detect_encoding($body, 'UTF-8', TRUE)) $body=utf8_encode($body);
    return $body;
}
 
function get_part($imap, $uid, $mimetype, $structure = false, $partNumber = false) {
    if (!$structure) {
           $structure = imap_fetchstructure($imap, $uid, FT_UID);
    }
    if ($structure) {
        if ($mimetype == get_mime_type($structure)) {
            if (!$partNumber) {
                $partNumber = 1;
            }
            $text = imap_fetchbody($imap, $uid, $partNumber, FT_UID);
            switch ($structure->encoding) {
                case 3: return imap_base64($text);
                case 4: return imap_qprint($text);
                default: return $text;
           }
       }
 
        // multipart 
        if ($structure->type == 1) {
            foreach ($structure->parts as $index => $subStruct) {
                $prefix = "";
                if ($partNumber) {
                    $prefix = $partNumber . ".";
                }
                $data = get_part($imap, $uid, $mimetype, $subStruct, $prefix . ($index + 1));
                if ($data) {
                    return $data;
                }
            }
        }
    }
    return false;
}
 
function get_mime_type($structure) {
    $primaryMimetype = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");
 
    if ($structure->subtype) {
       return $primaryMimetype[(int)$structure->type] . "/" . $structure->subtype;
    }
    return "TEXT/PLAIN";
}
function getInline($inbox,$msgNo,$section){
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
function getAttachments($imap, $mailNum, $part, $partNum) {
	$attachments = array();

	if (isset($part->parts)) {
		foreach ($part->parts as $key => $subpart) {
			if($partNum != "") {
				$newPartNum = $partNum . "." . ($key + 1);
			}
			else {
				$newPartNum = ($key+1);
			}
			$result = getAttachments($imap, $mailNum, $subpart,
					$newPartNum);
			if (count($result) != 0) {
				array_push($attachments, $result);
			}
		}
	}
	else if (isset($part->disposition)) {
		if ($part->disposition == "ATTACHMENT" or $part->disposition == "attachment") {
			$partStruct = imap_bodystruct($imap, $mailNum,
					$partNum);
			$attachmentDetails = array(
					"name"    => $part->dparameters[0]->value,
					"partNum" => $partNum,
					"enc"     => $partStruct->encoding
					);
			return $attachmentDetails;
		}
	}

	return $attachments;
}
function fileAdttachments($db,$nurad,$user,$filename,$attachNumber,$dependence){
	//$db->conn->debug=true;
	$ext=strtolower(array_pop(explode(".",$filename)));
	//$ext=array_pop(explode(".",$filename));
	$type = "SELECT ANEX_TIPO_CODI FROM ANEXOS_TIPO WHERE ANEX_TIPO_EXT = '$ext'";
	$type = $db->conn->query($type);
	$type = $type->fields["ANEX_TIPO_CODI"];
	if(!$type) $type = 99;
	$attachNumber=str_pad($attachNumber, 5, "0", STR_PAD_LEFT);
	//$code = $nurad."0000".$attachNumber;
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
function downloadAttachment($imap, $uid, $partNum, $encoding, $path, $anexName=false, $nurad=false) {
	$partStruct = imap_bodystruct($imap, imap_msgno($imap, $uid), $partNum);
	$filename = $partStruct->dparameters[0]->value;
	$message = imap_fetchbody($imap, $uid, $partNum, FT_UID);

	switch ($encoding) {
		case 0:
		case 1:
			$message = imap_8bit($message);
			break;
		case 2:
			$message = imap_binary($message);
			break;
		case 3:
			$message = imap_base64($message);
			break;
		case 4:
			$message = quoted_printable_decode($message);
			break;
	}
	if (!$anexName and !$nurad){
		$filename=str_replace(' ','',$filename);
        if(strtolower(explode('.',$filename)[1])=='pdf'){
            header('Content-type: application/pdf');
            header("Content-Disposition: inline; filename=" . $filename);   
        }else{
            header("Content-Description: File Transfer");
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . $filename);
        }
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma: public");
        echo $message;
	}else{
    $url = "../bodega/".substr($nurad,0,4) ."/".$_SESSION["dependencia"] ."/docs/$anexName";
		$fopen = fopen($url, 'w');
		fwrite($fopen, $message);
		fclose($fopen);
	}
}
function getCharset($section,$structure){
	if($section=='1.1'){
		$charset=$structure->parts[0]->parts[0]->parts[1]->parameters[0]->value;
	}elseif($section=='1.2'){
		$charset=$structure->parts[0]->parts[1]->parameters[0]->value;
	}elseif($section=='2'){
		$charset=$structure->parts[1]->parameters[0]->value;
	}
	return $charset;
}
function getMimetype($structure) {
	$section=getSection($structure);
	if ($section=='1.2'){
		$structure=$structure->parts[1];
	}
	$mimetype = array("TEXT",
			"MULTIPART",
			"MESSAGE",
			"APPLICATION",
			"AUDIO",
			"IMAGE",
			"VIDEO",
			"OTHER");
	if ($structure->subtype) {
		return $mimetype[(int)$structure->type] . "/" . $structure->subtype;
	}
	return "TEXT/PLAIN";
}
?>
