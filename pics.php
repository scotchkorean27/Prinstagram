<?php
include "connectdb.php";
include "session.php";

function echoImage($PID, $poster, $caption, $date, $lon, $lat, $loc, $img_file_name){
	global $mysqli;
	echo "<div style=\"width: 256px;padding: 5px;background-color: rgb(180, 180, 180);margin: 2px; float: left;\">";
	echo "<img src=\"$img_file_name\" height=256 width=256 />";
	echo "<p>";
	echo htmlspecialchars($caption);
	echo "</p>";
	echo "<p> Posted by: $poster, On: $date, At: $loc ($lon, $lat)</p>";
	echo "<p> Tagged: ";
	//echoing the tagged people
	$query = "SELECT fname, lname FROM tag JOIN person ON (taggee = username) WHERE pid = ?";
	if($stmt = $mysqli->prepare($query)){
		$stmt->bind_param("i", $PID);
		$stmt->execute();
		$stmt->bind_result($fn, $ln);
		while($stmt->fetch()){
			echo "$fn $ln ";
		}
		$stmt->close();
	}
	else{
		echo "no sql" . $mysqli->error;
	}


	echo "</p>";
	echo "<form action=\"maketag.php\" method=\"post\"><input type=\"hidden\" name=\"pid\" value=\"$PID\" /><button type=\"submit\"> Add Tags </button></form>";
	echo "</div>";
}

function loadPics(){
	global $mysqli, $_SESSION;
	$query = "select * from photo
			where pid in 
			(select pid from shared inner join inGroup
			 where username = ?)
			or is_pub = 1 or poster = ? order by pdate desc;";
	if($stmt = $mysqli->prepare($query)){
		$stmt->bind_param("ss", $_SESSION["username"], $_SESSION["username"]);
		$stmt->execute();
		$stmt->bind_result($PID, $poster, $caption, $date, $lon, $lat, $loc,$is_pub, $img_file_name);
		$stmt->store_result();
		while($stmt->fetch()){
			echoImage($PID, $poster, $caption, $date, $lon, $lat, $loc, $img_file_name);
		}
		$stmt->close();
	}
	else{
		echo "no mysql";
	}
}

//loadPics();
?>
