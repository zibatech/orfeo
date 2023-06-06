<?php
session_start();
error_reporting(E_ALL);

require_once("$ruta_raiz/include/db/ConnectionHandler.php");

class InformeEvidencia {

	private static $db;
	private static $rutaRaiz = '..';
	private static $table = 'contratistas_informe_evidencia';
	private static $uploadPath = 'evidencias';
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
	public $obligacion_informe_id;
	public $archivo;
	public $nombre;

	public static function find($id) {
		self::init_db();

		$row = self::$db->conn->GetRow('SELECT * FROM '.self::$table.' WHERE id = ?', [$id]);

		if ($row)
			return self::create_from_row($row);
		else 
			return null;
	}

	public static function getFromObligation($obligacion_informe_id) {
		self::init_db();

		$rows = self::$db->conn->GetAll('SELECT * FROM '.self::$table.' WHERE obligacion_informe_id = ? ORDER BY id DESC', [$obligacion_informe_id]);
		$evidencias = [];
		
		foreach($rows as $row) {
			$evidencias[] = self::create_from_row($row);
		}

		return $evidencias;
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

	public static function upload($obligacion_informe, $file) {
		if (!self::is_valid_mime($file))
			return false;

		if (!self::is_valid_size($file))
			return false;
		
		$evidencia = new InformeEvidencia;

		$uploadPath = self::$uploadPath.'/'.$obligacion_informe->informe_id.'/'.$obligacion_informe->id;
		$path = self::$rutaRaiz.'/bodega/contratistas/';

		self::create_dir_if_not_exists($path.$uploadPath);

		$filename = explode('.', $file['filename']);
		$evidencia->obligacion_informe_id = $obligacion_informe->id;
		$evidencia->nombre = $file['filename'];
		$evidencia->archivo = $uploadPath.'/'.date('YmdHis').'_'.$filename[0].'.'.$filename[1];

		$did_upload = move_uploaded_file($file['tmp_name'], $path.$evidencia->archivo);

		if ($did_upload)
		{
			$evidencia->save();
			return $evidencia;
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

	private function to_save() {
		self::$db->conn->Execute(
			'INSERT INTO '.self::$table.' (obligacion_informe_id, archivo, nombre) VALUES (?,?,?)', 
			[
				$this->obligacion_informe_id,
				$this->archivo,
				$this->nombre
			]);

		$this->id = self::$db->conn->getOne('SELECT id FROM '.self::$table.' WHERE informe_id = ? ORDER BY id DESC LIMIT 1', [$this->informe_id]);
	}

	private function to_update() {
		self::$db->conn->Execute(
				'UPDATE '.self::$table.' SET obligacion_informe_id = ?, archivo = ?, nombre = ? WHERE id = ?',
				[
					$this->obligacion_informe_id,
					$this->archivo,
					$this->nombre,
					$this->id
				]);
	}

	private static function create_from_row($row) {
		$informe_evidencia = new informeEvidencia;
		$informe_evidencia->id = $row['ID'];
		$informe_evidencia->obligacion_informe_id = $row['OBLIGACION_INFORME_ID'];
		$informe_evidencia->archivo = $row['ARCHIVO'];
		$informe_evidencia->nombre = $row['NOMBRE'];

		return $informe_evidencia;
	}

	private static function init_db() {
		if (!self::$db)
			self::$db = new ConnectionHandler(self::$rutaRaiz);
	}

}