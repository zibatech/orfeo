<link href="./estilos/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="cuentacobro/css/font-awesome.min.css" type="text/css" />
<?php 

$directorio = 'formatos';
$ficheros  = scandir($directorio);
$ficheros = array_slice($ficheros, 2);

if (empty($ficheros)) {
    ?>
    <div class="alert alert-warning"> No existen archivos para listar en el directorio <b>"formatos"</b> </div>
    <?php exit;
}
 
#echo "<pre>";
#print_r($ficheros);
#echo "</pre>";

?>
<div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
			<table class="table">
			    <thead>
			      <tr>
			        <th>Listado de Formatos para la entidad</th>
			      </tr>
			    </thead>
			    <tbody>
			    <?php foreach ($ficheros as &$archivo) { ?>
					<tr><td class ="download" ><a href="formatos/<?=$archivo?>"> <i class="fa fa-download" aria-hidden="true"></i>  <?=$archivo?></a> </td></tr>
				<?php } ?>
			    </tbody>
			  </table>
			</div>
        <div class="col-sm-2"></div>
      </div>
<?php 

 ?>
 <style type="text/css">
 	.download{
 		cursor: pointer;
 	}
 	.download:hover{
 		background-color: #ACB5F1;
 		color:white;
 	}
 	.download a {
    	display: block;
	}

	/* unvisited link */
	a:link {
	    color: black;
	}

	/* visited link */
	a:visited {
	    color: black;
	}

	/* mouse over link */
	a:hover {
	    color: black;
	}

	/* selected link */
	a:active {
	    color: black;
	}

 </style>