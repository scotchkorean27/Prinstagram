<html>
<head>
</head>
<body>
<?
include "connectdb.php";
include "session.php";
include "pics.php";

function privatize($PID){
	global $mysqli, $_GET, $_SESSION;
	if(isset($_GET["makePrivate"])){
		//TODO delete the row
	}
	else{
		echo "<form action=\"share.php\" method=\"GET\" />\n";
		echo "<input type=\"hidden\" name=\"pid\" value=\"$PID\" />\n";
		echo "<input type=\"hidden\" name=\"makePrivate\" value=1 />\n";
		echo "<button type=\"submit\"> Make Private <\button>\n";
		echo "</form>\n";
	}
}

function publicize(){

}

function share(){

}

function unshare(){

}

$query = "SELECT * FROM photo WHERE poster = ?";
if($stmt = $mysqli->prepare($query)){
	$stmt->bind_param("s", $_SESSION["username"]);
	$stmt->execute();
	$stmt->bind_result($PID, $poster, $caption, $date, $lon, $lat, $loc, $public, $img_file_name);
	while($stmt->fetch()){
		echoImg($img_file_name, $poster, $caption, $date, $lon, $lat, $loc, $img_file_name);
		if($public != 0){
			privatize($PID);
		}
	}
}

?>
</body>
</html>
