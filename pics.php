<?php
include "connectdb.php";

function loadPublicPics(){
	//PHP has some of the dumbest scoping imaginable
	global $mysqli;
	
	//load the public ones first (it's easier)
	if($stmt = $mysqli->prepare("SELECT * FROM photo WHERE is_pub != 0")){
		$stmt->execute();
		$stmt->bind_result($PID, $poster, $caption, $date, $lon, $lat, $loc, $public, $img_url);
		while($stmt->fetch()){
			echo "<img src=\"./assets/$img_url\" /><br />";
		}
	}
}

function loadPics(){
	loadPublicPics();
}

loadPics();

?>
