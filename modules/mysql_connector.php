<?php

class MySQLConnector {
  private $db_conn = null;
  private $initialized = false;
  private $db_servername = "";
  private $db_username = "";
  private $db_password = "";
  private $db_name = "";

  private function initialize() {
    $this->db_servername = DB_SERVERNAME;
    $this->db_username = DB_USERNAME;
    $this->db_password = DB_PASSWORD;
    $this->db_name = DB_NAME;

    $this->initialized = true;
  }

  public function inserted_id() {
    return mysqli_insert_id($this->db_conn);
  }

  public function open_db() {
    if($this->initialized == false) {
      $this->initialize();
    }

    $this->db_conn = new mysqli($this->db_servername, $this->db_username, $this->db_password);

    // check connection :
    if($this->db_conn->connect_error) {
      error_log(mysqli_connect_error());
      return false;
    }

    mysqli_set_charset($this->db_conn, "utf8");

    if(!mysqli_select_db($this->db_conn, $this->db_name)) {
      error_log("could not select database (DB_NAME): " . mysqli_error($this->db_conn));
      return false;
    }

    return true;
  }

  public function select_db($target_db_name) {
    if(!mysqli_select_db($this->db_conn, $target_db_name)) {
      error_log("could not select database (DB_NAME): " . mysqli_error($this->db_conn));
      return false;
    }

    return true;
  }

  public function close_db() {
    if($this->db_conn != null && !$this->db_conn->connect_errno)  {
      $this->db_conn->close();
      $this->db_conn = null;

      return true;
    }

    return false;
  }

  public function escape_string($value) {
    // FIXME: add db_conn created validation so the code doesn't break without necessity
    return trim(mysqli_real_escape_string($this->db_conn, $value));
  }

  public function execute_nonquery($sql) {
    if ($this->db_conn->query($sql) === TRUE) {
      return true;
    } else {
      $error = mysqli_error($this->db_conn);
      error_log("Error in: $sql \nError Message: $error");
      return false;
    }
  }

  public function execute_query($sql) {
    return $this->db_conn->query($sql);
  }

  public function get_error() {
    return mysqli_errno($this->db_conn) . "_:_" . mysqli_error($this->db_conn);
  }
}

?>
