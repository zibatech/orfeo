<?php
/**
 * This class starts up everything for Travis.
 */
 
// require_once "autoload.php";
 
require_once "ImapClient/ImapClientException.php";
require_once "ImapClient/ImapConnect.php";
require_once "ImapClient/ImapClient.php";
require_once "ImapClient/Section.php";
require_once "ImapClient/Helper.php";
require_once "ImapClient/IncomingMessage.php";
require_once "ImapClient/IncomingMessageAttachment.php";
require_once "ImapClient/TypeAttachments.php";
require_once "ImapClient/TypeBody.php";
require_once "ImapClient/SubtypeBody.php";
require_once "ImapClient/OutgoingMessage.php";
require_once "ImapClient/AdapterForOutgoingMessage.php";




use SSilence\ImapClient\Helper;
use SSilence\ImapClient\ImapClientException;
use SSilence\ImapClient\ImapConnect;
use SSilence\ImapClient\ImapClient as Imap;use SSilence\ImapClient\OutgoingMessage;
use SSilence\ImapClient\AdapterForOutgoingMessage;
use SSilence\ImapClient\IncomingMessage;
use SSilence\ImapClient\IncomingMessageAttachment;
use SSilence\ImapClient\Section;
use SSilence\ImapClient\SubtypeBody;
use SSilence\ImapClient\TypeAttachments;
use SSilence\ImapClient\TypeBody;


//$imap = new ImapClient();

$hostname = 'imap.gmail.com:993';
$mailbox = $hostname;
$username = 'jlosada@gmail.com';
$password = 'JairoH798';
$encryption = Imap::ENCRYPT_SSL; // TLS OR NULL accepted

// Open connection
try{
    $imap = new Imap($mailbox, $username, $password, $encryption);
    // You can also check out example-connect.php for more connection options

}catch (ImapClientException $error){
    echo $error->getMessage().PHP_EOL; // You know the rule, no errors in production ...
    die(); // Oh no :( we failed
}

$folders = $imap->getFolders();
foreach($folders as $folder) {
    //echo "<br>".$folder;
    //var_dump($folder);
}

echo "// Select the folder INBOX";
$imap->selectFolder('INBOX');
echo ":fin Select Folder <br>";
// Count the messages in current folder
$overallMessages = $imap->countMessages();

echo "<br>Numero de Mails: ". $overallMessages."<br>";
$unreadMessages = $imap->countUnreadMessages();
echo "<br>Numero de Mails Sin Leer: ". $unreadMessages."<br>";

// Fetch all the messages in the current folder
//$emails = $imap->getMessages (20);
//$uids   = $imap->getMessages(20);
//var_dump($uids);
//echo "<br> Mails...";
//var_dump($emails);
//echo "<br>";

//include_once "decodeBody.php";

//$emails = $imap->getId (232847);
//var_dump($emails);


$emails = $imap->getMessages(5);
var_dump($emails);


?>