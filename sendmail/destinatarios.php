<?php 

if (($fichero = fopen("destinatarios.csv", "r")) !== FALSE) {
    while (($datos = fgetcsv($fichero, 1000)) !== FALSE) {
        // Procesar los datos.
        // En $datos[0] está el valor del primer campo,
        // en $datos[1] está el valor del segundo campo, etc...
echo $datos[0];//NOMBRE DEL ARCHIVO
echo "-";
//echo $datos[1];//LOCALIDAD
//echo "-";
echo $datos[2]; //NOMBRE
/*echo "-";
echo $datos[3];//IMPRESION
echo "-";
echo $datos[4];//TIRAJE
echo "-";
echo $datos[5];//TAMAÑO
echo "-";
echo $datos[6];//ISSN
echo "-";
echo $datos[7];//DIRECCION
echo "-";
echo $datos[8];//TELEFONO
echo "-";*/
echo "(";
echo $datos[9];//CORREO 1
echo ")";
echo "-";
echo $datos[10]; //CORREO 2
//echo "-";
//echo $datos[11]; //CORREO 3
//echo "-";
echo $datos[12]; //REPRESENTANTE Y/O DIRECTOR
//echo "-";
//echo $datos[13];//CELULAR
//echo "-";
//echo $datos[14];//CREADO

echo "<br>";
    }
}

?>
