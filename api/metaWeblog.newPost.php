<?php


if ( $json = xapi_get_json_post() ) $_REQUEST = array_merge( $_REQUEST, $json );


if ( ! isset($_REQUEST['username']) ) fail('input username');
if ( ! isset($_REQUEST['password']) ) fail('input password');
if ( ! isset($_REQUEST['endpoint']) ) fail('input endpoint');
if ( ! isset($_REQUEST['title']) ) fail('input title');
if ( ! isset($_REQUEST['description']) ) fail('input description');

require_once 'src/Autoloader.php';
\PhpXmlRpc\Autoloader::register();
use PhpXmlRpc\Value;
use PhpXmlRpc\Request;
use PhpXmlRpc\Client;

$date = PhpXmlRpc\Helper\Date::iso8601Encode(time());

$client = new Client( $_REQUEST['endpoint'] );
$request = new Request('metaWeblog.newPost',
	array(
		new Value( md5('key') , "string"),
		new Value( $_REQUEST['username'] , "string"),
		new Value( $_REQUEST['password'] , "string"),
		new Value( [
//			"categories" => new Value([ new Value('필리핀 스토리', 'string')], 'struct'),
			"description" => new Value($_REQUEST['description'], 'string'),
			"title" => new Value($_REQUEST['title'], 'string'),
			"dateCreated" => new Value($date)
		], "struct"),
		new Value( 1, 'boolean' )
	)
);

$response = $client->send( $request );

if ( $response->faultCode() ) {
	echo json_encode( ['code' => $response->faultCode(), 'message' => $response->faultString()]);
}
else {
	echo json_encode( ['code' => 0 ] );
}


function fail($msg) {
	echo json_encode(['code'=>-1, 'message' => $msg, 'method' => $_SERVER['REQUEST_METHOD']]);
	exit;
}


function xapi_get_json_post() {
	$json_params = file_get_contents( "php://input" );
	if ( strlen($json_params) > 0 ) {
		$dec = json_decode( $json_params, true );
		if ( json_last_error() == JSON_ERROR_NONE ) {
			return $dec;
		}
	}
	return null;
}
