<?php
session_start();
error_reporting(E_ALL);

require_once("$ruta_raiz/include/db/ConnectionHandler.php");

class InformeRequerimiento {

	private static $db;
	private static $rutaRaiz = '..';
	private static $table = 'contratistas_informe_requerimiento';
	private static $uploadPath = 'pagos';
	private static $fileSize = 10000000;
	private static $fileExtensionsAllowed = [
		'application/msword',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'application/vnd.ms-excel',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'image/png',
		'image/jpeg',
		'application/pdf',
	];
	private $errors = [];
	public $id;
	public $informe_id;
	public $requerimiento_id;
	public $adjunto;
	public $usuario_id;
	public $fecha;

	public static function find($id) {
		self::init_db();

		$row = self::$db->conn->GetRow('SELECT * FROM '.self::$table.' WHERE id = ?', [$id]);

		if ($row)
			return self::create_from_row($row);
		else 
			return null;
	}

	public static function getFromReport($inf) {
		self::init_db();

		$rows = self::$db->conn->GetAll('SELECT cir.* FROM '.self::$table.' cir join contratistas_requerimientos_pago crp on cir.requerimiento_id = crp.id WHERE informe_id = ? ORDER BY crp.opcional ASC, crp.contratista DESC, cir.id', [$inf]);
		$informe_requerimiento = [];
		
		foreach($rows as $row) {
			$informe_requerimiento[] = self::create_from_row($row);
		}

		return $informe_requerimiento;
	}

	public static function getFromReportUser($inf, $user) {
		self::init_db();
		
		$rows = self::$db->conn->GetAll('SELECT * FROM contratistas_informe_requerimiento WHERE informe_id = ? and usuario_id = ?', [$inf, $user]);
		$informe_requerimiento = [];
		
		foreach($rows as $row) {
			$informe_requerimiento[] = self::create_from_row($row);
		}

		return $informe_requerimiento;
	}

	private static function create_dir_if_not_exists($path) {
		if (!file_exists($path))
		    mkdir($path, 0775, true);
	}

	private static function is_valid_mime($file) {
		return in_array($file['type'], self::$fileExtensionsAllowed);
	}

	private static function is_valid_size($file) {
		return $file['size'] < self::$fileSize;
	}

	public function is_from_employe() {
		$is_from_employe = self::$db->conn->getOne('SELECT contratista FROM contratistas_requerimientos_pago WHERE id = ?', [$this->requerimiento_id]);

		return $is_from_employe == 't';
	}

	public function get_doc_name() {
		return self::$db->conn->getOne('SELECT documento FROM contratistas_requerimientos_pago WHERE id = ?', [$this->requerimiento_id]);
	}

	public function its_optional_doc() {
		return self::$db->conn->getOne('SELECT opcional FROM contratistas_requerimientos_pago WHERE id = ?', [$this->requerimiento_id]) == 't';
	}

	public function upload($file) {
		if (!self::is_valid_mime($file))
			return false;

		if (!self::is_valid_size($file))
			return false;

		$uploadPath = self::$uploadPath.'/'.$this->informe_id;
		$path = self::$rutaRaiz.'/bodega/contratistas/';

		self::create_dir_if_not_exists($path.$uploadPath);

		$filename = explode('.', $file['name']);
		$this->adjunto = $uploadPath.'/'.date('YmdHis').'_'.$filename[0].'.'.$filename[1];
		$this->usuario_id = $_SESSION['usua_id'];
		$this->fecha = date('Y-m-d H:i:s');

		$did_upload = move_uploaded_file($file['tmp_name'], $path.$this->adjunto);

		if ($did_upload)
		{
			$this->save();
			return $this;
		} else {
			return false;
		}
	}

	public function __construct() {	
		self::init_db();
		$this->id = null;
	}

	public function save() {
		if (!$this->can_perform_action())
			return false;

		if ($this->id == null)
			$this->to_save();
		else 
			$this->to_update();
	}

	public function delete() {
		if (!$this->can_perform_action())
			return false;

		self::$db->conn->Execute('DELETE FROM '.self::$table.' WHERE id = ?', 
			[
				$this->id
			]);
	}

	public function can_perform_action() {
		return true;
	}

	public function validateDependence($dependencia) {
		$count = self::$db->conn->getOne('SELECT count(id) FROM contratistas_requerimientos_pago_dependencia WHERE requerimiento_id = ? AND depe_codi = ?', [$this->requerimiento_id, $dependencia]);

		return $count;
	}

	private function to_save() {
		self::$db->conn->Execute(
			'INSERT INTO '.self::$table.' (informe_id, requerimiento_id) VALUES (?,?)', 
			[
				$this->informe_id,
				$this->requerimiento_id
			]);

		$this->id = self::$db->conn->getOne('SELECT id FROM '.self::$table.' WHERE informe_id = ? ORDER BY id DESC LIMIT 1', [$this->informe_id]);
	}

	private function to_update() {
		self::$db->conn->Execute(
				'UPDATE '.self::$table.' SET informe_id = ?, requerimiento_id = ?, adjunto = ?, usuario_id = ?, fecha = ? WHERE id = ?',
				[
					$this->informe_id,
					$this->requerimiento_id,
					$this->adjunto,
					$this->usuario_id,
					$this->fecha,
					$this->id
				]);
	}

	private static function create_from_row($row) {
		$informe_requerimiento = new InformeRequerimiento;
		$informe_requerimiento->id = $row['ID'];
		$informe_requerimiento->informe_id = $row['INFORME_ID'];
		$informe_requerimiento->requerimiento_id = $row['REQUERIMIENTO_ID'];
		$informe_requerimiento->adjunto = $row['ADJUNTO'];
		$informe_requerimiento->usuario_id = $row['USUARIO_ID'];
		$informe_requerimiento->fecha = $row['FECHA'];

		return $informe_requerimiento;
	}

	private static function init_db() {
		if (!self::$db)
			self::$db = new ConnectionHandler(self::$rutaRaiz);
	}

}