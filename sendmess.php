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
    if(isset($_POST["username"]) && isset($_POST["message"])) {
      $stmt = $mysqli->prepare("SELECT username FROM person WHERE username = ?");
      if($stmt){
        $uname = $_POST["username"];
        $pmess = $_POST["message"];
        $stmt->bind_param('s', $uname);
        $stmt->execute();
        $stmt->bind_result($username);
        if($stmt->fetch()){
          $stmt->close();
          $tuname = $_SESSION["username"];
          if($stmt = $mysqli->prepare("INSERT INTO pweets (s_id, r_id, mess, mdate) values (?, ?, ?, NOW())")){
            $stmt->bind_param("sss", $tuname, $uname, $pmess);
            $stmt->execute();
            $stmt->close();
            echo "Who would you like to send this to?<br>";
            echo '<form action="sendmess.php" method="POST">';
            echo '<input type = "text" name = "username"><br>';
            echo "Type in a short message to send.<br>";
            echo '<input type = "text" name = "message"><br>';
            echo '<input type = "submit" value = "Submit">';
            echo '</form>';
            echo "Message sent!<br>";
            echo '<form action = "index.php" method = "get">';
              echo '<br><input type = "submit" value = "Go Back">';
            echo '</form>'; 
          }
        } 
        else{
          echo "User does not exist!<br>";
          echo "Who would you like to send this to?<br>";
          echo '<form action="sendmess.php" method="POST">';
          echo '<input type = "text" name = "username"><br>';
          echo "Type in a short message to send.<br>";
          echo '<input type = "text" name = "message"><br>';
          echo '<input type = "submit" value = "Submit">';
          echo '</form>';
          echo '<form action = "index.php" method = "get">';
            echo '<br><input type = "submit" value = "Go Back">';
          echo '</form>'; 
        }     

      }
      else{
        throw new Exception("MySQL not working");
      }
    }
    else{
      echo "Who would you like to send this to?<br>";
      echo '<form action="sendmess.php" method="POST">';
      echo '<input type = "text" name = "username"><br>';
      echo "Type in a short message to send.<br>";
      echo '<input type = "text" name = "message"><br>';
      echo '<input type = "submit" value = "Submit">';
      echo '</form>';
      echo '<form action = "index.php" method = "get">';
        echo '<br><input type = "submit" value = "Go Back">';
      echo '</form>'; 
      
    }
    

     
  }
?>
</body>
</html>  