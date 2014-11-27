<html>
<head>
</head>
<body>
<?php
include "connectdb.php";
include "session.php";

if(isset($_SESSION["username"])) {
	echo "You are already logged in. \n";
	echo "<a href=\"index.php\">here</a>.\n";
	header("location: index.php");
}
else{
	
	//if the user have entered both entries in the form, check if they exist in the database
	if(isset($_POST["username"]) && isset($_POST["password"])) {
		$stmt = $mysqli->prepare("SELECT * FROM person WHERE username = ? && password = ?");
		if($stmt){
			$stmt->bind_param("ss", $_POST["username"], md5($_POST["password"]));
			$stmt->execute();
			$stmt->bind_result($username, $password, $fname, $lname);
			if($stmt->fetch()){
				$_SESSION["username"] = $username;
				$_SESSION["password"] = $password;
				$_SESSION["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];
				header("location: index.php");
				
			}
			else{
				echo "wrong password/username combo";
			}
		}
		else{
			throw new Exception("MySQL not working");
		}
	}
	//if not then display login form
	else {
		echo "Enter your username and password below: <br /><br />\n";
		echo '<form action="login.php" method="POST">';
		echo "\n";
		echo 'Username: <input type="text" name="username" /><br />';
		echo "\n";
		echo 'Password: <input type="password" name="password" /><br />';
		echo "\n";
		echo '<input type="submit" value="Submit" />';
		echo "\n";
		echo '</form>';
		echo "\n";
		echo '<br /><a href="index.php">Go back</a>';
	}
	echo "Register an account now!  It's fast, easy, and secure-ish!<br>";
	echo "<a href=\"register.php\"> Register </a><br />";
}
?>
</body>
</html>
