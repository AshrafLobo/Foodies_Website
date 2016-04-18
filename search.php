<?php
define('DB_USER', 'User');
define('DB_PASSWORD', 'GcrZ9FGmdrzHsYpW');
define('DB_SERVER', 'localhost');
define('DB_NAME', 'foodies');


if (!$db = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME)) {
	die($db->connect_errno.' - '.$db->connect_error);
}

$arr = array();

if (!empty($_POST['keywords'])) {
	$keywords = $db->real_escape_string($_POST['keywords']);
	$sql = "SELECT name,id FROM products WHERE name LIKE '%".$keywords."%'";
	$result = $db->query($sql) or die($mysqli->error);
	if ($result->num_rows > 0) {
		while ($obj = $result->fetch_object()) {
			$arr[] = array('id' => $obj->id, 'name' => $obj->name);
		}
	}
}
echo json_encode($arr);
