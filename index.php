<html>
<head>
</head>

<body>
	<?php
	include "connectdb.php";
	include "session.php";

	if(!isset($_SESSION["username"])){
		//header("location: login.php");
		echo "<a href=\"login.php\">You Must Login</a><br \>";
	}
	else{
		//TODO show some pictures
		echo "donebuns <br />";
		echo "<a href=\"logout.php\"> logout </a>";
	}
	?>
	<h1>It is loaded</h1>
</body>
</html>
