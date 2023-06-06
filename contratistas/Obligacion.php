<?php
session_start();
error_reporting(E_ALL);

require_once("$ruta_raiz/include/db/ConnectionHandler.php");

class Obligacion {

	private static $db;
	private static $rutaRaiz = '..';
	public $id;
	public $contrato_id;
	public $descripcion;
	public $numero;

	public static function find($id) {
		self::init_db();

		$row = self::$db->conn->GetRow('SELECT * FROM contratistas_obligacion WHERE id = ?', [$id]);

		if ($row)
			return self::create_from_row($row);
		else 
			return null;
	}

	public static function getFromContract($contrato_id) {
		self::init_db();

		$rows = self::$db->conn->GetAll('SELECT * FROM contratistas_obligacion WHERE contrato_id = ? ORDER BY id', [$contrato_id]);
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
		if ($this->id == null)
			$this->to_save();
		else 
			$this->to_update();
	}

	public function delete() {
		if (!$this->can_perform_action())
			return false;

		self::$db->conn->Execute('DELETE FROM contratistas_obligacion WHERE id = ?', 
			[
				$this->id
			]);
	}

	public function can_perform_action() {
		return true;
	}

	private function to_save() {
		self::$db->conn->Execute(
			'INSERT INTO contratistas_obligacion (contrato_id, descripcion, numero) VALUES (?,?,?)', 
			[
				$this->contrato_id,
				$this->descripcion,
				$this->numero
			]);

		$this->id = self::$db->conn->getOne('SELECT id FROM contratistas_obligacion WHERE contrato_id = ? ORDER BY id DESC LIMIT 1', [$this->contrato_id]);
	}

	private function to_update() {
		self::$db->conn->Execute(
				'UPDATE contratistas_obligacion SET contrato_id = ?, descripcion = ?, numero = ? WHERE id = ?',
				[
					$this->contrato_id,
					$this->descripcion,
					$this->numero,
					$this->id
				]);
	}

	private static function create_from_row($row) {
		$obligacion = new Obligacion;
		$obligacion->id = $row['ID'];
		$obligacion->contrato_id = $row['CONTRATO_ID'];
		$obligacion->descripcion = $row['DESCRIPCION'];
		$obligacion->numero = $row['NUMERO'];

		return $obligacion;
	}

	private static function init_db() {
		if (!self::$db)
			self::$db = new ConnectionHandler(self::$rutaRaiz);
	}

}