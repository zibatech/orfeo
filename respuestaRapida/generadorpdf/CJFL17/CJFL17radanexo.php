<?php
   class MYPDF extends TCPDF
      {          

          protected $radicadoOrfeo;
          protected $fechaOrfeo;

          function __construct($radicadoOrfeo, $fechaOrfeo) {
             $this->radicadoOrfeo = $radicadoOrfeo;
             $this->fechaOrfeo = $fechaOrfeo;
             parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
          }        

          public function Header()
          {
            
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
                            <br><b>PROCESO:</b>
                        </div>
                    </td>
                    <td align="center">
                        <div style="vertical-align: middle;">
                            COBRO PERSUASIVO Y POR JURISDICCIÓN COACTIVA
                        </div>
                    </td>
                    <td align="center">
                        <div style="vertical-align: middle;">
                            <br><b>CÓDIGO:</b>
                        </div>
                    </td>
                    <td align="center">
                        <div style="vertical-align: middle;">
                            <br>CJFL17
                        </div>
                    </td>
                  </tr>
                  <tr align="center">
                    <td align="center">
                        <div style="vertical-align: middle;">
                            <br><b>FORMATO:</b>
                        </div>
                    </td>
                    <td align="center">
                        <div style="vertical-align: middle;">
                            AUTO QUE ORDENA EL LEVANTAMIENTO DE MEDIDAS CAUTELARES
                        </div>
                    </td>
                    <td align="center">
                        <div style="vertical-align: middle;">
                            <br><b>VERSIÓN:</b>
                        </div>
                    </td>
                    <td align="center">
                        <div style="vertical-align: middle;">
                            <br>1
                        </div>
                    </td>
                  </tr>                 
              </table><br><br>';
              $this->SetY(10);
              $this->SetFont ('helvetica', '', 8 , '', 'default', true );
              $this->writeHTML($tbl, true, false, false, false, '');
              $this->SetFont ('helvetica', 'B', 11 , '', 'default', true );
              $this->Cell(0, 15, 'AUTO ' . $this->radicadoOrfeo . ' DE ' . $this->fechaOrfeo, 0, false, 'C', 0, '', 0, false, 'M', 'M');              

          }

        public function Footer() {         
              // Position at 15 mm from bottom
            $this->SetY(-15);
            // Set font
            $this->SetFont ('helvetica', '', 8 , '', 'default', true );
            // Page number
            $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'M', 'M');                 
        }

      }

            
      $pdf = new MYPDF($numradNofi, $anho);
      $pdf->SetCreator(PDF_CREATOR);
      $pdf->SetAuthor(AUTOR_PDF);
      $pdf->SetTitle(TITULO_PDF);
      $pdf->SetSubject(ASUNTO_PDF);
      $pdf->SetKeywords(KEYWORDS_PDF);
      
      $pdf->SetFont ('helvetica', '', 11 , '', 'default', true );
      $pdf->SetMargins(22, 51, 22);
      $pdf->AddPage();    

     /*$style = array(
        'position' => '',
        'align' => 'C',
        'stretch' => true,
        'fitwidth' => true,
        'cellfitalign' => '',
        'border' => false,
        'hpadding' => 'auto',
        'vpadding' => 'auto',
        'fgcolor' => array(0,0,0),
        'bgcolor' => false, //array(255,255,255),
        'text' => false,
        'font' => 'helvetica',
        'fontsize' => 8,
        'stretchtext' => 4
    );
      $style['position'] = 'R';
      $pdf->write1DBarcode($nurad, 'C39', '', '', '', 7, 0.2, $style, 'N');*/
      $pdf->writeHTML($asu, true, false, true, false, '');
      $pdf->Output($ruta_raiz.$ruta2, 'F'); 
?>