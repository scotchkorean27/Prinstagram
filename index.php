<html>
<head>
</head>

<body>
<?php
include "connectdb.php";
include "session.php";
include "pics.php";

if(!isset($_SESSION["username"])){
	header("location: login.php");
	echo "<a href=\"login.php\">You Must Login</a><br \>";
}
else{
	//TODO add more links
	//TODO add tag notices
	echo "<a href=\"logout.php\"> logout </a>";
	echo "<a href=\"friends.php\"> Manage Friends </a>";
	echo "<a href=\"picpost.php\"> Upload Photos </a><br/>";

	loadPics();
}
?>

</body>
</html>
