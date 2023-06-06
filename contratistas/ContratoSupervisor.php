<?php
session_start();
error_reporting(E_ALL);

require_once("$ruta_raiz/include/db/ConnectionHandler.php");

class ContratoSupervisor {

	private static $db;
	private static $rutaRaiz = '..';
	private static $table = 'contratistas_contrato_supervisor';
	public $id;
	public $contrato_id;
	public $usuario_id;
	public $usuario;

	public static function find($id) {
		self::init_db();

		$row = self::$db->conn->GetRow('SELECT * FROM '.self::$table.' WHERE id = ?', [$id]);

		if ($row)
			return self::create_from_row($row);
		else 
			return null;
	}

	public static function findByContractSupervisor($contrato_id, $usuario_id)
	{
		self::init_db();

		$row = self::$db->conn->GetRow('SELECT * FROM '.self::$table.' WHERE contrato_id = ? AND usuario_id = ?', [$contrato_id, $usuario_id]);

		if ($row)
			return self::create_from_row($row);
		else
			return null;
	}

	public static function getFromContract($contrato_id) {
		self::init_db();

		$rows = self::$db->conn->GetAll('SELECT * FROM '.self::$table.' WHERE contrato_id = ? ORDER BY id DESC', [$contrato_id]);
		$supervisores = [];
		
		foreach($rows as $row) {
			$supervisores[] = self::create_from_row($row);
		}

		return $supervisores;
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
			'INSERT INTO '.self::$table.' (contrato_id, usuario_id) VALUES (?,?)', 
			[
				$this->contrato_id,
				$this->usuario_id
			]);

		$this->id = self::$db->conn->getOne('SELECT id FROM '.self::$table.' WHERE contrato_id = ? AND usuario_id = ? ORDER BY id DESC LIMIT 1', [$this->contrato_id, $this->usuario_id]);
	}

	private function to_update() {
		self::$db->conn->Execute(
				'UPDATE '.self::$table.' SET contrato_id = ?, usuario_id = ? WHERE id = ?',
				[
					$this->contrato_id,
					$this->usuario_id,
					$this->id
				]);
	}

	private static function create_from_row($row) {
		$contrato = new ContratoSupervisor;
		$contrato->id = $row['ID'];
		$contrato->contrato_id = $row['CONTRATO_ID'];
		$contrato->usuario_id = $row['USUARIO_ID'];
		$contrato->usuario = self::$db->conn->GetRow('SELECT * FROM usuario WHERE id = ?', $row['USUARIO_ID']);

		return $contrato;
	}

	private static function init_db() {
		if (!self::$db)
			self::$db = new ConnectionHandler(self::$rutaRaiz);
	}

}