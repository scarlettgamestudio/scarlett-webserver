<?php

define('SERVICE_PROTOCOL', 'https://');
define('SERVICE_SERVERNAME', 'anlagehub.com');

define('DB_SERVERNAME', SERVICE_SERVERNAME);
define('DB_USERNAME', '<database_user>');
define('DB_PASSWORD', '<database_password>');
define('DB_NAME', '<database_name>');

define('SERVICE_NAMESPACE', SERVICE_PROTOCOL . SERVICE_SERVERNAME . '/scarlett_ws/service.php?wsdl');
define('ERROR_LOG_FILE', './error.log');

define('SERVICE_RESULT_NAME', "result");
define('SERVICE_RESULT_DATA_NAME', "data");

?>
