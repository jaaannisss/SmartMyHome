<?php
require "db.php";
if(isset($_GET['email'])){
  if(isset($_GET['activation_key'])){
    $email = $_GET['email'];
    $activation_key = $_GET['activation_key'];
    $sql = mysqli_query($database, "SELECT activated FROM login where email = '".$email."'");
    $result = mysqli_fetch_assoc($sql)['activated'];
    if ($result == 0) {
      $sql = mysqli_query($database, "SELECT activation_key FROM login WHERE email = '".$email."'");
      $result = mysqli_fetch_assoc($sql)['activation_key'];
      if ($result == $activation_key) {
        if(mysqli_query($database, "UPDATE login SET activated = '1' WHERE email = '".$email."'") === TRUE){
          header("Location:index.php?event=activated");
        } else {
          header("Location:index.php?error=db_error");
        }
      }else {
        header("Location:index.php?error=activation");
      }
    }else {
      header("Location:index.php?error=already_activated");
    }
  }
}
 ?>
