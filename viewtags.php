<html>
<head>
</head>
<body>
<?php
  include "connectdb.php";
  include "session.php";
  session_start();
  if(!isset($_SESSION["username"])){
    header("location: login.php");
    echo "<a href=\"login.php\">You Must Login</a><br \>";
  }
  else{
    $username = $_SESSION["username"];
    if(isset($_POST["submit"])){
      if(isset($_POST["yesno"])){
        $pid = $_POST["pid"];
        $tagger = $_POST["tagger"];
        $taggee = $_POST["taggee"];
        if($_POST["yesno"] == "Accept"){
          echo "Tag has been accepted .<br>";  
          if($stmt = $mysqli->prepare("update tag set tstatus = 1 where pid = ? and tagger = ? and taggee = ?")){
            $stmt->bind_param("iss", $pid, $tagger, $taggee);
            $stmt->execute();
            $stmt->close();
          }
          else{
            throw new Exception("The Database is kaput at the moment.  Try again later.");
          }
        }
        else if($_POST["yesno"] == "Decline"){
          echo "Tag has been declined.<br>";
          if($stmt = $mysqli->prepare("delete from tag where pid = ? and tagger = ? and taggee = ?")){
            $stmt->bind_param("iss", $pid, $tagger, $taggee);
            $stmt->execute();
            $stmt->close();
          }
          else{
            throw new Exception("The Database is kaput at the moment.  Try again later.");
          }
        }
      }
    }
    if($stmt = $mysqli->prepare("select tag.pid, tagger, ttime, image from tag inner join photo on tag.pid = photo.pid where taggee = ? and tstatus = 0 order by ttime desc")){
      $stmt->bind_param('s', $username);
      $stmt->execute();
      $stmt->bind_result($pid, $tagger, $time, $url);
      echo "Here are your pending tag requests:<br>";
      echo '<form action = "index.php" method = "get">';
        echo '<br><input type = "submit" value = "Go Back">';
      echo '</form>'; 
      while($stmt->fetch()){
        echo $time . ": " . $tagger . " has tagged you in this photo<br>";
        echo '<img src = "' . $url . '" alt = "Picture" height = "128"><br>';
        echo "Would you like to accept or decline this tag?<br>";
        echo '<form action = "viewtags.php" method = "post">';
        echo '<input type = "hidden" name = "pid" value = "' . $pid . '">';
        echo '<input type = "hidden" name = "tagger" value = "' . $tagger . '">';
        echo '<input type = "hidden" name = "taggee" value = "' . $username . '">';
        echo 'Accept<input type = "radio" name = "yesno" value = "Accept">&nbsp&nbsp';
        echo 'Decline<input type = "radio" name = "yesno" value = "Decline"><br>';
        echo '<input type = "submit" name = "submit" value = "Submit">';
        echo '</form><br>';
      }
      $stmt->close();
    }
    else{
      echo "Could not access database.";
      echo '<form action = "index.php" method = "get">';
        echo '<br><input type = "submit" value = "Go Back">';
      echo '</form>'; 
    }

  }



?>
</body>
</html>
