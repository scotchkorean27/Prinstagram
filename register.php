<html>
<head>
</head>
<body>
<?php
include "connectdb.php";
include "session.php";
include "security.php";

function echoForm(){
	echo "Fill out ALL of the fields, ya dingus.";
	echo '<form action = "register.php" method = "POST">';
	echo "Please fill out ALL fields.<br>";
	echo "Username:";
	echo '<input type = "text" name = "username"><br>';
	echo "Password:";
	echo '<input type = "password" name = "password"><br>';
	echo "First name:";
	echo '<input type = "text" name = "fname"><br>';
	echo "Last Name:";
	echo '<input type = "text" name = "lname"><br>';
	echo '<input type = "submit" value = "Register">';
	echo '</form>';
	echo '<form action = "login.php" method = "get">';
	echo '<input type = "submit" value = "Return to Login">';
	echo '</form>';
}


if(isset($_SESSION["username"])) {
	echo "You are already logged in. \n";
	echo "<a href=\"index.php\">here</a>.\n";
	header("location: index.php");
}
else{
	if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["fname"]) && isset($_POST["lname"])) {
		$uname = $_POST["username"];
		$pw = $_POST["password"];
		$fname = $_POST["fname"];
		$lname = $_POST["lname"];
		if($uname == "" || $pw == "" || $fname == "" || $lname == ""){
			echoForm();
		}
		else if(!(isUserNameValid($uname) && isUserNameValid($fname) && isUserNameValid($lname))){
			echo "<p>usernames and first and last names must be alphanumeric and at least one character</p><br />\n";
			echoForm();
		}
		else if($stmt = $mysqli->prepare("SELECT * from person WHERE username = ?")){
			$stmt->bind_param('s', $uname);
			$stmt->execute();
			$stmt->bind_result($tmpuname);
			if($stmt->fetch()){
				echo "I'm sorry, but that username is taken.  Please try again.";
				$stmt->close();
				echoForm();
			}
			else{
				$stmt->close();
				if($stmt = $mysqli->prepare("INSERT INTO person values (?, ?, ?, ?)")){
					$stmt->bind_param('ssss', $uname, md5($pw), $fname, $lname);
					$stmt->execute();
					$stmt->close();
					echo "Thank you for registering " . $fname . ".  Click the button below to proceed.";
					$_SESSION["username"] = $uname;
					$_SESSION["password"] = $pw;
					$_SESSION["fname"] = $fname;
					$_SESSION["lname"] = $lname;
					$_SESSION["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];
					echo '<form action = "login.php" method = "get">';
					echo '<br><input type = "submit" value = "Proceed">';
					echo '</form>';  
				}
			}
		}
		else{
			throw new Exception("MySQL not working");
		}

	}
	else{
		echoForm();
	}

}




?>
</body>
</html>
