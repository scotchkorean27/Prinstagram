<html>
<head></head>
<body>

<?
include "connectdb.php";
include "session.php";

function createSelectionMenu(){
	global $mysqli, $_SESSION;

	echo "<form action=\"friends.php\" method=\"GET\" >\n<select name=\"gname\">";
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
	echo "<button type=\"submit\">Select</button>\n";
	echo "</form><br />\n";
}

function createNewGroupMenu(){
	global $mysqli, $_SESSION;
	echo "<form action=\"friends.php\" method=\"GET\">\n";
	echo "Group Name: <input type=\"text\" cols=\"30\" name=\"gname\" /><br />\n";
	echo "Description: <input type=\"text\" cols=\"30\" name=\"desc\" /><br />\n";
	echo "<input type=\"hidden\" name=\"newGroup\" value=\"1\" />\n";
	echo "<button type=\"submit\">Create</button>\n";
	echo "</form>\n";
}

function createFriendEditor(){
	global $_GET, $_SESSION, $mysqli;	
	
	//makes a new freindGroup row if it is told to do so
	if(isset($_GET["newGroup"])){
		//make a new group before engaging the editing
		$query = "INSERT INTO `friendGroup` (`gname`, `descr`, `ownername`) VALUES (?, ?, ?);";
		$stmt = $mysqli->prepare($query);
		if($stmt){
			$stmt->bind_param("sss", $_GET["gname"], $_GET["desc"], $_SESSION["username"]);
			$stmt->execute();
			$stmt->close();
			//I think i'm done...
		}
	}
	
	//deletes freinds as needed
	if(isset($_GET["target"])){
		$query = "DELETE FROM inGroup WHERE ownername = ? && gname = ? && username = ?";
		if($stmt = $mysqli->prepare($query)){
			$stmt->bind_param("sss", $_SESSION["username"], $_GET["gname"], $_GET["target"]);
			$stmt->execute();
		}
	}
	
	//when a group is selected generate a list of members
	$query="SELECT fname, lname, username
		FROM person NATURAL JOIN (
				SELECT username
				FROM inGroup
				WHERE gname = ? && ownername = ?
				) as mems";
	if($stmt = $mysqli->prepare($query)){
		$stmt->bind_param("ss", $_GET["gname"], $_SESSION["username"]);
		$stmt->execute();
		$stmt->bind_result($fname, $lname, $un);
		while($stmt->fetch()){
			echo "<form action=\"friends.php\" method=\"GET\">\n";
			echo "<input type=\"hidden\" name=\"gname\" value=\"{$_GET["gname"]}\" />\n";
			echo "<input type=\"hidden\" name=\"target\" value=\"$un\" />\n";
			echo "$fname $lname\n";
			echo "<button type=\"submit\"> Defriend </button><br />\n";
			echo "</form><br />\n";
		}
	}

	//add friend box/button
	

}

if(isset($_SESSION["username"])){
	//display a menu of groups	
	createSelectionMenu();
	createNewGroupMenu();

	if(isset($_GET["gname"])){
		createFriendEditor();
	}
}
else{
	header("location: index.php");
}

?>

</body>
</html>
