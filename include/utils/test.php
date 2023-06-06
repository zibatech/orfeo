<?php
 include_once('Utils.php');
 $validacionUsuario    = Utils::checkldapuser('orfeo', '%gabriela');
 var_dump( $validacionUsuario);
?>
