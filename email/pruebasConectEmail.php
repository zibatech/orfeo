<?
$mbox = imap_open ("{192.168.100.32:143/novalidate-cert}INBOX", "orfeo", "%gabriela%")
     or die("can't connect: " . imap_last_error());
echo "OK";
?>
