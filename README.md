===========
A Simple PHP Library to interfact with Bitly API V3 (OAuth and Basic)

# simpleBitly Bitly API Wrapper

This library is written to make calls against the Bitly API V3 (oauth and basic).<br />

# Requirements
PHP 5+<br />
CURL<br />

# Getting Started
Follow the instructions in <code>basic_example.php</code> for a detailed example of <code>basic</code> method and <code>oauth_example.php</code> for the <code>oauth2</code> authentication. There are also instructions on Bitly's Developer Portral (http://dev.bitly.com)

<br />It's easy to make a call to the Bity API. Just include <code>simpleBitly.php</code> in your script and use the following examples below.

For oauth2 requests:
<pre>
$bitly = new simpleBitly(CLIENT_ID_, CLIENT_SECRET, ACCESS_TOKEN);
$data = $bitly->get("/expand", array("shortUrl" => "http://bit.ly/1RmnUT");
</pre>

For oauth2 requests:
<pre>
$bitly = new simpleBitly();
$bitly->setBasic(USERNAME, APIKEY)
$data = $bitly->get_basic("/expand", array("shortUrl" => "http://bit.ly/1RmnUT");
</pre>

# Error Handling
This class also contains custom error handling so it recommended that you take a look at the example files to see how it's implemented. It's recommended that you wrap all your queries in <code>try / catch</code> statements like below:

<pre>
try {
	$data = $bitly->get("/expand", array("shortUrl" => "http://bit.ly/1RmnUT");
}	
catch (BitlyException $e) {
	echo $e->getMessage();
}
</pre>


# Getting Help
If you need help or have questions, please contact Greg Avola on Twitter at http://twitter.com/gregavola