<?php

// Add the simpleBitly Library
include ("lib/simpleBitly.php");

// create an empty instance of simpleBitly (no need to add client ID client Secret as these are basic)
$bitly = new simpleBitly();

// Set the username and API Key for the call
$bitly->setBasic("username", "API KEY");

// make the call to shorten or any other call, and pass in the array if needed of arguements. If the call fails, use BitlyException to track it.
try {
	$data = $bitly->get_basic("/shorten", array("longUrl" => "http://google.com"));
	
	// time for beer, because here is the response
	print_r($data);
}
catch (BitlyException $e) {
	echo $e->getMessage();
}



?>