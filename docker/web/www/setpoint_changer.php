<?php

require_once "connect.php"; // Gives us $con
require_once "thermo_functions.php";
require_once "constants.php";

$id = $con->real_escape_string($_POST['id']);

$qry_str = 'SELECT  `value`,`description` FROM `status` WHERE `id`='.$id.';';
$entry = fetch_array_db(query_db($con, $qry_str));
$result['value'] = $entry['value'];
$result['label'] = $entry['description'];

echo json_encode($result);

?>
