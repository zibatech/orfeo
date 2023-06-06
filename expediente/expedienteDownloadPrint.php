<?php
session_start();
date_default_timezone_set("America/Bogota");
//var_dump($_SESSION);
$ruta_raiz = "..";
?>
<HTML>
<header>

<?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
</header>
<BODY>
<p>

</p>

<?php
setlocale(LC_TIME, "es_CO");
setlocale(LC_TIME, 'es_ES.UTF-8');
echo strftime("%A, %d de %B de %Y");
?>
<p>
Resultado de exportacion del expediente No. <?=$numExpediente?>.  Archivos que se entregan en un archivo zip.
<p>
<center>
<small>
<table border="0" width="80%">
<?PHP
   echo $_SESSION["tableInformeRadicados"]; 
?>
</table>
</small>
<BR><BR>
</center>
<P>
Att,

<BR><BR><BR><BR>

<B>COMISION NACIONAL DEL SERVICIO CIVIL</B>
</P>

</BODY>
</HTML>