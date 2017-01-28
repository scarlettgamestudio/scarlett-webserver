<?php
// header definitions:
header('Content-Type: text/html; charset=utf-8');
header("Access-Control-Allow-Headers: SOAPAction,Content-Type");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods : POST,GET,OPTIONS");

// 3rd party includes:
require_once "./lib/nusoap.php";
require_once "./lib/password.php";

// project includes:
require_once "./config/settings.php";
require_once "./modules/mysql_connector.php";
require_once "./modules/encryption.php";
require_once "./modules/utils.php";
require_once "./modules/result.php";
require_once "./modules/request.php";
require_once "./modules/user.php";

// set logger:
ini_set("log_errors", 1);
ini_set("error_log", ERROR_LOG_FILE);

$server = new soap_server();
$server->configureWSDL("server", SERVICE_NAMESPACE);
$server->soap_defencoding = 'UTF-8';
$server->decode_utf8 = false;
$server->encode_utf8 = true;

// test function:
function ping() {
  return "Hello " . $_SERVER['REMOTE_ADDR'];
}

$server->register("ping",
                  array(), // parameters
                  array("return" => "xsd:string")); // return value(s)

// test function:
function request($request) {
  $data = array();
  $parsed = json_decode($request, true);
  
  // request body handler:
  if(!array_key_exists("data", $parsed) || !array_key_exists("action", $parsed)) {
    // invalid parameters, reject and tell why
    $data[SERVICE_RESULT_NAME] = system_result(ResultType::InvalidParameters);
  } else {
    // handle requests
    $requestData = $parsed["data"];

    if(!array_key_exists("token", $requestData)) {
      $data[SERVICE_RESULT_NAME] = system_result(ResultType::InvalidParameters);
    } else {
      switch($parsed["action"]) {
        case ActionRequest::Login:
          $data = user_login($requestData); 
          break;
        case ActionRequest::Register:
          $data = user_register($requestData);
          break;
        case ActionRequest::UpdateUserInfo:
          $data = user_update_info($requestData);
          break;
      }
    }
  }

  // return the processed data info encoded
  return json_encode($data);
}

$server->register("request",
                  array("request" => "xsd:string"), // parameters
                  array("return" => "xsd:string")); // return value(s)

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);

?>
