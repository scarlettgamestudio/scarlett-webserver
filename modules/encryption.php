<?php

function hash_encrypt($text) {
  return password_hash($text, PASSWORD_DEFAULT);
}

?>
