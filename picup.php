<!DOCTYPE html>

<html>
<?php

include "connectdb.php";
include "session.php";
include "security.php";

if($_FILES['fileToUpload']['error'] == UPLOAD_ERR_NO_FILE){
	header("location: picpost.php");
}  
else{
	$tdir = "assets/";
	$tfile = $tdir . basename($_FILES["fileToUpload"]["name"]);
	$tname = $_FILES["fileToUpload"]["name"];
	if(file_exists($tfile)){
		$ttfile = $tdir . $tname;
		$i = 0;
		while(true){
			$ttfile = $tdir. $i . $tname;
			if(file_exists($ttfile)){
				$i = $i + 1;
			}
			else{
				$tfile = $ttfile;
				break;
			}
		}
	} 


	if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $tfile)){
		//echo "Shared!";
		//this was a tad premature
		echo "file recieved<br />";
	}

	$abspath = "~/www/prinstagram/".$tfile;
	$pubvar = 0;
	if($_POST["gname"] == "Public"){
		$pubvar = 1;
	}
	if(isCaptionValid($_POST["caption"]) && (isCaptionValid($_POST["locname"]) || $_POST["locname"] == "")){
		if($stmt = $mysqli->prepare("INSERT INTO photo (poster, caption, pdate, lnge, lat, lname, is_pub, image) values (?, ?, NOW(), ?, ?, ?, ?, ?)")){
			$stmt->bind_param("ssddsis", $_SESSION["username"], $_POST["caption"], $_POST["lon"], $_POST["lat"], $_POST["locname"], $pubvar, $tfile);
			$stmt->execute();
			$stmt->close();
		}


		if($pubvar == 0){
			$pid = 0;
			if($stmt = $mysqli->prepare("select max(pid) from photo where poster = ?")){
				$stmt->bind_param("s", $_SESSION["username"]);
				$stmt->execute();
				$stmt->bind_result($pid);
				$stmt->fetch();
				$stmt->close();
			}  
			if($stmt = $mysqli->prepare("INSERT INTO shared (pid, gname, ownername) values (?, ?, ?)")){
				$stmt->bind_param("iss", $pid, $_POST["gname"], $_SESSION["username"]);
				$stmt->execute();
			}
		}

		echo '<br><br><img src = "'.$tfile.'" alt="Your Picture">';
		echo '<form action = "picpost.php" method = "get">';
		echo '<br><br><input type = "submit" value = "Back">';
		echo '</form>';
	}
	else{
		echo "Loction and caption names must not be longer than 100 characters.";
		echo "<br /><a href=\"picpost.php\"> Back </a>";
	}
}
?>
</html>
