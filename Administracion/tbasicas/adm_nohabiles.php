<?php
/**
* @module Dias habiles 2021
*
* @author hardy Deimont Niño   <hdeimont@gmail.com>
* @license  GNU AFFERO GENERAL PUBLIC LICENSE
* @copyright

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
date_default_timezone_set('America/Bogota');
session_start();
if (!$_SESSION['dependencia']) {
    header("Location: $ruta_raiz/cerrar_session.php");
}

foreach ($_GET as $key => $valor) {
    ${$key} = $valor;
}

foreach ($_POST as $key => $valor) {
    ${$key} = $valor;
}

$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
$id_rol = $_SESSION["id_rol"];
$ruta_raiz = "../../";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$scripturl = 'rest-adm.php';
//inicializa  dependecias
$tituloPage = 'Administración Dias No Habiles';
$ano = $ano ? $ano : date('Y');
?>
<html>

<head>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $ruta_raiz; ?>/estilos/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" media="screen"
        href="<?php echo $ruta_raiz; ?>/estilos/smartadmin-production.css">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $ruta_raiz; ?>/estilos/smartadmin-skins.css">
    <link href="<?=$ruta_raiz?>/estilos/bootstrap4.min.css" rel="stylesheet">

    <style type="text/css">
    dataPres .td {
        font-family: Verdana, Arial, Helvetica, sans-serif;
        font-size: 10px;
        font-weight: bolder;
        color: #069;
        text-decoration: none;
    }

    #lista {
        width: 200px;
    }
    </style>
</head>

<body >

    <div class="col-12 pt-4">
        <section id="widget-grid">
            <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="false">
                <header>
                    <h2 id="nomReport"><?php echo $tituloPage; ?></h2>
                </header>


                <!-- widget content -->
                <div class="widget-body" style='min-height: 40px;padding-bottom:0px'>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="inputGroupSelect01">Año</label>
                        </div>
                        <form action='adm_nohabiles.php' style='margin:0px'>
                        <select id='ano' name='ano' class="custom-select" onchange='cambio()' style='max-width: 100px;' onchange="yearcarge()">
                            <?php
for ($index = (date('Y') + 1); $index > 2019; $index--) {
    $select = $index == $ano ? ' selected="selected" ' : '';
    echo "<option $select value='$index'>$index</option>";
}
?>
                        </select>
                        </form>
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="inputGroupSelect01">Sábado</label>
                        </div>
                        <div class="input-group-append">

                            <a href="#2" class='btn btn-outline-danger' onclick="habMas('sabado', 'addFmas');">No
                                Habil</a>
                            <a href="#2" class='btn btn-outline-success'
                                onclick="habMas('sabado', 'delFmas');">Habil</a>
                        </div>
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="inputGroupSelect01">Domingo</label>
                        </div>
                        <div class="input-group-append">

                            <a href="#2" class='btn btn-outline-danger' onclick="habMas('domingo', 'addFmas');">No
                                Habil</a>
                            <a href="#2" class='btn btn-outline-success'
                                onclick="habMas('domingo', 'delFmas');">Habil</a>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
    <div class="col-12">
        <section id="widget-grid">
            <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="false">
                <header>
                    <h2 id="nomReport">Calendario</h2>
                </header>


                <!-- widget content -->
                <div class="widget-body" id="listados">
                    <div class="form-inline">
                        <?php
for ($i = 1; $i <= 12; $i++) {
    echo "<div class='col-3 ' style='height:263px'>";
    calendario($ano, $i);
    echo "</div>";
}
?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>

<script language="JavaScript" src="<?=$ruta_raiz?>/js/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="<?=$ruta_raiz?>/js/axios.min.js"></script>
<script type="text/javascript">

function addfech(fecha, div, tdstyle) {
    axios({
            method: 'post',
            baseURL: 'rest-adm.php',
            data: 'fn=add&fecha=' + fecha
        })
        .then(function(response) {
            document.getElementById(tdstyle).style.backgroundColor = "#d9534f";
            dtfecga = fecha.replace('-', '').replace('-', '');           
            $('#A' + dtfecga).removeClass('btn-success');
            $('#A' + dtfecga).addClass('btn-danger');
            $('#A' + dtfecga).attr('onclick', "adddel('" + fecha + "','div" + dtfecga + "','td" + dtfecga + "')");
            
        })
        .catch(function(error) {
            //  $('#animationload').hide();
            if (error.hasOwnProperty('response') && Object.keys(error.response).length > 0) {
                $(this).showError('Error en petición', 'Estado del error: ' + error.response.status +
                    '. Mensaje: ' + error.response.data.error);
            }
            //toastr.error(data.message, 'Error al Modificar ');
        });
}

function adddel(fecha, div, tdstyle) {
    axios({
            method: 'post',
            baseURL: 'rest-adm.php',
            data: 'fn=del&fecha=' + fecha
        })
        .then(function(response) {
            
            document.getElementById(tdstyle).style.backgroundColor = "#4cae4c";
            dtfecga = fecha.replace('-', '').replace('-', '');
            $('#A' + dtfecga).removeClass('btn-danger');
            $('#A' + dtfecga).addClass('btn-success');
            $('#A' + dtfecga).attr('onclick', "addfech('" + fecha + "','div" + dtfecga + "','td" + dtfecga + "')");

        })
        .catch(function(error) {
            //   $('#animationload').hide();
            if (error.hasOwnProperty('response') && Object.keys(error.response).length > 0) {
                $(this).showError('Error en petición', 'Estado del error: ' + error.response.status +
                    '. Mensaje: ' + error.response.data.error);
            }
            //toastr.error(data.message, 'Error al Modificar ');
        });

}

function cambio(){
    $( "form" ).submit();
    
}

function habMas(nomb, action) {
    var data;
    var dato = '';
    var fano = document.getElementById('ano').value;
    for (var i = 1; i <= 12; i++) {
        data = document.getElementById(nomb + i).value;
        dato = dato + data;
    }
    /*var poststr = "action=" + action + "&ano=" + fano + "&datos=" + dato;
    partes('<?php echo $scripturl; ?>', 'listados', poststr, '');*/
    axios({
            method: 'post',
            baseURL: 'rest-adm.php',
            data: 'fn='+action+ "&ano=" + fano + "&datos=" + dato
        })
        .then(function(response) {
            
          //  document.getElementById(tdstyle).style.backgroundColor = "#4cae4c";
            $( "form" ).submit();

        })
        .catch(function(error) {
            //   $('#animationload').hide();
            if (error.hasOwnProperty('response') && Object.keys(error.response).length > 0) {
                $(this).showError('Error en petición', 'Estado del error: ' + error.response.status +
                    '. Mensaje: ' + error.response.data.error);
            }
            //toastr.error(data.message, 'Error al Modificar ');
        });
}
</script>

