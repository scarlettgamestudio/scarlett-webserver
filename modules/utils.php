<?php

function get_guid(){
  if (function_exists('com_create_guid') === true)
      return trim(com_create_guid(), '{}');

  return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
    mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535),
    mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535),
    mt_rand(0, 65535), mt_rand(0, 65535));
}

// converts a mysql result to a php array
function m_array($db, $sql) {
  $call = array();
  $result = $db->execute_query($sql);
  if($result) {
    while($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
      array_push($call, $row);
    }
  }
  return $call;
}

?>
