<?php

  session_start();
  error_reporting(0);

  //If get error
  if(isset($_GET['error'])){
    if($_GET['error'] == "not_activated"){
      print "Your account is not activated, please check your email.";
    }
    if($_GET['error'] == "already_activated"){
      print "Your account is already activated!";
    }
    if($_GET['error'] == "db_error"){
      print "Database error!";
    }
    if($_GET['error'] == "login"){
      print "Wrong username or password!";
    }
    if($_GET['error'] == "activation"){
      print "Wrong email or activationkey!";
    }
  }

  //If get event
  if(isset($_GET['event'])){
    if($_GET['event'] == "activated"){
      print "Your account is now activated.";
    }
    if($_GET['event'] == "registered"){
      print "Successfully registered! Please check your email, to activate your account!";
    }
    if($_GET['event'] == "logout"){
      print "Sucessfully logged out!";
    }
  }

  //If Session
  if($_SESSION["loggedin"] == 1){
    print "Logged in!";

    //Logout
    if(isset($_POST['logout'])){
      $_SESSION["loggedin"] = 0;
      header("Location:index.php?event=logout");
    }
  }


  //Login
  if(isset($_POST['login'])){
    require "db.php";
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $sql = mysqli_query($database, "SELECT password FROM login WHERE email = '".$email."'");
    $result = mysqli_fetch_assoc($sql)['password'];
      if ($result == $password) {
        $sql = mysqli_query($database, "SELECT activated FROM login WHERE email = '".$email."'");
        $result = mysqli_fetch_assoc($sql)['activated'];
        if($result == 1 ){
          $_SESSION["loggedin"] = 1;
          header("Location:index.php");
        }else {
          header("Location:index.php?error=not_activated");
        }
      } else {
        header("Location:index.php?error=login");
      }
    }


  //Register
  if(isset($_POST['register'])){
    require "db.php";
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $passwordrepeat = md5($_POST['passwordrepeat']);
    $activation_key = md5(time()*rand(10000,1000000000));
    if($password == $passwordrepeat){
        $sql = mysqli_query($database, "SELECT email FROM login WHERE email = '".$email."'");
        if(mysqli_num_rows($sql) > 0){
          echo "This email is already in use!";
        }else {
          if(mysqli_query($database, "INSERT INTO login (email, password, activation_key, activated) VALUES ('$email', '$password', '$activation_key', '0')") === TRUE) {
            $message =
            "
            Please click the link below, to activate your account:
            192.168.178.99/smartmyhome/activate.php?email=$email&activation_key=$activation_key
            ";
            mail($email, "Please activate your account!", $message, "From: noreply@smartmyhome.com");
            header("Location:index.php?event=registered");
          } else {
            header("Location:index.php?error=db_error");
          }
        }
    }else {
      print "The passwords don't match!";
    }
  }
 ?>

 <!DOCTYPE HTML>
<html>
  <head>

  </head>
  <body>
    <form class="register" action="index.php" method="post">
      <input type="email" name="email" placeholder="Enter email" required="true"><br/>
      <input type="password" name="password" placeholder="Enter password" required="true"><br/>
      <input type="password" name="passwordrepeat" placeholder="Repeat password" required="true"><br/>
      <input type="checkbox" name="eula" required="true">
      <label for="eula">I have read the eula!</label><br/>
      <input type="submit" name="register" value="Register!">
    </form>

    <form class="login" action="index.php" method="post">
      <input type="email" name="email" placeholder="Enter email" required="true"><br/>
      <input type="password" name="password" placeholder="Enter password" required="true"><br/>
      <input type="checkbox" name="stayloggedin">
      <label for="eula">Stay logged in</label><br/>
      <input type="submit" name="login" value="Login!">
    </form>

    <form class="logout" action="index.php" method="post">
      <input type="submit" name="logout" value="Log out!">
    </form>
  </body>
</html>
