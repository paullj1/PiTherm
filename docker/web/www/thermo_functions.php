<?php

function query_db($con, $query) {
  return mysqli_query($con,$query);
}

function fetch_array_db($result) {
  return mysqli_fetch_array($result);
}

function fetch_assoc_db($result) {
	return mysql_fetch_assoc($result);
}

?>
