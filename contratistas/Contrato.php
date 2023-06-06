<?php
session_start();
error_reporting(E_ALL);

require_once("$ruta_raiz/include/db/ConnectionHandler.php");

class Contrato {

	private static $db;
	private static $rutaRaiz = '..';
	public $id;
	public $contrato;
	public $objeto;
	public $fecha_inicio;
	public $fecha_fin;
	public $valor;
	public $honorarios_mensuales;
	public $usuario_id;
	public $expediente;
	public $rp;
	public $fecha_rp;
	public $cdp;
	public $fecha_cdp;

	public static function find($id) {
		self::init_db();

		$row = self::$db->conn->GetRow('SELECT * FROM contratistas_contrato WHERE id = ?', [$id]);

		if ($row)
			return self::create_from_row($row);
		else 
			return null;
	}

	public static function getFromUser($usuario_id) {
		self::init_db();

		$rows = self::$db->conn->GetAll('SELECT * FROM contratistas_contrato WHERE usuario_id = ? ORDER BY id DESC', [$usuario_id]);
		$contratos = [];
		
		foreach($rows as $row) {
			$contratos[] = self::create_from_row($row);
		}

		return $contratos;
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

		self::$db->conn->Execute('DELETE FROM contratistas_contrato WHERE id = ?', 
			[
				$this->id
			]);
	}

	public function can_perform_action() {
		return true;
	}

	private function to_save() {
		self::$db->conn->Execute(
			'INSERT INTO contratistas_contrato (contrato, objeto, fecha_inicio, fecha_fin, valor, honorarios_mensuales, usuario_id, expediente, rp, fecha_rp, cdp,fecha_cdp) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)', 
			[
				$this->contrato,
				$this->objeto,
				$this->fecha_inicio,
				$this->fecha_fin,
				$this->valor,
				$this->honorarios_mensuales,
				$this->usuario_id,
				$this->expediente,
				$this->rp,
				$this->fecha_rp,
				$this->cdp,
				$this->fecha_cdp,
			]);

		$this->id = self::$db->conn->getOne('SELECT id FROM contratistas_contrato WHERE usuario_id = ? ORDER BY id DESC LIMIT 1', [$this->usuario_id]);
	}

	private function to_update() {
		self::$db->conn->Execute(
				'UPDATE contratistas_contrato SET contrato = ?, objeto = ?, fecha_inicio = ?, fecha_fin = ?, valor = ?, honorarios_mensuales = ?, usuario_id = ?, expediente = ?, rp = ?, fecha_rp = ?, cdp = ?, fecha_cdp = ? WHERE id = ?',
				[
					$this->contrato,
					$this->objeto,
					$this->fecha_inicio,
					$this->fecha_fin,
					$this->valor,
					$this->honorarios_mensuales,
					$this->usuario_id,
					$this->expediente,
					$this->rp,
					$this->fecha_rp,
					$this->cdp,
					$this->fecha_cdp,
					$this->id
				]);
	}

	private static function create_from_row($row) {
		$contrato = new Contrato;
		$contrato->id = $row['ID'];
		$contrato->contrato = $row['CONTRATO'];
		$contrato->objeto = $row['OBJETO'];
		$contrato->fecha_inicio = $row['FECHA_INICIO'];
		$contrato->fecha_fin = $row['FECHA_FIN'];
		$contrato->valor = $row['VALOR'];
		$contrato->honorarios_mensuales = $row['HONORARIOS_MENSUALES'];
		$contrato->usuario_id = $row['USUARIO_ID'];
		$contrato->expediente = $row['EXPEDIENTE'];
		$contrato->rp = $row['RP'];
		$contrato->fecha_rp = $row['FECHA_RP'];
		$contrato->cdp = $row['CDP'];
		$contrato->fecha_cdp = $row['FECHA_CDP'];

		return $contrato;
	}

	private static function init_db() {
		if (!self::$db)
			self::$db = new ConnectionHandler(self::$rutaRaiz);
	}

}