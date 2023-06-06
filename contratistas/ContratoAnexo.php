<?php
session_start();
error_reporting(E_ALL);

require_once("$ruta_raiz/include/db/ConnectionHandler.php");

class ContratoAnexo {

	private static $db;
	private static $rutaRaiz = '..';
	private static $table = 'contratistas_contrato_anexo';
	private static $uploadPath = 'anexos';
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
	public $contrato_id;
	public $archivo;
	public $nombre;
	public $tipo;

	public static function find($id) {
		self::init_db();

		$row = self::$db->conn->GetRow('SELECT * FROM '.self::$table.' WHERE id = ?', [$id]);

		if ($row)
			return self::create_from_row($row);
		else 
			return null;
	}

	public static function getFromContract($contrato_id) {
		self::init_db();

		$rows = self::$db->conn->GetAll('SELECT * FROM '.self::$table.' WHERE contrato_id = ? ORDER BY id DESC', [$contrato_id]);
		$anexos = [];
		
		foreach($rows as $row) {
			$anexos[] = self::create_from_row($row);
		}

		return $anexos;
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

	public static function upload($contrato, $tipo, $file) {
		if (!self::is_valid_mime($file))
			return false;

		if (!self::is_valid_size($file))
			return false;
		
		$anexo = new ContratoAnexo;

		$uploadPath = self::$uploadPath.'/'.$contrato->informe_id.'/'.$contrato->id;
		$path = self::$rutaRaiz.'/bodega/contratistas/';
		echo $path;
		self::create_dir_if_not_exists($path.$uploadPath);

		$filename = explode('.', $file['name']);
		$anexo->contrato_id = $contrato->id;
		$anexo->nombre = $file['name'];
		$anexo->archivo = $uploadPath.'/'.date('YmdHis').'_'.$filename[0].'.'.$filename[1];
		$anexo->tipo = $tipo;

		$did_upload = move_uploaded_file($file['tmp_name'], $path.$anexo->archivo);

		if ($did_upload)
		{
			$anexo->save();
			return $anexo;
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
			'INSERT INTO '.self::$table.' (contrato_id, archivo, nombre, tipo) VALUES (?,?,?,?)', 
			[
				$this->contrato_id,
				$this->archivo,
				$this->nombre,
				$this->tipo
			]);

		$this->id = self::$db->conn->getOne('SELECT id FROM '.self::$table.' WHERE informe_id = ? ORDER BY id DESC LIMIT 1', [$this->informe_id]);
	}

	private function to_update() {
		self::$db->conn->Execute(
				'UPDATE '.self::$table.' SET contrato_id = ?, archivo = ?, nombre = ?, tipo = ? WHERE id = ?',
				[
					$this->contrato_id,
					$this->archivo,
					$this->nombre,
					$this->tipo,
					$this->id
				]);
	}

	private static function create_from_row($row) {
		$informe_evidencia = new ContratoAnexo;
		$informe_evidencia->id = $row['ID'];
		$informe_evidencia->contrato_id = $row['CONTRATO_ID'];
		$informe_evidencia->archivo = $row['ARCHIVO'];
		$informe_evidencia->nombre = $row['NOMBRE'];
		$informe_evidencia->tipo = $row['TIPO'];

		return $informe_evidencia;
	}

	private static function init_db() {
		if (!self::$db)
			self::$db = new ConnectionHandler(self::$rutaRaiz);
	}

}