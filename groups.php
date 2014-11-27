<html>
<head>
</head>
<body>
<?php

include "connectdb.php";
include "session.php";
include "security.php";

function addFriend($un, $gname){
	global $mysqli, $_SESSION;
	//check if this person is already in the group
	if($stmt = $mysqli->prepare("SELECT gname 
				FROM inGroup 
				WHERE ownername = ? 
				&& gname = ? 
				&& username = ?"))
	{
		$stmt->bind_param("sss", $_SESSION["username"], $gname, $un);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows > 0){
			echo "That person is already a friend!<br>";
			return;	//I don't need or want to do anything if they have a friend of that name
		}
	}
	$stmt->close();
	//we have ensured that the there is not alread an entry (very sterling-style)
	if($stmt = $mysqli->prepare("INSERT INTO `inGroup`(`ownername`, `gname`, `username`) VALUES (?, ?, ?)")){
		if($stmt->bind_param("sss", $_SESSION["username"], $gname, $un)){
			$stmt->execute();
		}
	}
	$stmt->close();
	echo $un . " has been added!<br>";
}

function removeFriend($un, $gname){
	global $mysqli, $_SESSION;
	if($stmt = $mysqli->prepare("select pid from tag where taggee = ? and pid in ( select pid from shared natural join inGroup where ownername = ? and gname = ?) and pid not in (select pid from shared natural join inGroup where ownername = ? and gname <> ? and username = ?)")){
		$stmt->bind_param("ssssss", $un, $_SESSION["username"], $gname, $_SESSION["username"], $gname, $un);
		$stmt->execute();
		$stmt->bind_result($pid);
		$pids[0] = 0;
		$ind = 0;
		while($stmt->fetch()){
			$pids[$ind] = $pid;
			$ind = $ind + 1;
		}
		$stmt->close();
		for($x = 0; $x < count($pids); $x++){
			if($stmt = $mysqli->prepare("delete from tag where pid = ? and taggee = ?")){
				$stmt->bind_param("is", $pids[$x], $un);
				$stmt->execute();
				$stmt->close();
			}
		}
		if($stmt = $mysqli->prepare("delete from inGroup where ownername = ? and gname = ? and username = ?")){
			$stmt->bind_param("sss", $_SESSION["username"], $gname, $un);
			$stmt->execute();
			$stmt->close();
		}
	}
	echo $un . " has been removed from group " . $gname."<br>";

}


session_start();
if(!isset($_SESSION["username"])){
	header("location: login.php");
	echo "<a href=\"login.php\">You Must Login</a><br \>";
}
else{
	if(isset($_POST["defriend"])){
		removeFriend($_POST["target"], $_POST["gname"]);

	}
	else if(isset($_POST["nfriend"])){
		if( isUserNameValid($_POST["fname"]) && isUserNameValid($_POST["lname"]) ){
			$stmt = $mysqli->prepare("SELECT username FROM person WHERE fname = ? && lname = ?");
			if($stmt){
				$stmt->bind_param("ss",$_POST["fname"], $_POST["lname"]);
				$stmt->execute();
				$stmt->bind_result($un);
				$stmt->store_result();
				if($stmt->num_rows == 0){
					//not found
					echo "That person doesn't exist.<br />\n";
				}
				else if($stmt->num_rows == 1){
					//got our guy
					$stmt->fetch();
					addFriend($un, $_POST["gname"]);
				}
				else if($stmt->num_rows > 1){
					//too many results
					//check if there is unadd
					if(isset($_POST["unt"])){
						addFriend($_POST["unt"], $_POST["gname"]);
					}
					else{
						echo '<form action="groups.php" method="POST">';
						echo '<input type="hidden" name="gname" value="'.$_POST["gname"].'">';
						echo '<input type="hidden" name="fname" value="'.$_POST["fname"].'">';
						echo '<input type="hidden" name="lname" value="'.$_POST["lname"].'">';
						echo "Please Select By Username: <select name=\"unt\">\n";
						while($stmt->fetch()){
							$option = htmlspecialchars($un);
							echo "<option value=\"$option\">$option</option>\n";
						}
						echo '</select>';
						echo '<input type = "submit" name = "nfriend" value = "Add friend"><br><br>';
						echo '</form>';
					}
				}
				$stmt->close();
			}
		}


	}
	if(isset($_POST["ngroup"])){
		if(isGroupNameValid($_POST["gname"]) && $_POST["gname"] != "" && isGroupDescValid($_POST["desc"])){
			$gname = htmlspecialchars($_POST["gname"]);
			$desc = htmlspecialchars($_POST["desc"]);
			if($stmt = $mysqli->prepare("SELECT gname from friendGroup where gname = ? and ownername = ?")){
				$stmt->bind_param("ss", $gname, $_SESSION["username"]);   
				$stmt->execute();
				$stmt->bind_result($tmpnm);
				if($stmt->fetch()){
					$stmt->close();
					echo "This group already exists!<br>";
				}
				else{
					$stmt->close();
					if($stmt = $mysqli->prepare("INSERT INTO friendGroup values (?, ?, ?)")){
						$stmt->bind_param("sss", $gname, $desc, $_SESSION["username"]);
						$stmt->execute();
						$stmt->close();
						echo "Group has been created!<br>";
					}
				}
			}
		}
		else{
			echo "Try entering valid fields next time you dummy";
		}
	}
	if(isset($_POST["manage"]) || isset($_POST["nfriend"]) || isset($_POST["defriend"])){
		echo "Currently managing " . $_POST["gname"] . "<br>";
		echo "Add new person:<br>";
		$gname = $_POST["gname"];
		echo '<form action = "groups.php" method = "POST">';
		echo "First Name:".'<input type = "text" name = "fname"><br>';
		echo "Last Name: ".'<input type = "text" name = "lname"><br>';
		echo '<input type = "submit" name = "nfriend" value = "Add friend"><br><br>';
		echo "Here are all the friends in your group<br>";


		if($stmt = $mysqli->prepare("SELECT fname, lname, username
					FROM person NATURAL JOIN (
						SELECT username
						FROM inGroup
						WHERE gname = ? and ownername = ?
						) as mems")){
						$stmt->bind_param("ss", $_POST["gname"], $_SESSION["username"]);
		$stmt->execute();
		$stmt->bind_result($fname, $lname, $un);
		while($stmt->fetch()){
			if($un != $_SESSION["username"]){
				echo '<form action="groups.php" method="POST"><br>';
				echo '<input type="hidden" name= "gname" value="'.$_POST["gname"].'">';
				echo '<input type="hidden" name= "target" value="'.$un.'">';
				echo "$fname $lname (" . $un . ") \n";
				echo '<input type = "submit" name = "defriend" value = "Defriend">';
				echo "</form>\n";
			}
		}
	}

	}
	else if(isset($_POST["remove"])){
		if($stmt = $mysqli->prepare("SELECT fname, lname, username
					FROM person NATURAL JOIN (
						SELECT username
						FROM inGroup
						WHERE gname = ? and ownername = ?
						) as mems")){
						$stmt->bind_param("ss", $_POST["gname"], $_SESSION["username"]);
		$stmt->execute();
		$stmt->bind_result($fname, $lname, $un);
		$ind = 0;
		while($stmt->fetch()){
			$mems[$ind] = $un;
			$ind++;
		}
		$stmt->close();
		for($x = 0; $x < count($mems); $x++){
			removeFriend($mems[$x], $_POST["gname"]);
		}
		if($stmt = $mysqli->prepare("delete from shared where gname = ? and ownername = ?")){
			$stmt->bind_param("ss", $_POST["gname"], $_SESSION["username"]);
			$stmt->execute();
			$stmt->close();
		}
		if($stmt = $mysqli->prepare("delete from friendGroup where gname = ? and ownername = ?")){
			$stmt->bind_param("ss", $_POST["gname"], $_SESSION["username"]);
			$stmt->execute();
			$stmt->close();
			echo $_POST["gname"] . " has been removed!<br>";
		}
	}
	}    
	else if(isset($_POST["make"]) || isset($_POST["ngroup"])){
		echo "Enter the details of your group here.<br>";
		echo '<form action = "groups.php" method = "POST">';
		echo 'Group Name: <input type = "text" name = "gname"><br>';
		echo 'Description: <input type = "text" name = "desc"><br>';
		echo '<input type = "submit" name = "ngroup" value = "Create!">';
		echo '</form>';

	}
	if(!(isset($_POST["manage"]) || isset($_POST["make"]) || isset($_POST["nfriend"]) || isset($_POST["defriend"]) || isset($_POST["make"]) || isset($_POST["ngroup"]))){
		$username = $_SESSION["username"];
		echo "Please choose a group to manage.<br>";
		echo '<form action = "groups.php" method = "POST">';
		echo '<select name = "gname">';
		if ($stmt = $mysqli->prepare("select distinct gname from friendGroup where ownername=?")) {
			$stuff = $_SESSION["username"];
			$stmt->bind_param("s", $stuff);
			$stmt->execute();
			$stmt->bind_result($gname);;
			while($stmt->fetch()) {
				$gname = htmlspecialchars($gname);
				echo "<option value='$gname'>$gname</option>\n";	
			}
			$stmt->close();
			echo '</select>';
			echo '<br><input type = "submit" name = "manage" value = "Manage this group"><br>';
			echo '<input type = "submit" name = "remove" value = "Remove this group"><br>';
			echo '<input type = "submit" name = "make" value = "Create new group"><br>';
			echo '</form>';
		}
	}
	else{
		echo '<form action = "groups.php" method = "POST">';
		echo '<br><input type = "submit" value = "Back to groups">';
		echo '</form>';
	}        
	echo '<form action = "index.php" method = "get">';
	echo '<br><input type = "submit" value = "Go Back">';
	echo '</form>';




}
?>
</body>
</html?
