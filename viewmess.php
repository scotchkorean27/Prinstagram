<html>
<head>
</head>
<body>
<?php
  include "connectdb.php";
  include "session.php";
  if(!isset($_SESSION["username"])){
    header("location: login.php");
    echo "<a href=\"login.php\">You Must Login</a><br \>";
  }
  else{
    $username = $_SESSION["username"];
    if($stmt = $mysqli->prepare("select fname, lname, s_id, mess, mdate from pweets inner join person on s_id = username where r_id = ? order by mdate desc limit 100")){
      $stmt->bind_param('s', $username);
      $stmt->execute();
      $stmt->bind_result($fname, $lname, $sid, $mess, $mdate);
      echo "Here are the last 100 Qweets you got:<br>";
      echo '<form action = "index.php" method = "get">';
        echo '<br><input type = "submit" value = "Go Back">';
      echo '</form>'; 
      while($stmt->fetch()){
        echo "Message from " . $fname . " " . $lname . " (" . $sid . ") on " . $mdate . ": <br>";
        echo $mess . "<br><br>";
      }
    }
    else{
      echo "You have no messages.";
      echo '<form action = "index.php" method = "get">';
        echo '<br><input type = "submit" value = "Go Back">';
      echo '</form>'; 
    }

  }



?>
</body>
</html>