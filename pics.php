<?php
include "connectdb.php";
include "session.php";

function echoImg($img_file_name){
	//TODO less awful display method
	echo "<img src=\"./assets/$img_file_name\" /><br />";
}

function loadSharedPics(){
	global $mysqli, $_SESSION;

	$query = "SELECT pid, poster, caption, pdate, lnge, lat, lname, image 
		FROM inGroup NATURAL JOIN shared NATURAL JOIN photo 
		WHERE username = ?";
	if($stmt = $mysqli->prepare($query)){
		$stmt->bind_param("s",$_SESSION["username"]);
		$stmt->execute();
		$stmt->bind_result($PID, $poster, $caption, $date, $lon, $lat, $loc, $img_file_name);
		while($stmt->fetch()){
			echoImg($img_file_name);
		}
	}
}

function loadPublicPics(){
	//PHP has some of the dumbest scoping imaginable
	global $mysqli;
	
	//load the public ones first (it's easier)
	if($stmt = $mysqli->prepare("SELECT * FROM photo WHERE is_pub != 0")){
		$stmt->execute();
		$stmt->bind_result($PID, $poster, $caption, $date, $lon, $lat, $loc, $public, $img_file_name);
		while($stmt->fetch()){
			
			echoImg($img_file_name);
		}
	}
}

function loadPics(){
	loadPublicPics();
	loadSharedPics();
}

//REMOVE!
//loadPics();
?>
