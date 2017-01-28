<?php

function user_register($params) {
  $db = new MySQLConnector();
  $data = array();

  if($db->open_db()) {
    $username = $db->escape_string($params["username"]);
    $password = $db->escape_string($params["password"]);
    $email = $db->escape_string($params["email"]);

    // valid fields?
    if(trim($username) == "" || trim($password) == "" || trim($email) == "") {
      $data[SERVICE_RESULT_NAME] = system_result(ResultType::InvalidParameters);
      $db->close_db();
      return $data;
    }

    $user_ip = $_SERVER['REMOTE_ADDR'];
    $encrypted_password = hash_encrypt($password);

    $sql = "INSERT INTO user (username, email, password, register_date, last_ip)
            VALUES ('$username', '$email', '$encrypted_password', UTC_TIMESTAMP(), '$user_ip')";

    if($db->execute_nonquery($sql)) {
      $data[SERVICE_RESULT_NAME] = system_result(ResultType::Success);
    } else {
      $data[SERVICE_RESULT_NAME] = system_result(ResultType::ApiError);
      $data["error"] = $db->get_error();
    }

    $db->close_db();
  } else {
      $data[SERVICE_RESULT_NAME] = system_result(ResultType::ApiError);
  }

  return $data;
}

function user_update_info($params) {
  $data = array();
  $updateSql = array();
  $db = new MySQLConnector(); 

  if($db->open_db()) {   
    // token validation
    $id_user = token_valid($db, $params["token"]);
    if($id_user <= 0) {
      $db->close_db();
      $data[SERVICE_RESULT_NAME] = system_result(ResultType::NotAuthorized);
      return $data;
    }

    // TODO: add more keys when necessary
    if (array_key_exists("displayName", $params)) {
      array_push($updateSql, "display_name = " + $db->escape_string($params["displayName"]));
    }

    if(count($updateSql) > 0) {
      // ok, we have some data to update..
      $updateSqlStr = "";
      for($i = 0; $i < count($updateSql); $i++) {
        if($i > 0) {
          $updateSqlStr .= ", ";
        }
        $updateSqlStr .= $updateSql[$i];
      }

      $sql = "UPDATE user u SET $updateSqlStr WHERE id_user = $id_user";
      
      if($db->execute_nonquery($sql)){
        $data[SERVICE_RESULT_NAME] = system_result(ResultType::Success);
      }
      else{
        $data[SERVICE_RESULT_NAME] = system_result(ResultType::ApiError);
      }
      
    } else {
      // no valid data to update, inform the invalid parameters:
      $data[SERVICE_RESULT_NAME] = system_result(ResultType::InvalidParameters);
    }

    $db->close_db();
  } else {
      $data[SERVICE_RESULT_NAME] = system_result(ResultType::ApiError);
  }

  return $data;
}

function user_login($params) {
  $db = new MySQLConnector();
  $data = array();

  if($db->open_db()) {
    $identity = $db->escape_string($params["identity"]);
    $password = $db->escape_string($params["password"]);

    $sql = "SELECT u.id_user, u.username, u.password, u.email, u.email_verified, u.display_name, u.avatar_url
            FROM user u WHERE (username = '$identity' OR email = '$identity')";

    $result = $db->execute_query($sql);
    if ($result->num_rows > 0) {
      $row = mysqli_fetch_array($result, MYSQL_ASSOC);
      if($row["email_verified"] == 0) {
        $data[SERVICE_RESULT_NAME] = system_result(ResultType::EmailNotVerified);
      }
      else if (password_verify($password, $row["password"])) {
        $id_user = $row["id_user"];

        // remove private fields from the array (for safety reasons):
        unset($row["password"]);
        unset($row["id_user"]);

        $data["userdata"] = $row;

        // in order to be more secure, a new token must be created for each
        // login.. even if its for the same user..
        $token = get_user_token($db, $id_user);

        if($token == "") {
          // no valid token, create..
          $token = create_user_token($db, $id_user);
        }

        // update user ip & date
        $sql = "UPDATE user
                SET last_ip = '" . $_SERVER['REMOTE_ADDR'] . "', last_login_date = UTC_TIMESTAMP()
                WHERE id_user = $id_user";
        $db->execute_nonquery($sql);

        if($token != "") {
          $data["usertoken"] = $token;
          $data[SERVICE_RESULT_NAME] = system_result(ResultType::Success);
        }
        else {
          // token fetch failed.
          $data[SERVICE_RESULT_NAME] = system_result(ResultType::ApiError);
        }
      } else {
        $data[SERVICE_RESULT_NAME] = system_result(ResultType::InvalidParameters);
      }
    } else {
      $data[SERVICE_RESULT_NAME] = system_result(ResultType::InvalidCredentials);
    }

    $db->close_db();
  } else {
      $data[SERVICE_RESULT_NAME] = system_result(ResultType::ApiError);
  }

  return $data;
}

function delete_token($db, $id_user) {
  $id_user = $db->escape_string($id_user);
  $sql = "DELETE FROM user_token WHERE id_user = $id_user";
  return $db->execute_nonquery($sql);
}

function get_user_token($db, $id_user) {
  $id_user = $db->escape_string($id_user);
  $user_ip = $_SERVER['REMOTE_ADDR'];
  $sql = "SELECT token, expiration_date FROM user_token WHERE
          id_user = $id_user AND expiration_date > NOW() AND user_ip = '$user_ip'
          ORDER BY id_user_token DESC";

  $result = $db->execute_query($sql);
  if($result->num_rows > 0) {
    // valid token already exists, use it:
    $row = mysqli_fetch_array($result, MYSQL_ASSOC);
    return $row["token"];
  }

  return "";
}

function create_user_token($db, $id_user) {
  $id_user = $db->escape_string($id_user);
  $guid = get_guid();
  //$encrypted_guid = hash_encrypt($guid);
  $user_ip = $_SERVER['REMOTE_ADDR'];

  $sql = "INSERT INTO user_token (id_user, token, expiration_date, register_date, user_ip)
          VALUES ($id_user, '$guid', DATE_ADD(NOW(), INTERVAL 90 DAY), NOW(), '$user_ip')";

  if($db->execute_nonquery($sql))
      return $guid;

  return "";
}

/*
  Validates if the user token is valid.
  If true, returns the user ID, if not, returns '-1'
*/
function token_valid($db, $token, $user_ip = "") {
  $token = $db->escape_string($token);

  if($user_ip == "")
    $user_ip = $_SERVER['REMOTE_ADDR'];
  else
    $user_ip = $db->escape_string($user_ip);

  $sql = "SELECT id_user, token FROM user_token
          WHERE expiration_date > NOW() AND user_ip = '$user_ip'
          ORDER BY id_user_token DESC";

  $result = $db->execute_query($sql);

  if($result->num_rows > 0) {
    // there can be multiple tokens for the same ip / account
    // for instance, it can be from mobile and desktop at the same time:
    while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
      if ($token == $row["token"]) {
        $id_user = $row["id_user"];

        // update last action
        $sql = "UPDATE user u SET last_action_date = NOW() WHERE u.id_user = $id_user";
        $db->execute_nonquery($sql);

        return $id_user;
      }
    }
  }

  return -1;
}

?>