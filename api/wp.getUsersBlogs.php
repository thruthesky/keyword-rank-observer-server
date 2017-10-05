<?php

require_once 'src/Autoloader.php';
\PhpXmlRpc\Autoloader::register();
use PhpXmlRpc\Value;
use PhpXmlRpc\Request;
use PhpXmlRpc\Client;

$client = new Client('https://www.sonub.com/xmlrpc.php'); // 워드프레스 블로그 테스트
$response = $client->send(new Request('wp.getUsersBlogs', // api 메소드
	array(
		new Value( "thruthesky@adwriter.com" , "string"), // 입력 값. 첫번째 인자.
		new Value( "asdf99" , "string") // 입력 값. 두번째 인자.
	)
));
if ( $response->faultCode() ) {
	print "Fault <BR>";
	print "Code: " . htmlentities($response->faultCode()) . "<BR>" .
	      "Reason: '" . htmlentities($response->faultString()) . "'<BR>";
}
else {
	echo 'Success';
}

