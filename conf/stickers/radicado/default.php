<?$ruta_raiz="../.."?> <html>

<title>Sticker web</title>
<link rel="stylesheet" href="estilo_imprimir.css" TYPE="text/css" MEDIA="print">
<style type="text/css">

body {
    margin-bottom:0;
    margin-left:0;
    margin-right:0;
    margin-top:0;
    padding-bottom:0;
    padding-left:0;
    padding-right:0;
    padding-top:0
    font-family: Arial, Helvetica, sans-serif;
}

.flex-container {
  padding: 0;
  margin: 0;
  list-style: none;
  -ms-box-orient: horizontal;
  display: -webkit-box;
  display: -moz-box;
  display: -ms-flexbox;
  display: -moz-flex;
  display: -webkit-flex;
  display: flex;
}

.wrap{
  -webkit-flex-wrap: wrap;
  flex-wrap: wrap;
}

.flex-item {
  text-align: center;
  margin-left: 0.5rem;
}
</style>
</head>

<body marginheight="0" marginwidth="0">
<br>
<table border="0">
  <tr>
    <td rowspan="2">
      <ul class="flex-container wrap">
           <li class="flex-item">
          <img width="95" src="../../estilos/images/logo.png" alt="logo" />
        </li>
      </ul>
  </td>
    
  <td valign="top">  
 <ul class="flex-container wrap">
    <li class="flex-item">
    <?=$noRadBarras?>
    </li>
 </ul>
  </td>
  </tr>
  <tr>
    <td>
      <ul class="flex-container wrap">
              <li class="flex-item">
          <font face="Arial" size="3">
              Al contestar cite este número:
          </font>
        </li>
        <li class="flex-item">
        <font face="Arial" size="4"><?=$noRad?></font>
        </li>
        <li class="flex-item">
        <font face="Arial" size="4"><?=substr($radi_fech_radi,0,17)?></font>
        </li>
        <?php
          if(empty($dirLogo)){
              echo "
              <li class='flex-item'>
                 <font face='Arial' size='3'><?=$entidad_corto?></font>
              </li> ";
          }

          if(!empty($anexos)){
              echo "<li class='flex-item'><font face='Arial' size='3'>Anexos".$anexos.".</font></li>";
          }

          if(!empty($folios)){
              echo "<li class='flex-item'><font face='Arial' size='3'>Folios:".$folios.".</font></li>";
          }
      ?>
      <li class="flex-item">
        <font face="Arial" size="3">Origen: <?=$remitente?>.</font>
      </li>
</ul>
    </td>
  </tr>
</table>



</body>
</html>
