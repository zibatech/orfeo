<?php

class client
{
	
	private $url = '';
	private $uri = '';
	
	public function __construct()
	{
		try {
			$this->url = "http://localhost/argogpl/webServices/server.php";		
			$this->uri = "http://localhost/argogpl/webServices/";		
			$this->instance = new SoapClient(Null, array(
				"location" => $this->url,
				"uri" => $this->uri,
				'trace' => true,
				'encoding'=>'utf-8',
				'cache_wsdl' => WSDL_CACHE_NONE,
				"exception" => 0));		
		} catch (Exception $e) {
			printf("Error:sendSms: %s\n", $e->__toString());
			return false;
		}
	}
	
	public function getName($id_array)
	{
		return $this->instance->__soapCall('y', $id_array, 
                    array('soapaction' => 'some_action',
                          'uri'        => 'some_uri')
                    );
	}
	
	public function geyAuth($id)
	{	
		var_dump($id);
		return $this->instance->__soapCall('auth', array($id));
	}
	
}

$client = new client();

$id_array = array('a' => '123456789', 'gato' => 'twr');
echo '<pre>'. $client->getName($id_array);
echo '<pre>'. $client->geyAuth($id_array);


?>


	
	


