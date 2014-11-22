<html>
<head></head>
<body>

<?
include "connectdb.php";
include "session.php";

function createSelectionMenu(){
	global $mysqli, $_SESSION, $_GET;

	echo "<form action=\"friends.php\" method=\"GET\" >\n<select name=\"gname\">";
	echo "<option value=\"\"></option>\n";//a blank default is nice and makes the new group bit easier
	if ($stmt = $mysqli->prepare("SELECT gname FROM friendGroup WHERE ownername = ?")) {
		$stmt->bind_param("s", $_SESSION["username"]);
		$stmt->execute();
		$stmt->bind_result($GNOption);
		while($stmt->fetch()) {
			$GNOption = htmlspecialchars($GNOption);
			if(isset($_GET["gname"]) && $_GET["gname"] == $GNOption){
				echo "<option selected=\"selected\" value='$GNOption'>$GNOption</option>\n";
			}
			else{
				echo "<option value='$GNOption'>$GNOption</option>\n";
			}

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

function createGroupDeleter(){
	global $mysqli, $_SESSION, $_GET;

	echo "<form action=\"friends.php\" method=\"GET\">\n";
	echo "<input type=\"hidden\" name=\"gname\" value=\"{$_GET["gname"]}\" />\n";
	echo "<input type=\"hidden\" name=\"deleteGroup\" value=1 />\n";
	echo "<button type=\"submit\"> Delete This Group </button>\n";
	echo "</form>\n";

	if(isset($_GET["deleteGroup"])){
		//first empty the group
		$query = "DELETE FROM inGroup WHERE gname = ? && ownername = ?";
		if($stmt = $mysqli->prepare($query)){
			$stmt->bind_param("ss", $_GET["gname"], $_SESSION["username"]);
			$stmt->execute();
		}
		$stmt->close();
		//then delete the group itself
		$query="DELETE FROM friendGroup WHERE gname = ? && ownername = ?";
		if($stmt = $mysqli->prepare($query)){
			$stmt->bind_param("ss", $_GET["gname"], $_SESSION["username"]);
			$stmt->execute();

		}
		$stmt->close();
		header("location: friends.php");
	}

}

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
}

function createFriendEditor(){
	global $_GET, $_SESSION, $mysqli;	

	//makes a new freindGroup row if it is told to do so
	if(isset($_GET["newGroup"]) && $_GET["gname"] != ""){
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
	//add friend box/button
	if( isset($_GET["nfname"]) && isset($_GET["nlname"]) ){
		$query = "SELECT username FROM person WHERE fname = ? && lname = ?";
		$stmt = $mysqli->prepare($query);
		if($stmt){
			$stmt->bind_param("ss",$_GET["nfname"], $_GET["nlname"]);
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
				addFriend($un, $_GET["gname"]);

			}
			else if($stmt->num_rows > 1){
				//too many results
				//check if there is unadd
				if(isset($_GET["unt"])){
					addFriend($_GET["unt"], $_GET["gname"]);
				}
				else{
					echo "<form action=\"friends.php\" methog=\"GET\">\n";
					echo "<input type=\"hidden\" name=\"gname\" value=\"{$_GET["gname"]}\" />";
					echo "<input type=\"hidden\" name=\"nfname\" value=\"{$_GET["nfname"]}\" />";
					echo "<input type=\"hidden\" name=\"nlname\" value=\"{$_GET["nlname"]}\" />";
					echo "Please Select By Username: <select name=\"unt\">\n";
					while($stmt->fetch()){
						$option = htmlspecialchars($un);
						echo "<option value=\"$option\">$option</option>\n";
					}
					echo "</select>\n";
					echo "<button type=\"submit\"> Add </button>\n";
					echo "</form>\n";

				}
			}
			$stmt->close();
		}
	}
	
	echo "<form action=\"friends.php\" method=\"GET\">\n";
	echo "<input type=\"hidden\" name=\"gname\" value=\"{$_GET["gname"]}\" />\n";
	echo "First Name: <input type=\"text\" name=\"nfname\" cols=15 /><br />\n";
	echo "Last Name: <input type=\"text\" name=\"nlname\" cols=15 /><br />\n";
	echo "<button type=\"submit\"> Add New Friend </button><br />\n";
	echo "</form>\n";
	
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
			echo "</form>\n";
		}
	}




}


//"main" method
if(isset($_SESSION["username"])){
	//display a menu of groups	
	createSelectionMenu();
	
	if(isset($_GET["gname"]) && $_GET["gname"] != ""){
		createGroupDeleter();
	}
	createNewGroupMenu();

	if(isset($_GET["gname"]) && $_GET["gname"] != ""){
		createFriendEditor();
	}
}
else{
	header("location: index.php");
}

?>

</body>
</html>
