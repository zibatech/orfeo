<?php

#@author nimeshrmr
#@modified COGEINSAS Wilson Hernández Ortiz <-wilsonhernandezortiz@gmail.com->

# Las funciones presentes en este archivo, han sido extraidas de 
# http://www.sitepoint.com/exploring-phps-imap-library-1/ y 
# http://www.sitepoint.com/exploring-phps-imap-library-2/
# y modificadas para su pleno funcionamiento con OrfeoGPL.

# The functions present in this file have been extracted from
# http://www.sitepoint.com/exploring-phps-imap-library-1/ and
# http://www.sitepoint.com/exploring-phps-imap-library-2/
# and modified for full operation with OrfeoGPL.

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
		if ($part->disposition == "ATTACHMENT") {
			$partStruct = imap_bodystruct($imap, $mailNum,
					$partNum);
			if ($part->dparameters[0]->value){
				$attName=$part->dparameters[0]->value;
			}elseif ($part->parameters[0]->value){
				$attName=$part->parameters[0]->value;
			}else{
				$attName="noName.pdf";
			}
			$attachmentDetails = array(
					"name"    => $attName,
					"partNum" => $partNum,
					"enc"     => $partStruct->encoding,
					"type"    => getMimetype($partStruct)
					);
			return $attachmentDetails;
		}
	}

	return $attachments;
}

function downloadAttachment($imap, $uid, $partNum, $encoding, $path, $anexName=false, $nurad=false, $auto_name=false) {
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
	if (!$anexName and !$nurad and !$auto_name){
		$filename=str_replace(' ','',$filename);
		header("Content-Description: File Transfer");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=" . $filename);
		header("Content-Transfer-Encoding: binary");
		header("Expires: 0");
		header("Cache-Control: must-revalidate");
		header("Pragma: public");
		echo $message;
	}else{
		if ($auto_name){
			$url = "/tmp/radimail/$auto_name";

			$fopen = fopen($url, 'w');
			fwrite($fopen, $message);
			fclose($fopen);
		}else{
			$url = "../bodega/".substr($nurad,0,4) ."/".$_SESSION["dependencia"] ."/docs/$anexName";
			$fopen = fopen($url, 'w');
			fwrite($fopen, $message);
			fclose($fopen);
		}
	}
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
