<html>
<body>

<?php
include "pics.php";
include "security.php";
include "session.php";
include "connectdb.php";


if(!isset($_SESSION["username"])){
	header("location: login.php");
}
else{
	//makes sure the viewer is permited to see the thing
	$query = "select image from photo left join (shared natural join inGroup) on photo.pid = shared.pid where photo.pid = ? and (username = ? or is_pub = 1)";
	if($stmt = $mysqli->prepare($query)){
		$stmt->bind_param("is",$_GET["pid"], $_SESSION["username"]);
		$stmt->execute();
		if(!$stmt->fetch()){
			header("location: index.php");
		}
		$stmt->close();
	}
	
	//we have ensure viewability now
	//TODO echo the image
}

?>


</body>
</html>

