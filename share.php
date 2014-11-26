<html>
<head>
</head>
<body>
<?
include "connectdb.php";
include "session.php";
include "pics.php";

function update(){
	global $mysqli, $_GET, $_SESSION;

	if(isset($_GET["makePrivate"])){
		if($stmt = $mysqli->prepare("UPDATE photo SET is_pub = 0 WHERE pid = ? && poster = ?")){
			$stmt->bind_param("is", $_GET["pid"], $_SESSION["username"]);
			$stmt->execute();
			$stmt->close();
		}
	}

	if(isset($_GET["makePublic"])){
		if($stmt = $mysqli->prepare("UPDATE photo SET is_pub = 1 WHERE pid = ? && poster = ?")){
			$stmt->bind_param("is", $_GET["pid"], $_SESSION["username"]);
			$stmt->execute();
			$stmt->close();
		}
	}

	if(isset($_GET["target"])){
		if($stmt = $mysqli->prepare("SELECT pid FROM shared WHERE pid = ? && gname = ? && ownername = ?")){
			$stmt->bind_param("iss",$_GET["pid"],$_GET["target"],$_SESSION["username"]);
			$stmt->execute();
			$stmt->bind_result($trash);
			$exists = $stmt->fetch();
			$stmt->close();
			if($exists){
				if($stmt = $mysqli->prepare("DELETE FROM shared WHERE pid = ? && gname = ? && ownername = ?")){
					$stmt->bind_param("iss",$_GET["pid"],$_GET["target"],$_SESSION["username"]);
					$stmt->execute();
				}
			}
			else{
				if($stmt = $mysqli->prepare("INSERT INTO shared (pid, gname, ownername) VALUES (?, ?, ?)")){
					$stmt->bind_param("iss",$_GET["pid"],$_GET["target"],$_SESSION["username"]);
					$stmt->execute();
				}
			}
		}
	}
}

function privatize($PID){

	echo "<form action=\"share.php\" method=\"GET\">\n";
	echo "<input type=\"hidden\" name=\"pid\" value=\"$PID\" />\n";
	echo "<input type=\"hidden\" name=\"makePrivate\" value=1 />\n";
	echo "<button type=\"submit\"> Make Private </button>\n";
	echo "</form>\n";
}

function publicize($PID){
	echo "<form action=\"share.php\" method=\"GET\">\n";
	echo "<input type=\"hidden\" name=\"pid\" value=\"$PID\" />\n";
	echo "<input type=\"hidden\" name=\"makePublic\" value=1 />\n";
	echo "<button type=\"submit\"> Make Public </button>\n";
	echo "</form>\n";
}

function share($PID){
	global $mysqli, $_SESSION;
	echo "<form action=\"share.php\" method=\"GET\">";
	echo "<input type=\"hidden\" name=\"pid\" value=\"$PID\" />";
	echo "<select name=\"target\">";

	echo "<option value=\"\"></option>\n";//a blank default is nice and makes the new group bit easier
	if ($stmt = $mysqli->prepare("SELECT gname FROM friendGroup WHERE ownername = ?")) {
		$stmt->bind_param("s", $_SESSION["username"]);
		$stmt->execute();
		$stmt->bind_result($GNOption);
		while($stmt->fetch()) {
			$GNOption = htmlspecialchars($GNOption);
			echo "<option value='$GNOption'>$GNOption</option>\n";
		}
		$stmt->close();
	}
	echo "</select>\n";
	echo "<button type=\"submit\"> Toggle Sharing </button>\n";
	echo "</form>";
}


update();
$query = "SELECT * FROM photo WHERE poster = ?";
if($stmt = $mysqli->prepare($query)){
	$stmt->bind_param("s", $_SESSION["username"]);
	$stmt->execute();
	$stmt->bind_result($PID, $poster, $caption, $date, $lon, $lat, $loc, $public, $img_file_name);
	$stmt->store_result();
	while($stmt->fetch()){
		echo "<div style=\"width: 270px; float: left;\">";
		echoImage($img_file_name, $poster, $caption, $date, $lon, $lat, $loc, $img_file_name);
		if($public != 0){
			privatize($PID);
		}
		else{
			publicize($PID);
			share($PID);
		}
		echo "</div>";
	}
}

?>
</body>
</html>
