<?php
$mysqli = new mysqli("localhost", "PrinstagramRoot", "wMTFGAxuJYjMAUbz", "Prinstagram");

/* check connection */
if (mysqli_connect_errno()) {
	throw new Exception("MySQL login failed");
}
?>
