<?php

/****
Procedimiento creado para paginar con adodb limit una consulta existente .
ing. Cesar Buelvas
05/05/2015

Motivo : Al realizar diferentes migraciones en reportes de orfeo 3.8 a siim
las clases del adodb, requeridas para el buen funcionamiento de la paginacion
dejaron de funcionar. este pequeño procedimiento no limita su funcionamiento.
***/

//Para poder contar las filas
 $ADODB_COUNTRECS = true;
 $rs = $db->conn->Execute($isql);
 if ($rs){
 $num_total_registros = $rs->RecordCount();
 }else{ die ("error");}
 
//NUMERO DE REGISTROS POR PAGINA 
 if($TAMANO_PAGINA <= 0){
//Limito la busqueda
 $TAMANO_PAGINA = 100;
 }
 
 //examino la página a mostrar y el inicio del registro a mostrar
 $pagina = $_GET["pagina"];
 if (!$pagina) {
    $inicio = 0;
    $pagina = 1;
 }
 else {
    $inicio = ($pagina - 1) * $TAMANO_PAGINA;
 }
 //calculo el total de páginas
 $total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);
 
 //OBTENEMOS LOS TITULOS
 $rs_title = $db->conn->SelectLimit($isql,0,1);
 $a_title =  explode(",", $rs_title); //Array en donde guardo los titulos
 $n_title = count($a_title); //Cuento cuantos titulos o columnas tiene
 ?>

 <table class="table table-bordered table-striped mart-form" align="center" border="2" width="80%">
      <tbody>
          <tr>
 <?php
      //recorro los titulos y columnas
         for ($i = 0; $i < $n_title; $i++) {
                 echo "<th class='titulos2' align='center'> $a_title[$i] </th>";
         }
 ?>
        </tr>
 <?php
$rs = $db->conn->SelectLimit($isql,$TAMANO_PAGINA,$inicio);
 //LLENO EL CONTENIDO
    while(!$rs->EOF){
         echo "<tr>";
         //Por cada registro, recorro sus datos
         for ($i = 0; $i < $n_title; $i++) {
              echo '<td class="listado2" align="center">'.$rs->fields["$a_title[$i]"].'</td>';
         }
         echo "</tr>";
      $rs->MoveNext();
    }
 ?>
     </tbody>
 </table>
 <?
 //MUESTRO LA PAGINACION
 echo ' <div align="center">';
 if ($total_paginas > 1) {
    if ($pagina != 1){echo '<a href="'.$url.'?pagina='.($pagina-1).'"> << Anterior  </a>';}
 
       for ($i=1;$i<=$total_paginas;$i++) {
          if ($pagina == $i)
             //si muestro el índice de la página actual, no coloco enlace
          echo $pagina;
       else
          //si el índice no corresponde con la página mostrada actualmente,
          //coloco el enlace para ir a esa página
          echo '  <a href="'.$url.'?pagina='.$i.'">'.$i.'</a>  ';
    }
if ($pagina != $total_paginas){echo '<a href="'.$url.'?pagina='.($pagina+1).'"> Siguiente >> </a>';}
      //echo '  <a href="'.$url.'?pagina='.$total_paginas.'"> Ultimo </a>  ';
 echo "</div> ";
}
?>
