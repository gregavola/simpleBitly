<?php

/**
 * simpleBitly
 * 
 * Author: Greg Avola (@gregavola) - http://github.com/gregavola
 *
 * ========================================================
 * REQUIRES: php5, curl, json_decode
 * ========================================================
 * 
 * VERSION:  1.0
 * LICENSE: GNU GENERAL PUBLIC LICENSE - Version 2, June 1991
 * 
 **/
 
class simpleBitly {
	
	public $apiKey;
	public $username;
	public $http_code;
	
	public $client_id;
	public $client_secret;
	public $redirect_uri;
	public $access_token;
	
	public $userAgent = "simpleBitly 1.0 (http://github.com/gregavola/simpleBitly)";
	
	public $api_ssl_base = "https://api-ssl.bitly.com/";
	public $api_base = "http://api.bitly.com/";
	public $api_version = "v3";
	
	public $api_authorize = "https://bitly.com/oauth/authorize";
	public $api_authenticate = "https://api-ssl.bitly.com/oauth/access_token";
	
	public function __construct($client_id= NULL, $client_secret = NULL, $access_token = NULL)
    {
		$this->access_token = $access_token;
		$this->client_id = $client_id;
        $this->client_secret = $client_secret;
    }

	/**
   * Set Basic Tokens
   *
   * @param $username - the login of the user you want authenticate
   * @param $apikey - the API key of the user who is authenticating 
   */

	public function setBasic($username, $apiKey) {
		$this->apiKey = $apiKey;
		$this->username = $username;
	}
	
	/**
   * Set Tokens for OAuth
   *
   * @param $tokens - unserialized string returned from Bitly
   */

	public function setToken($tokens) {
		
		parse_str($tokens, $tokenSerialized);
		
		if (is_array($tokenSerialized)) {
				
			$this->access_token = $tokenSerialized["access_token"];
			$this->apiKey = $tokenSerialized["apiKey"];
			$this->username = $tokenSerialized["login"];
			
		} 
		else {
			BitlyException::token_issue("The variable that was passed was not an array: " . $tokens);
		}
	}
	
	/**
   * Return the current access token
   *
   */
	function currentToken() {
		return $this->access_token;
	}
	
	/**
   * Gets the authorizeURL to send to Bitly
   *
   * @param $redirect_uri - The URL you want the page to be redirected to. NOTE: This must be the URL you put in your oAuth Sign Up Form.
   */
	
	public function getAuthorizeUrl($redirect_uri) {
		
		$this->redirect_uri = $redirect_uri;
		
		$parms = array(
			"client_id" => $this->client_id,
			"redirect_uri" => $this->redirect_uri
			);
			
		return $this->api_authorize . "?" . http_build_query($parms);
		
	}
	
	/**
   * Get the access token from Bitl
   *
   * @param $code - The code that is returned from Bitly after authorization 
   * @param $redirect_uri - The URL you want the page to be redirected to. NOTE: This must be the URL you put in your oAuth Sign Up Form.
   */
	
	public function getToken($code, $redirect_uri) {
	
		$parms = array(
			"client_id" => $this->client_id,
			"client_secret" => $this->client_secret,
			"redirect_uri" => $redirect_uri,
			"code" => $code
 			);
			
		return $this->call($this->api_authenticate, "POST", $parms);
	}
	
	/**
   *  Make an GET oauth2 call
   *
   * @param $url - The URL of the request you want to make
   * @param $params - The parameters that you want to add in array form
   */
	
	public function get($url, $params = array()) {
		
		if (sizeof($params) == 0) {
            return $this->call($this->preperateURL($url, "oauth2"), "GET", array());
        }
        else {
            return $this->call($this->preperateURL($url, "oauth2") ."&".http_build_query($params), "GET", array());
        }
	}
	
	/**
   *  Make an POST oauth2 call
   *
   * @param $url - The URL of the request you want to make
   * @param $params - The parameters that you want to add in array form
   */
	
	public function post($url, $params) {
		
		if (sizeof($params) == 0) {
            return $this->call($this->preperateURL($url, "oauth2"), "POST", array());
        }
        else {
            return $this->call($this->preperateURL($url, "oauth2"), "POST", $params);
        }
	}
	
	/**
   *  Make an GET basic call
   *
   * @param $url - The URL of the request you want to make
   * @param $params - The parameters that you want to add in array form
   */
	
	public function get_basic($url, $params = array()) {
		
		if (sizeof($params) == 0) {
            return $this->call($this->preperateURL($url, "basic"), "GET", array());
        }
        else {
            return $this->call($this->preperateURL($url, "basic") ."&".http_build_query($params), "GET", array());
        }
	}
	
	/**
   *  Make an POST basic call
   *
   * @param $url - The URL of the request you want to make
   * @param $params - The parameters that you want to add in array form
   */
	
	public function post_basic($url, $params = array()) {

		if (sizeof($params) == 0) {
            return $this->call($this->preperateURL($url, "basic"), "POST", array());
        }
        else {
            return $this->call($this->preperateURL($url, "basic"), "POST", $params);
        }
	}
	
	/**
   *  Generate the URL based on the type of request
   *
   * @param $url - The URL of the request you want to make
   * @param $type_req - Can be oauth2 or basic which defines the type of request you want to make
   */
	
	
	private function preperateURL($url, $type_req = "oauth2") {
		
		if ($type_req == "basic") {
			return $this->api_base .$this->api_version. $url . "?login=".$this->username."&apiKey=".$this->apiKey;
		}
		else {
			return $this->api_ssl_base .$this->api_version. $url . "?access_token=".$this->access_token;
		}
		
	}
	
	/**
   *  Make the CURL request to Bitly
   *
   * @param $url - The URL of the request you want to make
   * @param $method - POST or GET
   * @param $parameters - The parameters that you want to add in array form
   */
	
	private function call($url, $method, $parameters) {

        $curl = curl_init();

		if ($method == "POST")
        {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);
        }
		
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent);

        $result = curl_exec($curl);

        $HttpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $this->http_code = (int)$HttpCode;
		
		if ((int)$HttpCode != 200) {
			BitlyException::raise($url, $HttpCode, $result);
		}
		else {
			
			$data = json_decode($result);
			
			if ((int)$data->status_code != 200) {
				BitlyException::raise($url, $data->status_code, $data->status_txt);
			}
			else {
				return $data;
			}
			
			
		}        
    }
}

class BitlyException extends Exception
{
	public static function raise($url, $httpCode, $error_message)
	{
	    $message = "Error (".$httpCode.") " . $error_message;
        throw new BitlyException($error_message, $httpCode);
	}
	
	public static function token_issue($message)
	{
		throw new BitlyExceptionToken($message);
	}
}

class BitlyExceptionToken extends BitlyException{}