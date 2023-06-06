<?php



class HpttpHeader
{
    public $headers = "";
    public $cookies = [];
    public function curlHeaderCallback($resURL, $strHeader)
    {
        $this->headers .= $strHeader;
        return strlen($strHeader);
    } 
    public function getCookies()
    {
        preg_match_all(
            '/Set\-Cookie\: ([A-Z-a-z-0-9\=]+)\;/',
            $this->headers,
            $matches,
            PREG_PATTERN_ORDER
        );
        foreach ($matches[1] as $key => $value) 
        {
            list($keyx,$valuex) = (explode("=", $value,2));
            array_push($this->cookies, array( $keyx => $valuex));
        }
        if(count($this->cookies)>0)
        {
            $this->cookies = $this->cookies[0];
        }
        
    }

}
class HttpClient 
{
    public $header = "";
    public $html = "";
    public $location = "";
    public $status_code = 0;
    public $user_agent = "Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201";

    public function __construct()
    {
        $this->header = new HpttpHeader();
    }

    public function parse_qs($query)
    {
        $params = array();
        foreach (explode('&', $query) as $chunk) 
        {
            list($key,$value) = explode("=", $chunk);
            $params[$key] = urldecode($value);
        }
        return  $params;
    }


    public function find_pattern($query)
    {
        preg_match_all(
            $query,
            $this->html,
            $matches,
            PREG_PATTERN_ORDER
        );
        return $matches;
    }
    
    public function find_pattern_location($query)
    {
        preg_match_all(
            $query,
            $this->location,
            $matches,
            PREG_PATTERN_ORDER
        );
        return $matches;
    }


    public function post($strURL, $data="", $headers=array(), $cookies=array(), $following_location=false, $json=false)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $strURL);
        curl_setopt($c, CURLOPT_USERAGENT, $this->user_agent); 
        curl_setopt($c, CURLOPT_HEADERFUNCTION, 
            array($this->header,'curlHeaderCallback')
        );
        curl_setopt($c, CURLOPT_HEADER, false);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($c, CURLOPT_VERBOSE, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        if(count($cookies)>0)
        {
            curl_setopt($c, CURLOPT_COOKIE, http_build_query($cookies));
        }

        if($json == false)
        {
            curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        }
        
        if($json == true)
        {
            $payload = json_encode($data);
            //attach encoded JSON string to the POST fields
            curl_setopt($c, CURLOPT_POSTFIELDS, $payload);
            //set the content type to application/json
            curl_setopt($c, CURLOPT_HTTPHEADER, array(

                'Content-Type: application/json; charset=utf-8',     
                'Accept: application/json',     
            ));
        }


        if(count($headers)>0)
        {
            curl_setopt($c, CURLOPT_HTTPHEADER,$headers);
        }

        if($following_location == true)
        {
            curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
        }

        $this->html = curl_exec($c);
        $this->header->getCookies();
        $this->status_code = curl_getinfo($c, CURLINFO_HTTP_CODE);
        $this->location = curl_getinfo($c, CURLINFO_EFFECTIVE_URL);
        if(curl_errno($c))
        {   
            echo 'Curl error: ' . curl_error($c);
        }           
        curl_close ($c);

        if ($this->status_code != 200) 
        {
            $error =  'was error: ' . $this->status_code;
            throw new \Exception($error);
        } 
   }
    public function post2($strURL, $data=array(), $headers=array(), $cookies=array(), $following_location=false, $json=false)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $strURL);
        //curl_setopt($c, CURLOPT_USERAGENT, $this->user_agent); 
        curl_setopt($c, CURLOPT_HEADERFUNCTION, 
            array($this->header,'curlHeaderCallback')
        );
        curl_setopt($c, CURLOPT_HEADER, 1);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($c, CURLOPT_VERBOSE, 0);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        if(count($cookies)>0)
        {
            curl_setopt($c, CURLOPT_COOKIE, http_build_query($cookies));
        }

        if($json == false)
        {
            curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        
        if($json == true)
        {
            $payload = json_encode($data);
            //attach encoded JSON string to the POST fields
            curl_setopt($c, CURLOPT_POSTFIELDS, $payload);
            //set the content type to application/json
            curl_setopt($c, CURLOPT_HTTPHEADER, array(

                'Content-Type: application/json; charset=utf-8',     
                'Accept: application/json',     
            ));
        }


        if(count($headers)>0)
        {
            curl_setopt($c, CURLOPT_HTTPHEADER,$headers);
        }

        if($following_location == true)
        {
            curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        }

        $this->html = curl_exec($c);
        $this->header->getCookies();
        $this->status_code = curl_getinfo($c, CURLINFO_HTTP_CODE);
        $this->location = curl_getinfo($c, CURLINFO_EFFECTIVE_URL);
        if(curl_errno($c))
        {   
            echo 'Curl error: ' . curl_error($c);
        }           
        curl_close ($c);

        if ($this->status_code != 200) 
        {
           
        } 
   }

   public function get($strURL,$headers=array(),$cookies=array(),$following_location=false)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $strURL);
        curl_setopt($c, CURLOPT_USERAGENT, $this->user_agent); 
        curl_setopt($c, CURLOPT_HEADERFUNCTION, 
            array($this->header,'curlHeaderCallback')
        );

        if(count($cookies)>0)
        {
            curl_setopt($c, CURLOPT_COOKIE, http_build_query($cookies));
        }

        if(count($headers)>0)
        {
            curl_setopt($c, CURLOPT_HTTPHEADER,$headers);
        }
        
        curl_setopt($c, CURLOPT_HEADER, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true); 

        if($following_location == true)
        {
            curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
        }

        $this->html = curl_exec($c);
        $this->status_code = curl_getinfo($c, CURLINFO_HTTP_CODE);
        $this->header->getCookies();
        $this->location = curl_getinfo($c, CURLINFO_EFFECTIVE_URL);
        if(curl_errno($c))
        {   
            echo 'Curl error: ' . curl_error($c);
        }
        curl_close ($c);
        
        if ($this->status_code != 200) 
        {
            $error =  'was error: ' . $this->status_code;
            throw new \Exception($error);
        }
   }


  
 


}