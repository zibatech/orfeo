<?

require __DIR__.'/../vendor/autoload.php';

class RespuestaInvalidaException extends \Exception {}

class Zidoc {
	
	private $results = [];
	private $cliente;
	private $headers;

	public function __construct($signature, $uri) 
	{
		$this->headers = ['Signature' => $signature];
		$client_config = [
			'base_uri' => $uri,
			'headers' => $this->headers
		];

		$this->cliente = new GuzzleHttp\Client($client_config);
	}

	public function verificarServicio() 
	{
		$res = $this->cliente->request('GET', 'expedientes?NumeroExpediente=202115009010001E', $this->headers);
		return $res->getStatusCode();
	}

	public function buscar($parametro, $valor) 
	{
		$ruta = 'expedientes?'.$parametro.'='.$valor;
		try {
			$res = $this->cliente->request('GET', $ruta, $this->headers);
		} catch (GuzzleHttp\Exception\ServerException $se) {
			throw new RespuestaInvalidaException();
		} 
		
		return json_decode($res->getBody(), true);
	}

	public function documentos($id_expediente)
	{
		$ruta = 'documentos?Expediente='.$id_expediente;
		try {
			$res = $this->cliente->request('GET', $ruta, $this->headers);
		} catch (GuzzleHttp\Exception\ServerException $se) {
			throw new RespuestaInvalidaException();
		} 
		
		return json_decode($res->getBody(), true);
	}
}