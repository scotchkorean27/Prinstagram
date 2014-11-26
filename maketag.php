<html>
<head>
</head>
<body>
<?php
include "connectdb.php";
include "session.php";
include "security.php";
session_start();
if(!isset($_SESSION["username"])){
	header("location: login.php");
	echo "<a href=\"login.php\">You Must Login</a><br \>";
}
else{
	$pid = $_POST["pid"];
	$tagger = $_SESSION["username"];
	if(isset($_POST["taggee"])){
		$taggee = $_POST["taggee"];
		if(!isUserNameValid($taggee)){
			echo "Not a valid username!<br>";
		}
		else if($stmt = $mysqli->prepare("select image from photo left join (shared natural join inGroup) on photo.pid = shared.pid where photo.pid = ? and (username = ? or is_pub = 1)")){
			$stmt->bind_param("is", $pid, $taggee);
			$stmt->execute();
			$stmt->bind_result($img);
			if($stmt->fetch()){
				$stmt->close();
				if($stmt = $mysqli->prepare("SELECT pid from tag where pid = ? and tagger = ? and taggee = ?")){
					$stmt->bind_param("iss", $pid, $tagger, $taggee);
					$stmt->execute();
					$stmt->bind_result($tmpid);
					if($stmt->fetch()){
						$stmt->close();
						echo "This person has already been tagged!<br>";
					}
					else{
						$stmt->close();
						if($tagger == $taggee){
							if($stmt = $mysqli->prepare("INSERT INTO tag values (?, ?, ?, NOW(), 1)")){
								$stmt->bind_param("iss", $pid, $tagger, $taggee);
								$stmt->execute();
								$stmt->close();
								echo "You have been tagged! Tag another?<br>";
							}
						}
						else if($stmt = $mysqli->prepare("INSERT INTO tag values (?, ?, ?, NOW(), 0)")){
							$stmt->bind_param("iss", $pid, $tagger, $taggee);
							$stmt->execute();
							$stmt->close();
							echo $taggee . " has been tagged!  Tag another?<br>";
						}
					}


				}
			}
			else{
				echo "Your taggee is not allowed to view this image!<br>";
			}
		}
	}
	if($stmt = $mysqli->prepare("select image from photo left join (shared natural join inGroup) on photo.pid = shared.pid where photo.pid = ? and (username = ? or is_pub = 1)")){
		$stmt->bind_param("is", $pid, $tagger);
		$stmt->execute();
		$stmt->bind_result($img);
		if($stmt->fetch()){
			//then show the picture
			echo '<img src = "' . $img . '" alt = "Picture"><br>';
			//present a textbox and prompt for username
			echo "Who would you like to tag?  Enter their username:<br>";
			echo '<form action = "maketag.php" method = "POST">';
			echo '<input type = "hidden" name = "pid" value = "' . $pid . '">';
			echo '<input type = "text" name = "taggee"><br>';
			echo '<input type = "submit" value = "Tag this person!"><br>';
			echo '</form>';
		}
		else{
			echo "You are not allowed to view this image!<br>";
		}
		$stmt->close();
	}
	else{
		throw new Exception("Database is borked.  Try again later.");
	}
	echo '<form action = "index.php" method = "get">';
	echo '<br><input type = "submit" value = "Go Back">';
	echo '</form>';
}


?>
</body>
</html>
