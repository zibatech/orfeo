<?php
require_once('configRadiMail.php');
extract($_REQUEST,EXTR_OVERWRITE);
imap_timeout(IMAP_READTIMEOUT,300);
imap_timeout(IMAP_OPENTIMEOUT,300);
imap_timeout(IMAP_WRITETIMEOUT,300);

$inbox = imap_open($hostname,$usua_email,$passwd_mail)
	or die('Error al conectar a e-mail'.imap_last_error());
$emailCount=imap_num_msg($inbox);
//$emailCount=50;
if(!isset($page)){
	if(!isset($_GET['page'])){
		$page = 1;
	}else{
		$page = $_GET['page'];
	}
}
if($emailCount>=0) {
	$lastRow=(($page-1)*RADIMAIL_PAGINATION);
	$ini=$emailCount-$lastRow;

	if ($emailCount<=RADIMAIL_PAGINATION+$lastRow)
		$fin=0;
	else
		$fin=$emailCount-RADIMAIL_PAGINATION-$lastRow;
	
	for($i=$ini;$i>$fin;$i--) {
		$overview = imap_fetch_overview($inbox,$i,0);
        $overview2 = imap_headerinfo($inbox,$i,0);
        $dateEmail = strtotime($overview[0]->date);
		$emails[$i]['id'] = $i;
		$emails[$i]['uid'] = imap_uid($inbox,$i);
		$emails[$i]['mailAsunto'] = mb_strimwidth(imap_utf8(trim($overview[0]->subject)),0,50,'...');
		$emails[$i]['mailFecha'] = date("Y-m-d H:i:s", $dateEmail);
        $emails[$i]['mailFrom'] = htmlentities(imap_utf8(trim($overview[0]->from)));
		$emails[$i]['mailToF'] = htmlentities(imap_utf8(trim($overview[0]->to)));
        $emails[$i]['mailCC'] = htmlentities(imap_utf8(trim($overview2->ccaddress)));
		$emails[$i]['mailAttach'] = "";
		$emails[$i]['seen'] = trim($overview[0]->seen);
	}
	if (($page-1)*RADIMAIL_PAGINATION+RADIMAIL_PAGINATION<$emailCount)
		$hasta=($page-1)*RADIMAIL_PAGINATION+RADIMAIL_PAGINATION;
	else
		$hasta=$emailCount;
	$desde=($page-1)*RADIMAIL_PAGINATION+1;
	$smarty->assign('mails',$emails);
	$smarty->assign('countMails',$emailCount);
	$smarty->assign('j',$lastRow+1);
	$smarty->assign('page',$page);
	$smarty->assign('pagination',RADIMAIL_PAGINATION);
	$smarty->assign('desde',$desde);
	$smarty->assign('hasta',$hasta);
	$smarty->display('list.tpl');
}else{
	die("Error al cargar archivos...");
}
?>
