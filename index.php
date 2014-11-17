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
		//TODO show some pictures
		
		loadPics();
		
		echo "<a href=\"logout.php\"> logout </a>";
	}
	?>

	<img src="./assets/newtest.png" />
</body>
</html>
