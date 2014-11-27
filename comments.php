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
		$stmt->bind_result($img);
		if(!$stmt->fetch()){
			header("location: index.php");
		}
		$stmt->close();
	}
	
	//we have ensure viewability now
	echo "<img src=\"$img\" hieght=512 width=512/>";
	
	if(isset($_GET["text"]) ){
		if(isCaptionValid($_GET["text"])){
			$query = "INSERT INTO comment (ctime, ctext) VALUES (NOW(), ?)";
			if($stmt = $mysqli->prepare($query)){
				$stmt->bind_param("s", $_GET["text"]);
				$stmt->execute();
				$stmt->close();
			}

			$query = "SELECT LAST_INSERT_ID()";
			if($stmt = $mysqli->prepare($query)){
				$stmt->execute();
				$stmt->bind_result($cid);
				$stmt->fetch();
				$stmt->close();
			}

			$query = "INSERT INTO commentOn (cid, pid, username) VALUES (?, ?, ?)";
			if($stmt = $mysqli->prepare($query)){
				$stmt->bind_param("iis", $cid, $_GET["pid"], $_SESSION["username"]);
				$stmt->execute();
				$stmt->close();
			}
		}
		else{
			echo "caption empty or too long";
		}
	}

	//plop the comments in
	$query = "SELECT username, ctime, ctext FROM commentOn NATURAL JOIN comment WHERE pid = ?";
	if($stmt = $mysqli->prepare($query)){
		$stmt->bind_param("i", $_GET["pid"]);
		$stmt->execute();
		$stmt->bind_result($commentor, $ctime, $ctext);
		while($stmt->fetch()){
			//echo the comment
			echo "<p style=\"border: 2px solid black \">";
			echo "User, $commentor said on $ctime : <br />";
			echo htmlspecialchars($ctext);
			echo "</p>";
		}
	}

	//add a new comment box
	echo "<form action=\"comments.php\" method=\"get\">";
	echo "<input type=\"hidden\" name=\"pid\" value=\"{$_GET["pid"]}\" />";
	echo "<textarea name=\"text\" cols=50 rows=2>Comment Here </textarea><br />";
	echo "<button type=\"submit\"> Submit! Submit! Obey! </button>";
	echo "</form>";
	
}

?>

<form action="index.php"><button type="submit">Home</button></form>
</body>
</html>

