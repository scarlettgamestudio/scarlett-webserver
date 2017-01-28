<?php

abstract class ResultType {
  const Success = 0;
  const InvalidCredentials = 1;
  const ApiError = 2;
  const EmailNotVerified = 3;
  const NoInformation = 4;
  const InvalidParameters = 5;
  const NotAuthorized = 6;
}

function system_result($type) {
  $response = array();
  $response["code"] = $type;

  switch($type) {
    case ResultType::Success :
      $response["message"] = "Success call";
      break;
    case ResultType::ApiError :
      $response["message"] = "Api Internal Error";
      break;
    case ResultType::InvalidCredentials :
      $response["message"] = "Not Authorized";
      break;
    case ResultType::EmailNotVerified :
      $response["message"] = "Email is yet to be verified";
      break;
    case ResultType::NoInformation :
      $response["message"] = "No information available";
      break;
    case ResultType::InvalidParameters :
      $response["message"] = "Invalid parameters";
      break;
    case ResultType::NotAuthorized :
      $response["message"] = "Not authorized";
      break;
  }

  return $response;
}

?>