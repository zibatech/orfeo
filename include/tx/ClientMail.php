<?php
include_once($ruta_raiz."/include/tx/Historico.php");
include_once($ruta_raiz."/include/simpleHtmlDom/simple_html_dom.php");


class ClientMail extends simple_html_dom {
  /** Aggregations: */

  /** Compositions: */

   /*** Attributes: ***/
	 /**
   * Clase que maneja los Historicos de los documentos
   *
   * @param int     Dependencia Dependencia de Territorial que Anula
   * @param number  usuaDocB    Documento de Usuario
   * @param number  depeCodiB   Dependencia de Usuario Buscado
   * @param varchar usuaNombB   Nombre de Usuario Buscado
   * @param varcahr usuaLogin   Login de Usuario Buscado
   * @param number	usNivelB	Nivel de un Ususairo Buscado..
   * @db 	Objeto  conexion
   * @access public
   */
  var $date;
  var $Date;
  var $subject;
  var $Subject;
  var $message_id;
  var $toaddress;
  var $to;
  var $fromaddress;
  var $from;
  var $reply_toaddress;
  var $reply_to;
  var $senderaddress;
  var $sender;
  var $textMail;
 
  function __construct($db)
 {
    /**
  * Constructor de la clase ClientMail
  * @db variable en la cual se recibe el cursor sobre el cual se esta trabajando.
  *
  */
  $this->db=$db;
 }
 function ClientMail($db)
 {
    /**
  * Constructor de la 
	* @db variable en la cual se recibe el cursor sobre el cual se esta trabajando.
	**/

	$this->db=$db;
 }
 /**
  * Metodo que trae los datos principales de un usuario a partir del codigo y la dependencia
  *
  * @param number $codUsuario
  * @param number $depeCodi
  *
  */
 function saveDbMail($uid, $usMailBox=null){
 	// id, msgno, asunto, desde, para, fecha, uniqueid, radi_nume_radi,           id_direcciones, buzon
 	$record["MSGNO"]   = $uid;
  $record["DATE_MAIL"] = $this->date; 
  if(!$this->date)$record["DATE"] = $this->Date; 
  $record["SUBJECT"] = $this->subject; 
  if(!$this->subject) $record["SUBJECT"] = $this->Subject; 
  $record["MESSAGE_ID"] = $this->message_id; 
  $record["TOADDRESS_JSON"] = json_encode($this->to); 
  $record["TOADDRESS"] = $this->toaddress; 
  $record["FROMADDRESS_JSON"] = json_encode($this->from); 
  $record["FROMADDRESS"] = $this->fromaddress; 
  $record["REPLY_TOADDRESS_JSON"] = json_encode($this->reply_to); 
  $record["REPLY_TOADDRESS"] = $this->reply_toaddress; 
  $record["SENDERADDRESS"] = $this->senderaddress; 
  if($this->radiNume) $record["RADI_NUME_RADI"] = $this->radiNume;
  //$record["ID_DIRECCIONES"]   = $this->idDirecciones;
  $record["BUZON"]   = $usMailBox;

  $rsInsert = $this->db->conn->replace("radimail", $record, array('MSGNO', 'BUZON'), true); 
  //Posibles retornos returns 0 = fail, 1 = update, 2 = insvar $Date; 
  
      switch ($rsInsert) {
      case 0:
        $rsInsert = -1;
        break;
      case 1:
        break;
      case 2:

        break;
        
      }
  return $rsInsert;
  // 0 on failure, 1 if update statement worked, 2 if no record
 }
      
