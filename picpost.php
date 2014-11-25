<!DOCTYPE html>

<html>
<body>
<form action = "picup.php" method="post" enctype = "multipart/form-data">
Choose a group<br>
<select name='gname'>

<?php
include "connectdb.php";
include "session.php";

if ($stmt = $mysqli->prepare("select distinct gname from friendGroup where ownername=?")) {
	$stmt->bind_param("s", $_SESSION["username"]);
	$stmt->execute();
	$stmt->bind_result($gname);
	echo "<option value='Public'>Public</option>\n";
	while($stmt->fetch()) {
		$gname = htmlspecialchars($gname);
		echo "<option value='$gname'>$gname</option>\n";	
	}
	$stmt->close();
}

?>
</select><br><br>
Selct Image to upload:<br>
<input type = "file" name = "fileToUpload" id = "fileToUpload"><br>
<br>Enter a caption (optional):<br><input type = "text" name = "caption"><br>
<br>Enter your longitude (optional):<br><input type = "text" name = "lon"><br>
<br>Enter your latitude (optional):<br><input type = "text" name = "lat"><br>
<br>Where are you? (optional)<br><input type = "text" name = "locname"><br><br>
<input type = "submit" value = "Post" name = "submit"><br><br>
</form>

<form action = "index.php" method = "get">
<br><input type = "submit" value = "Go Back">
</form>

</body>
</html>
