<?php
/**
 * @author JOHANS GONZALEZ MONTERO 
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyright
*/

require_once '../tcpdf/tcpdf.php';

class ConstanciaPDF extends TCPDF {

    public function Header() {

        $tbl = '
        <table border="1" >
            <tr align="center">
            <td rowspan="2" >
              <div style="vertical-align: middle;">
                    <br><img src="../bodega/sys_img/logosupersalud.png" width="80px" />
              </div>  
            </td>
            <td align="center">
                  <div style="vertical-align: middle;">
                      <b>PROCESO:</b>
                  </div>
              </td>
              <td align="center">
                  <div style="vertical-align: middle;">
                        ADMINISTRACIÓN DE LA GESTIÓN DOCUMENTAL
                  </div>
              </td>
              <td align="center">
                  <div style="vertical-align: middle;">
                      <b>CÓDIGO:</b>
                  </div>
              </td>
              <td align="center">
                  <div style="vertical-align: middle;">
                      GDFL09
                  </div>
              </td>
            </tr>
            <tr align="center">
              <td align="center">
                  <div style="vertical-align: middle;">
                      <b>FORMATO:</b>
                  </div>
              </td>
              <td align="center">
                  <div style="vertical-align: middle;">
                        CONSTANCIA DE EJECUTORIA ACTO ADMINISTRATIVO
                  </div>
              </td>
              <td align="center">
                  <div style="vertical-align: middle;">
                      <b>VERSIÓN:</b>
                  </div>
              </td>
              <td align="center">
                  <div style="vertical-align: middle;">
                      1
                  </div>
              </td>
            </tr>                 
        </table><br><br><br>';
        $this->SetY(10);
        $this->SetFont ('helvetica', '', 10 , '', 'default', true );
        $this->writeHTML($tbl, true, false, false, false, '');

    }

    public function Footer() {

        $this->SetFont('helvetica', '', 8);
        $tbl = '
            <p align="center"><b>Carrera 68A N°. 24B – 10, Torre 3, Piso 4, 9 y 10. Edificio Plaza Claro, Bogotá PBX: (57)-(1) 744 2000. Fax: (57)-(1) 744 2000
            <br>Opción 4. Bogotá, Colombia.</b><br>
            www.supersalud.gov.co.';
        $this->SetY(-20);
        $this->writeHTML($tbl, true, false, false, false, '');

    }

}

?>