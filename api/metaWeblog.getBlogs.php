<?php
$api_endpoint = 'https://www.sonub.com/xmlrpc.php';
$api_id = 'thruthesky@adwriter.com';
$api_password = 'asdf99';

$api_endpoint = 'https://api.blog.naver.com/xmlrpc'; // use your blog API instead
$api_id = 'fulljebi';
$api_password = '2ba5d640e614f5616011b0736a3fea51';

require_once 'src/Autoloader.php';
\PhpXmlRpc\Autoloader::register();
use PhpXmlRpc\Value;
use PhpXmlRpc\Request;
use PhpXmlRpc\Client;

$client = new Client( $api_endpoint );
$request = new Request('blogger.getUsersBlogs',
	array(
		new Value( md5('key') , "string"),
		new Value( $api_id , "string"),
		new Value( $api_password , "string")
	)
);
$response = $client->send( $request );

if ( $response->faultCode() ) {
	print "Fault <BR>";
	print "Code: " . htmlentities($response->faultCode()) . "<BR>" .
	      "Reason: '" . htmlentities($response->faultString()) . "'<BR>";
}
else {
	echo 'Success<hr>';
	// di($response->val); Response 클래스의 바로 아래에 Value 클래스 객체가 들어가 있다.
	foreach ( $response->val as $valueArrays ) {
		foreach ( $valueArrays as $values ) {
			foreach ( $values as $value ) {
				print_r( $value . "\n" );
			}
		}
	}

	$f = $response->val[0]->me['struct'];
	print_r($f);
	if ( isset($f['isAdmin']) ) print_r($f['isAdmin']->me['boolean']);
	if ( isset($f['url']) ) print_r("\nurl: " . $f['url']->me['string']);
	if ( isset($f['blogid']) ) print_r("\nblogId: " . $f['blogid']->me['string']);
	if ( isset($f['blogName']) ) print_r("\nblogName: " . $f['blogName']->me['string']);
	if ( isset($f['xmlrpc']) ) print_r($f['xmlrpc']->me['string']);
	echo "\n";
}