</html>
<?php
function calendario($ano, $mes)
{
    $dateVal = consultar($ano, $mes);
    //print_r($dateVal);
    //echo "hs";

    $MESCOMPLETO = array(1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre');
    $MESABREVIADO = array(1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic');
    $SEMANACOMPLETA = array(0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado');
    $SEMANAABREVIADA = array(0 => 'Dom', 1 => 'Lun', 2 => 'Mar', 3 => 'Mie', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb');

    $tipo_semana = 1;
    $tipo_mes = 1;
    if ($tipo_semana == 0) {
        $ARRDIASSEMANA = $SEMANACOMPLETA;
    } elseif ($tipo_semana == 1) {
        $ARRDIASSEMANA = $SEMANAABREVIADA;
    }
    if ($tipo_mes == 0) {
        $ARRMES = $MESCOMPLETO;
    } elseif ($tipo_mes == 1) {
        $ARRMES = $MESABREVIADO;
    }
    $TotalDiasMes = date(t, mktime(0, 0, 0, $mes, $dia, $ano));
    $DiaSemanaEmpiezaMes = date(w, mktime(0, 0, 0, $mes, 1, $ano));
    $DiaSemanaTerminaMes = date(w, mktime(0, 0, 0, $mes, $TotalDiasMes, $ano));
    $EmpiezaMesCalOffset = $DiaSemanaEmpiezaMes;
    $TerminaMesCalOffset = 6 - $DiaSemanaTerminaMes;
    $TotalDeCeldas = $TotalDiasMes + $DiaSemanaEmpiezaMes + $TerminaMesCalOffset;

    if ($mes == 1) {
        $MesAnterior = 12;
        $MesSiguiente = $mes + 1;
        $AnoAnterior = $ano - 1;
        $AnoSiguiente = $ano;
    } elseif ($mes == 12) {
        $MesAnterior = $mes - 1;
        $MesSiguiente = 1;
        $AnoAnterior = $ano;
        $AnoSiguiente = $ano + 1;
    } else {
        $MesAnterior = $mes - 1;
        $MesSiguiente = $mes + 1;
        $AnoAnterior = $ano;
        $AnoSiguiente = $ano;
        $AnoAnteriorAno = $ano - 1;
        $AnoSiguienteAno = $ano + 1;
    }
    /*** pinta el cmienzo del mes */
    print "<table  class=\"table-bordered table-striped\"  ><tr><td colspan=10 style=' text-align: center;'>";
    print " <b>" . $ARRMES[$mes] . " - $ano</b></td></tr>";
    /*** pinta la semana */
    print "<tr>";
    foreach ($ARRDIASSEMANA as $key) {
        print "<td  style='background-color: black;color:#fff'><b>$key</b></td>";
    }
    print "</tr> ";

    for ($a = 1; $a <= $TotalDeCeldas; $a++) {
        if (!$b) {
            $b = 0;
        }

        if ($b == 7) {
            $b = 0;
        }

        if ($b == 0) {
            print '<tr>  ';
        }

        if (!$c) {
            $c = 1;
        }

        if ($a > $EmpiezaMesCalOffset and $c <= UltimoDia($ano, $mes)) {
            $mes2 = $mes;
            $c2 = $c;
            if ($mes < 10) {
                $mes2 = '0' . $mes;
            }
            if ($c < 10) {
                $c2 = '0' . $c;
            }
            if ($dateVal[$c]) {
                print "<td style='background-color: #d9534f' id='td$ano$mes2$c2'><div id='div$ano$mes2$c2' style='margin: 0px auto'><a id='A$ano$mes2$c2' class='btn btn-danger btn-sm' style='width: 100%;' href='#2' onclick='adddel(\"$ano-$mes2-$c2\",\"div$ano$mes2$c2\",\"td$ano$mes2$c2\")'>$c </a> </td>";
            } else {
                print "<td style='background-color:  #4cae4c' id='td$ano$mes2$c2'><div id='div$ano$mes2$c2'  style='margin: 0px auto'><a id='A$ano$mes2$c2' class='btn btn-success btn-sm'  style='width: 100%;' href='#2' onclick='addfech(\"$ano-$mes2-$c2\",\"div$ano$mes2$c2\",\"td$ano$mes2$c2\")'>$c</a> </div></td>";
            }
            if ($b == 6) {
                $diaSabado .= "$ano-$mes-$c;";
            }
            if ($b == 0) {
                $diaDomingo .= "$ano-$mes-$c;";
            }
            $c++;
        } else {
            print "<td></td>";
        }
        if ($b == 6) {
            print '</tr>';
        }

        $b++;
    }
    //       print "<tr><td align=center colspan=10></a></td></tr>";
    print "</table><input type='hidden' id='sabado$mes' value='$diaSabado'><input type='hidden' id='domingo$mes' value='$diaDomingo'>";
}

function UltimoDia($anho, $mes)
{
    if (((fmod($anho, 4) == 0) and (fmod($anho, 100) != 0)) or (fmod($anho, 400) == 0)) {
        $dias_febrero = 29;
    } else {
        $dias_febrero = 28;
    }
    switch ($mes) {
        case 1:return 31;
            break;
        case 2:return $dias_febrero;
            break;
        case 3:return 31;
            break;
        case 4:return 30;
            break;
        case 5:return 31;
            break;
        case 6:return 30;
            break;
        case 7:return 31;
            break;
        case 8:return 31;
            break;
        case 9:return 30;
            break;
        case 10:return 31;
            break;
        case 11:return 30;
            break;
        case 12:return 31;
            break;
    }
}
function consultar($ano, $mes)
{

    $db = new ConnectionHandler("../../");

    if ($mes < 10) {
        $mes = '0' . $mes;
    }
    $query = "select to_char(noh_fecha,'DD') as dia ,to_char(noh_fecha,'MM') as mes,to_char(noh_fecha,'YYYY') as ano from sgd_noh_nohabiles where  to_char(noh_fecha,'MM')='$mes' and to_char(noh_fecha,'YYYY')='$ano' ";
    $rs = $db->query($query);

    $i = 0;
    while (!$rs->EOF) {
        $mes = $rs->fields['MES'];
        $dia = $rs->fields['DIA'];
        $dia2 = $dia + 0;
        $combi[$dia2] = $dia2;

        $i++;
        $rs->MoveNext();
    }
    $db->conn->Disconnect();
    return $combi;
}
?>