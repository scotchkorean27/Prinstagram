<html>
<head>
</head>

<body>
<?php
include "connectdb.php";
include "session.php";
include "pics.php";

if(!isset($_SESSION["username"])){
	header("location: login.php");
	echo "<a href=\"login.php\">You Must Login</a><br \>";
}
else{
	//TODO add more links
	echo "<a href=\"logout.php\">logout</a>&nbsp";
	echo "<a href=\"friends.php\">Manage Friends</a>&nbsp";
	echo "<a href=\"picpost.php\">Upload Photos</a>&nbsp";
	echo "<a href=\"sendmess.php\">Send Qweet</a>&nbsp";
	echo "<a href=\"viewmess.php\">Check Qweets</a><br/>";

	echo "<p>";
	$notcount = 0;
	if($stmt = $mysqli->prepare("SELECT count(*) from tag where taggee = ? and tstatus = 0")){
		$stmt->bind_param('s', $_SESSION["username"]);
		$stmt->execute();
		$stmt->bind_result($notcount);
		$stmt->fetch();
		if($notcount == 0){
			echo "You have no tags awaiting approval";}
		else if($notcount == 1){
			echo "You have 1 tag awaiting approval";}
		else{
			echo " You have " . $notcount . " tags awaiting approval.";}
		$stmt->close();
	}
	echo "</p>";

	loadPics();
}
?>

</body>
</html>
