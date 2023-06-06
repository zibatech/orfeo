<?php
session_start();
error_reporting(E_ALL);

require_once("$ruta_raiz/include/db/ConnectionHandler.php");
require_once($ruta_raiz.'/contratistas/Estado.php');

class Informe {

	private static $db;
	private static $rutaRaiz = '..';
	private static $table = 'contratistas_informe';
	public $id;
	public $contrato_id;
	public $numero;
	public $fecha_inicio;
	public $fecha_fin;
	public $valor_ejecutado;
	public $total_ejecutado;
	public $fecha_informe;
	public $estado;
	public $procede_pago;

	public static function find($id) 
	{
		self::init_db();

		$row = self::$db->conn->GetRow('SELECT * FROM '.self::$table.' WHERE id = ?', [$id]);

		if ($row)
			return self::create_from_row($row);
		else 
			return null;
	}

	public static function last($contrato_id) 
	{
		self::init_db();
		$id = self::$db->conn->GetOne('SELECT max(id) FROM '.self::$table.' WHERE contrato_id = ?', [$contrato_id]);

		return self::find($id);
	}

	public static function getPaidByDateRange($fecha_inicio, $fecha_fin) 
	{
		self::init_db();
		$informes_ids = self::$db->conn->GetAll('SELECT id FROM '.self::$table.' WHERE fecha_informe BETWEEN ? AND ? and procede_pago = true', [$fecha_inicio, $fecha_fin]);

		$informes = [];

		foreach($informes_ids as $inf) {
			$informes[] = self::find($inf['ID']);
		}

		return $informes;
	}

	public static function getForPaymentWithPendingFile($requerimiento_id) 
	{
		self::init_db();
		$informes_ids = self::$db->conn->GetAll('SELECT informe_id FROM contratistas_informe_requerimiento WHERE requerimiento_id = ? AND adjunto is null', [$requerimiento_id]);

		$informes = [];

		foreach($informes_ids as $inf) {
			$informe = self::find($inf['INFORME_ID']);
			if ($informe->estado == InformeEstado::$APROBADO)
				$informes[] = self::find($inf['INFORME_ID']);
		}

		return $informes;
	}

	public static function getForPayment()
	{
		self::init_db();
		$informes_ids = self::$db->conn->GetAll('SELECT ci.id
							FROM contratistas_informe ci
							WHERE EXISTS(
							        SELECT informe_id
							        FROM contratistas_informe_requerimiento cir
							        JOIN contratistas_requerimientos_pago crp on cir.requerimiento_id = crp.id
							        WHERE ci.id = cir.informe_id
							        GROUP BY informe_id
							        HAVING (COUNT(informe_id) filter (where adjunto is null and opcional = true and crp.opcional = false)) = 0
							    )
							  AND estado = ?
							  AND procede_pago = false', [InformeEstado::$APROBADO]);

		$informes = [];

		foreach($informes_ids as $inf) {
			$informes[] = self::find($inf['ID']);
		}

		return $informes;
	}

	public static function getFromContract($contrato_id) {
		self::init_db();

		$rows = self::$db->conn->GetAll('SELECT * FROM '.self::$table.' WHERE contrato_id = ? ORDER BY id DESC', [$contrato_id]);
		$informes = [];
		
		foreach($rows as $row) {
			$informes[] = self::create_from_row($row);
		}

		return $informes;
	}

	public function __construct() {	
		self::init_db();
		$this->id = null;
	}

	public function procede_pago($state) {
		$this->procede_pago = $state;
		$this->save();
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
			'INSERT INTO '.self::$table.' (contrato_id, numero, fecha_inicio, fecha_fin, valor_ejecutado, total_ejecutado, fecha_informe, estado) VALUES (?,?,?,?,?,?,?,?)', 
			[
				$this->contrato_id,
				$this->numero,
				$this->fecha_inicio,
				$this->fecha_fin,
				$this->valor_ejecutado,
				$this->total_ejecutado,
				$this->fecha_informe,
				$this->estado,
			]);

		$this->id = self::$db->conn->getOne('SELECT id FROM '.self::$table.' WHERE contrato_id = ? ORDER BY id DESC LIMIT 1', [$this->contrato_id]);
	}

	private function to_update() {
		self::$db->conn->Execute(
				'UPDATE '.self::$table.' SET contrato_id = ?, numero = ?, fecha_inicio = ?, fecha_fin = ?, valor_ejecutado = ?, total_ejecutado = ?, fecha_informe = ?, estado = ?, procede_pago = ? WHERE id = ?',
				[
					$this->contrato_id,
					$this->numero,
					$this->fecha_inicio,
					$this->fecha_fin,
					$this->valor_ejecutado,
					$this->total_ejecutado,
					$this->fecha_informe,
					$this->estado,
					$this->procede_pago,
					$this->id
				]);
	}

	private static function create_from_row($row) {
		$informe = new Informe;
		$informe->id = $row['ID'];
		$informe->contrato_id = $row['CONTRATO_ID'];
		$informe->numero = $row['NUMERO'];
		$informe->fecha_inicio = $row['FECHA_INICIO'];
		$informe->fecha_fin = $row['FECHA_FIN'];
		$informe->valor_ejecutado = $row['VALOR_EJECUTADO'];
		$informe->total_ejecutado = $row['TOTAL_EJECUTADO'];
		$informe->fecha_informe = $row['FECHA_INFORME'];
		$informe->estado = $row['ESTADO'];
		$informe->procede_pago = $row['PROCEDE_PAGO'];

		return $informe;
	}

	private static function init_db() {
		if (!self::$db)
			self::$db = new ConnectionHandler(self::$rutaRaiz);
	}

}