 /* Metodo saveLinkImgMail
  * Funcion que graba imagenes como links que llegan de Mails. 
  * ej.  <img src="http://google.com/arraw.png">
  *
  */
 function saveLinkImgMail($ruta_raiz, $uid=null){
    
    // this note is about how to get a DOMNode's outerHTML and innerHTML.
   // $dom = new DOMDocument('1.0','UTF-8');
    $textHtml = $this->textMail;
    $html = new simple_html_dom();
    $html->load($textHtml);
    $i = 1;
    
    //$url = "https://cnscgov-my.sharepoint.com/personal/admin_cnscgov_onmicrosoft_com/_layouts/15/guestaccess.aspx?docid=1751ee5fd1df64044afb4f530733b5bba&amp;authkey=Afp_k_A6Itl8_00xB4JPcGw";
    //$imgSrc = "https://cnscgov-my.sharepoint.com/personal/admin_cnscgov_onmicrosoft_com/_layouts/15/guestaccess.aspx?docid=1751ee5fd1df64044afb4f530733b5bba&authkey=Afp_k_A6Itl8_00xB4JPcGw";
      
    //$fila= $this->file_get_html($url);
    //echo $fila;
    
    foreach($html->find('img') as $link){
      
      $imgSrc = $link->src;
      if(!strpos($link->src, '.aspx')  and $link->src  ){
        
         if(strtolower(substr($link->src, 0,3)) == "cid") {
          // Si la imagen es una imagen embebida y viene adjunta en el mismo correo.   
            echo "<hr>";           
            $imgII = str_replace("cid:","",$link->src);
            foreach($this->objMail->getAttachments() as $attachment){
              if($attachment->contentId==$imgII){
                $linkImg = $attachment->filePath;
                $imagenB = file_get_contents($linkImg);
                $imagenMimeType = mime_content_type($linkImg);
                // No colocamos la ultima comilla ya que internamente la cerramos para colocar el nuevo tag htmLOriginal.
                // En el tag httpOriginal guardamos el link original de existencia de la Imagen si se llega a requerir en un futuro para algo.
                $imagenB = "data:".$imagenMimeType.";base64,".base64_encode($imagenB).'" httpOriginal="'.$attachment->contentId;                   
              }  
                
            }
            
         }else{  
           // Si la imagen es una imagen embebida y la Imagen viene como Link en el correo.  Es necesario descargarla ya que estos link pueden desaparecer en el Tiempo.
         $ext = array_pop(explode(".", $link->src));
         $nameImg = $uid."_ilink_".$i.".".$ext;
         $pathImg = $_SESSION["CONTENT_PATH"]. "tmp/radimail/imgs/";
         //$nameImg = $uid."_ilink_".str_replace("/", "&#47;",$imgSrc);
         // No se guarda la imagen ya que se graba en el html embebida.
         $this->save_img($imgSrc, $nameImg, $pathImg);
         $imagenB = file_get_contents($pathImg.$nameImg);
         $imagenMimeType = mime_content_type($pathImg.$nameImg);
         // No colocamos la ultima comilla ya que internamente la cerramos para colocar el nuevo tag htmLOriginal.
         // En el tag httpOriginal guardamos el link original de existencia de la Imagen si se llega a requerir en un futuro para algo.
         $imagenB = "data:".$imagenMimeType.";base64,".base64_encode($imagenB).'" httpOriginal="'.$imgSrc;   
         $i++;
         $textHtml = str_replace($link->src, $imagenB, $textHtml );
         }
      }
    }
      
    $this->textHtml = $textHtml;

    // Grabar el correo electrónico cno las imagenes embebidas.
    $nameFileMail = $_SESSION["CONTENT_PATH"]."/tmp/radimail/".$uid.".html";
    $fp = fopen($nameFileMail, 'w');
      fwrite($fp, $textHtml);
    fclose($fp);
    
    
    //$result = $html->save();
    //var_dump($node->childNodes);
    
 }
 /** getVarHeader
   * Metodo que trae las variable de la cabecera de cada correo electronico identificado por un UID y su respectivo Mail del Usuario.
   */
 function getVarHeader(){
   $header = get_object_vars($this->objMail->headers);
   
  $this->date= $header["date"]; 
  $this->Date= $header["Date"]; 
  //$this->subject= $header["subject"]; 
  //$this->Subject= $header["Subject"]; 
  $this->message_id= $header["message_id"]; 
  $this->toaddress= $header["toaddress"]; 
  $this->to= $header["to"]; 
  $this->fromaddress= $header["fromaddress"]; 
  $this->from= $header["from"]; 
  $this->reply_toaddress= $header["reply_toaddress"]; 
  $this->reply_to= $header["reply_to"]; 
  $this->senderaddress= $header["senderaddress"]; 
  $this->subject = $this->objMail->subject; 
   //foreach($header as $var => $value) {
     //$this->$var = $value;
     //echo "<hr> var $".$var.";<br>";
     //echo '<hr>$record["'.strtoupper($var).'"] = $this->'.$var."; <br>" ; 
   //}
   
   //echo "<hr>########################".$this->subject."<hr>***<br>";
   //return $header;
   
 }
 
 function file_get_html($url, $use_include_path = false, $context=null, $offset = -1, $maxLen=-1, $lowercase = true, $forceTagsClosed=true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN=true, $defaultBRText=DEFAULT_BR_TEXT, $defaultSpanText=DEFAULT_SPAN_TEXT)
{
  // We DO force the tags to be terminated.
  $dom = new simple_html_dom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);
  // For sourceforge users: uncomment the next line and comment the retreive_url_contents line 2 lines down if it is not already done.
  $contents = file_get_contents($url, $use_include_path, $context, $offset);
  // Paperg - use our own mechanism for getting the contents as we want to control the timeout.
  //$contents = retrieve_url_contents($url);
  if (empty($contents) || strlen($contents) > MAX_FILE_SIZE)
  {
    return false;
  }
  // The second parameter can force the selectors to all be lowercase.
  $dom->load($contents, $lowercase, $stripRN);
  return $dom;
}

// get html dom from string
function str_get_html($str, $lowercase=true, $forceTagsClosed=true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN=true, $defaultBRText=DEFAULT_BR_TEXT, $defaultSpanText=DEFAULT_SPAN_TEXT)
{
  $dom = new simple_html_dom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);
  if (empty($str) || strlen($str) > MAX_FILE_SIZE)
  {
    $dom->clear();
    return falsave_imgse;
  }
  $dom->load($str, $lowercase, $stripRN);
  return $dom;
}

// dump html dom tree
function dump_html_tree($node, $show_attr=true, $deep=0)
{
  $node->dump($node);
}


function save_img($url, $fileNameNewImg, $path) { 
    $image = $url;
    $img_file = file_get_contents($image); 

    $image_path = parse_url($image); 
    $img_path_parts = pathinfo($image_path['path']); 
     
    $filename = $img_path_parts['filename'];
    $img_ext = $img_path_parts['extension']; 

    $filex =  $path. $fileNameNewImg; 
    $fh = fopen($filex, 'w'); 
    fputs($fh, $img_file); 
    fclose($fh); 
    return filesize($filex); 
}
 

}
?>
