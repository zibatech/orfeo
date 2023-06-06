<?php
session_start();
include_once 'masiva.php';

foreach ($_GET  as $key => $val){ ${$key} = $val;}
foreach ($_POST as $key => $val){ ${$key} = $val;}

if(!isset($_SESSION['dependencia'])) include "$ruta_raiz/rec_session.php";

if(isset($_FILES['listado'])) {
    $errors= array();
    $file_name = $_FILES['listado']['name'];
    $file_size = $_FILES['listado']['size'];
    $file_tmp = $_FILES['listado']['tmp_name'];
    $file_type = $_FILES['listado']['type'];
    $file_ext = strtolower(end(explode('.',$_FILES['listado']['name'])));

    $extensions= array("csv");
      
    if(in_array($file_ext,$extensions)=== false){
       $errors[]="Por favor cargue un archivo csv";
    }

    if(empty($errors)){
        unlink($upload_dir.'masiva.txt');
        move_uploaded_file($file_tmp, $upload_dir.'masiva.txt');
        $file = fopen($upload_dir.'masiva.txt', "r") or die("No se pudo abrir el archivo!");
        $i = 0;
        $cols = [];
        $radicados = [];
        $registro = null;
        $size = 0;
        if ($file) {
            while (($line = fgets($file)) !== false) {
                if($i == 0)
                {
                    $cols = explode(";", $line);
                    $size = count($cols);
                } else {
                    $values = explode(";", $line);
                    if(count($values) == $size)
                    {
                        $registro = array_combine(array_map('trim', $cols), $values);
                        $radicados[$i] = Masiva::radicar($registro);
                    }
                }
                $i++;
            }        
            fclose($file);
        }
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
    <script src="../../include/ckeditor/ckeditor.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <pre>
                    <?php var_dump($radicados) ?>
                </pre>
            </div>
        </div>
    </div>
</body>
</html>