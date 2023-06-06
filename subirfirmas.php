<?
#echo "subi firmas"; exit;
$lim_tam = "1024000";

$image_firma_name = $_FILES['file1']['name'];
$image_firma_size = $_FILES['file1']['size'];
$image_firma_type = $_FILES['file1']['type'];
$iimage_firma_upload = $_FILES['file1']['tmp_name'];


$p12_firma_name = $_FILES['file2']['name'];
$p12_firma_size = $_FILES['file2']['size'];
$p12_firma_type = $_FILES['file2']['type'];
$p12_firma_upload = $_FILES['file2']['tmp_name'];



if($_FILES['file1']['error']==1){
print "
<script>
alert('El Archivo supera el límite de tamaño, por favor seleccione un archivo diferente.')

</script>
";
}else if($file1_size>$lim_tam){
print "
<script>
alert('El Archivo supera el límite de tamaño, por favor seleccione un archivo diferente.')

</script>
";
} 
else {
$bodega_firmas=$ruta_raiz.'/bodega/firmas/';
$uriFile1=$bodega_firmas.$usua_doc;
$uriFile2=$bodega_firmas.$usua_doc.'.p12';
$record[0]=553;
if (move_uploaded_file($_FILES['file1']['tmp_name'],$uriFile1)) {
	$record[1]=$uriFile1;
	$db->insert('usuf_firma',$record);
    echo "El archivo es válido y fue cargado exitosamente.\n";
} else {
	$record[1]="";
}
if (move_uploaded_file($_FILES['file2']['tmp_name'],$uriFile2)) {
	$record[2]=$uriFile2;
    echo "El archivo es válido y fue cargado exitosamente.\n";
} else {
	$record[2]="";
}
//*******Falta definir la capa de persistencia********//
//$db->conn->debug=true;
//echo $isql="insert into usuf_firma values (id,usuf_archivop12,usuf_archivofirma) set ('$record[0]','$record[1]','$record[2]')";
//$db->query($isql);
//****************************************************//
}
?>
