<?php


      class MYPDF extends TCPDF
      {
          protected $processId = 0;
          protected $header = '';
          protected $footer = '';
          static $errorMsg = '';

          protected $radicadoOrfeo;
          protected $fechaOrfeo;
          protected $epigrafe;
          protected $tableEpi;

          function __construct($radicadoOrfeo, $fechaOrfeo, $epigrafe) {
             $this->radicadoOrfeo = $radicadoOrfeo;
             $this->fechaOrfeo = $fechaOrfeo;
             $this->epigrafe = $epigrafe;
             parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
          }


          /**
            * This method is used to override the parent class method.
          **/
          public function Header()
          {
            
              if($this->page == 1) {
                  $this->SetY(10);
                  $this->Cell(0, 15, 'República de Colombia', 0, false, 'C', 0, '', 0, false, 'M', 'M');      
                  $this->SetLineStyle( array( 'width' => 0.2, 'color' => array(0,0,0)));
                  $this->RoundedRect(20, 20, $this->getPageWidth() -40, $this->getPageHeight() - 40, 5);
                  $image_file = '../bodega/sys_img/escudo.jpg';
                  $this->Image($image_file, 'C', 15, 20, '', 'JPG', false, 'C', false, 300, 'C', false, false, 0, false, false, false);
              } else {
                  $this->SetY(10);
                  $this->Cell(0, 15, 'RESOLUCIÓN NÚMERO ' . $this->radicadoOrfeo . ' DE ' . $this->fechaOrfeo . ' HOJA  No. ' . $this->getAliasNumPage(), 0, false, 'C', 0, '', 0, false, 'M', 'M'); 
                  $this->SetY(25);

                  $this->tableEpi = '<table border="0">
                       <tr align="center">
                            <td>' . $this->epigrafe  . '</td>
                       </tr> 
                  </table>';
                  $this->writeHTML($this->tableEpi, true, false, true, false, '');                   
                  $this->Line($this->getX(), $this->getY(), $this->getPageWidth() - 22,  $this->getY());

                  $this->SetLineStyle( array( 'width' => 0.2, 'color' => array(0,0,0)));
                  $this->RoundedRect(20, 20, $this->getPageWidth() -40, $this->getPageHeight() - 40, 5);
              }


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
      
      $pdf = new MYPDF($numradNofi, $anho, $radi_asun);
      //$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($setAutor);
        $pdf->SetTitle($SetTitle);
        $pdf->SetSubject($SetSubject);
        $pdf->SetKeywords($SetKeywords);

      
      $pdf->SetMargins(22, 56, 22);
      $pdf->AddPage();    

        // define barcode style
       /* $style = array(
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
        // echo "Entro a Radicar Anexo";
        $style['position'] = 'R';
        $pdf->write1DBarcode($nurad, 'C39', '', '', '', 7, 0.2, $style, 'N');*/
        // output the HTML content      

      
      $pdf->writeHTML($asu, true, false, true, false, '');
      $pdf->Output($ruta_raiz.$ruta2, 'F');

?>