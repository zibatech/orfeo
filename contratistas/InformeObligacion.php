<?php
session_start();
error_reporting(E_ALL);

require_once("$ruta_raiz/include/db/ConnectionHandler.php");

class InformeObligacion {

	private static $db;
	private static $rutaRaiz = '..';
	private static $table = 'contratistas_informe_obligacion';
	public $id;
	public $informe_id;
	public $obligacion_id;
	public $fecha_inicio;
	public $fecha_fin;
	public $descripcion;

	public static function find($id) {
		self::init_db();

		$row = self::$db->conn->GetRow('SELECT * FROM '.self::$table.' WHERE id = ?', [$id]);

		if ($row)
			return self::create_from_row($row);
		else 
			return null;
	}

	public static function findByReportObligation($informe_id, $obligacion_id) {
		self::init_db();

		$row = self::$db->conn->GetRow('SELECT * FROM '.self::$table.' WHERE informe_id = ? AND obligacion_id = ?', [$informe_id, $obligacion_id]);

		if ($row)
			return self::create_from_row($row);
		else 
			return null;
	}

	public static function getFromReport($informe_id) {
		self::init_db();

		$rows = self::$db->conn->GetAll('SELECT * FROM '.self::$table.' WHERE informe_id = ? ORDER BY id DESC', [$informe_id]);
		$obligaciones = [];
		
		foreach($rows as $row) {
			$obligaciones[] = self::create_from_row($row);
		}

		return $obligaciones;
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
			'INSERT INTO '.self::$table.' (informe_id, obligacion_id, fecha_inicio, fecha_fin, descripcion) VALUES (?,?,?,?,?)', 
			[
				$this->informe_id,
				$this->obligacion_id,
				$this->fecha_inicio,
				$this->fecha_fin,
				$this->descripcion
			]);

		$this->id = self::$db->conn->getOne('SELECT id FROM '.self::$table.' WHERE informe_id = ? ORDER BY id DESC LIMIT 1', [$this->informe_id]);
	}

	private function to_update() {
		self::$db->conn->Execute(
				'UPDATE '.self::$table.' SET informe_id = ?, obligacion_id = ?, fecha_inicio = ?, fecha_fin = ?, descripcion = ? WHERE id = ?',
				[
					$this->informe_id,
					$this->obligacion_id,
					$this->fecha_inicio,
					$this->fecha_fin,
					$this->descripcion,
					$this->id
				]);
	}

	private static function create_from_row($row) {
		$informe_obligacion = new InformeObligacion;
		$informe_obligacion->id = $row['ID'];
		$informe_obligacion->informe_id = $row['INFORME_ID'];
		$informe_obligacion->obligacion_id = $row['OBLIGACION_ID'];
		$informe_obligacion->fecha_inicio = $row['FECHA_INICIO'];
		$informe_obligacion->fecha_fin = $row['FECHA_FIN'];
		$informe_obligacion->descripcion = $row['DESCRIPCION'];

		return $informe_obligacion;
	}

	private static function init_db() {
		if (!self::$db)
			self::$db = new ConnectionHandler(self::$rutaRaiz);
	}

}