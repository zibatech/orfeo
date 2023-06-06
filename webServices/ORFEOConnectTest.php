<?php
/**
 * ORFEOConnect - ORFEO creation and transport class.
 * PHPUNIT V 9
 * @see https://repo.correlibre.org/argopublico/argogpl
 * The ORFEOConnect OrfeoGPL project
 *
 * @author    cesar.gonzalez@hdsas.co
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */
declare(strict_types=1);

require("ORFEOConnect.php");

use PHPUnit\Framework\TestCase;


class ORFEOConnectTest extends TestCase
{

    protected static $tb;
    protected static $CONTENT_PATH;
    protected static $funcionarioCedula = 10153900001;
    protected static $funcionarioEmail  = 'orfeopruebas1@gmail.com';


    public static function setUpBeforeClass(): void
    {

        $ruta_raiz = '..';
        require("$ruta_raiz/dbconfig.php");
        require("$ruta_raiz/processConfig.php");

        self::$CONTENT_PATH = $CONTENT_PATH;
        self::$tb = new ORFEOConnect($dbx);
    }

    protected function assertPreConditions(): void
    {
        self::$tb->setContentPath(self::$CONTENT_PATH);
        self::$tb->setSizeRad(14);
    }


    public function testTiposDocumentales()
    {
        $td = self::$tb->tipoDocumentalParaRadicar();
        self::assertFalse(empty($td), 'No se tienen datos de tipo documental');
        self::assertIsArray($td, 'No regreso un arreglo de tipos documentales para radicación');
        return (int)$td[0]['codigoTipoDoc'];

    }

    public function testGetUsuarioSelect()
    {
        $td1 = self::$tb->getUsuarioSelect('0000000');
        self::assertFalse($td1, 'El usuario no existe');

        $td2 = self::$tb->getUsuarioSelect('');
        self::assertFalse($td2, 'El usuario no fue suministrado');

        $td3 = self::$tb->getUsuarioSelect('adfadfadadfa sdf ');

        var_dump(self::$tb->getListError()['arg_insuficientes'], explode('=>',self::$tb->getError())[0]);
        self::assertContains(self::$tb->getListError()['arg_insuficientes'], self::$tb->getError());
        self::assertFalse($td3, 'No es un correo valido');

        $td4 = self::$tb->getUsuarioSelect(self::$funcionarioCedula);
        self::assertIsArray($td4, 'Usuario existente en la base de datos');
        self::assertArrayHasKey('email',$td4 , 'El usuario tiene datos en el campo email');
        self::assertArrayHasKey('login',$td4 , 'El usuario tiene datos en el campo login');
        self::assertArrayHasKey('codusuario',$td4 , 'El usuario tiene datos en el campo codusuario');
        self::assertArrayHasKey('dependencia',$td4 , 'El usuario tiene datos en el campo dependencia');
        self::assertArrayHasKey('documento',$td4 , 'El usuario tiene datos en el campo documento');
        $td4 = self::$tb->getUsuarioSelect(self::$funcionarioEmail);
        self::assertIsArray($td4, 'Usuario existente en la base de datos');

        return $td4;
    }

    /**
     * @depends testTiposDocumentales
     * @depends testGetUsuarioSelect
     */

    public function testCrearRadicado(int $tipoDocumental, array $funcionario)
    {

        $ciudadano['docum'] = '8900121';
        $ciudadano['email'] = 'uncorreodeprueba@test.co';
        $ciudadano['nombr'] = 'Señor de prueba';
        $ciudadano['direc'] = 'La dirección 23 No test colores';

        $ciudadano['docum'] = $td['documento'];
        $ciudadano['email'] = $td['email'];
        $ciudadano['nombr'] = $td['nombre'];
        $ciudadano['direc'] = "Direccion de la entidad";

        $sar0 = self::$tb->crearRadicado( 00000000 , 10, 1111, '', '', array());
        self::assertFalse($sar0, 'Datos insuficientes no se genera radicado');
        $sar1 = self::$tb->crearRadicado( 00000000 , 10, 1111, '', '', array());
        self::assertFalse($sar1, 'Datos insuficientes no se genera radicado');
        $sar2 = self::$tb->crearRadicado( $this->funcionarioCedula , 0, 1111, '', '', array());
        self::assertFalse($sar2, 'Datos insuficientes no se genera radicado');
        $sar3 = self::$tb->crearRadicado( $this->funcionarioCedula , 10 ,$tipoDocumental,  '', '', array());
        self::assertFalse($sar3, 'Datos insuficientes no se genera radicado');
        $sar4 = self::$tb->crearRadicado( $this->funcionarioCedula , 1 ,$tipoDocumental,  ' Radicado de prueba ', '');
    }

    /*
                public function testConsultarRadicado() {
                    $this->assertFalse(self::$tb->consultarRadicado(44444444));
                    $this->assertFalse(self::$tb->consultarRadicado(200000000000000000001));
                    $this->assertFalse(self::$tb->consultarRadicado(20119000000232));
                    $temp1 = self::$tb->consultarRadicado(20209000000232);
                    $this->assertArrayHasKey('ARCHIVO_B64', $temp1, 'Parametro del arreglo de radicados');
                    $this->assertArrayHasKey('NUMERO', $temp1, 'Parametro del arreglo de radicados');
                    $this->assertArrayHasKey('ASUNTO', $temp1, 'Parametro del arreglo de radicados');
                    $this->assertArrayHasKey('FECHA_RADICADO', $temp1, 'Parametro del arreglo de radicados');
                    $this->assertArrayHasKey('TIPODOCUMENTAL', $temp1, 'Parametro del arreglo de radicados');
                    $this->assertArrayHasKey('ANEXO', $temp1, 'Parametro del arreglo de radicados');
                    $this->assertArrayHasKey('DEPEN_ACTUAL', $temp1, 'Parametro del arreglo de radicados');
                    $this->assertArrayHasKey('USUARIO_ACTUAL', $temp1, 'Parametro del arreglo de radicados');
                }

                public function testConsultarAnexos() {
                    $this->assertFalse(self::$tb->consultarAnexos(2020000000232), 'No existe el radicado');
                    $this->assertFalse(self::$tb->consultarAnexos(202000000), 'Radicado con formato incorrecto');
                    $temp1 = self::$tb->consultarAnexos(20209000000232);
                    $this->assertInternalType('array',$temp1,'Debe ser un arreglo de anexos');
                    $this->assertNotEmpty($temp1, 'Retorna el listado de anexos en un arreglo');
                }

                public function testAsociarRadicado() {}

                public function testCrearAnexo() {}

                public function testFirmaDigital() {}

                public function testUploadFile() {}

                public function testCambiarImagenRad() {}

                public function testDarEstado() {}

                public function testSubseries() {}

                public function testSeries() {}

                public function testActualizarTrd() {}

                public function testReasignarRadicado() {}

                public function testTipoDocumentalParaRadicar() {}

                public function testCrearExpediente() {}

                public function testAnexarExpediente() {}

                public function testCerrarExpediente() {}

                public function testAnulacionRadicado() {}

                public function testVerificaSolAnulacion() {}*/

}
