<?php

// Add the simpleBitly Library
include ("simpleBitly.php");

// create an instance simpleBitly of client_id and client_secret provided by Bitly
$bitly = new simpleBitly("client_id", "client_secret");

// if the URI contains the "code" query element, let's proceed. We only see then on the callback.
if (isset($_GET['code'])) {

	$code = $_GET['code'];
	
	try {
		
		// get the acesss token from Bitly after passing it the code and callback_uri. IMPORTANT: The callback URI must be the same set in the API registration on bitly
		$access_token = $bitly->getToken($code, "callback_uri_here");
		
		// set the token to our bitly object
		$bitly->setToken($access_token);

		// make an oauth2 get request with the paramaters that you want to send as an array
		$data = $bitly->get("/user/link_history", array("limit" => 50));
		
		// have a beer, because you got your data
		print_r($data);
		
	}
	// if there an error, let's catch it and do something with it. 
	catch (BitlyException $e) {
		echo $e->getMessage();
	}
}
// if we don't have a the "code" in the URL, we want to generate a URL to send to Bitly to authorize.
else {
	
	// go grab that URL - make sure you have set your client ID. IMPORTANT: The callback URI must be the same set in the API registration on bitly
	$authURL = $bitly->getAuthorizeURL("callback_uri_here");
	
	// to infinitty and beyond!
	header("Location: " . $authURL);
}



?>