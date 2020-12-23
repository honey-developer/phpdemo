<?php
session_start();
require_once('PHPMailer/PHPMailerAutoload.php');

// initializing variables
$username = "";
$email    = "";
$mobile = "";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'vinod', 'vinod@033', 'testdb');//server,username,password,databasenname

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $mobile = mysqli_real_escape_string($db, $_POST['mobile']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if(empty($mobile)){
    array_push($errors,"Mobile number is required");
  }
  if(strlen($mobile)!=10){
    array_push($errors,"Mobile number should be 10 digit number");
  }
  if ($password_1 != $password_2) {
	array_push($errors, "The two passwords do not match");
  }

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);//mysqli_query('dbconnection','query')
  $user = mysqli_fetch_assoc($result);
  //array => {[0]=>value of id,[1]=>value of username,[2]=>value of email,[3]=>value of password}
  //associcate array {[id]=>value of id,[username]=>valueof username}

  if ($user) { // if user exists
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }

    if ($user['email'] === $email) {
      array_push($errors, "email already exists");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
  	$password = md5($password_1);//encrypt the password before saving in the database
  	$query = "INSERT INTO users (username, email, password) 
  			  VALUES('$username', '$email', '$password')";
    mysqli_query($db, $query);
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Host = 'ssl://smtp.gmail.com';
    $mail->Port = '465';
    $mail->isHTML();
    $mail->Username = 'testphpvinod@gmail.com';
    $mail->Password = 'Qwerty@123';
    $mail->SetFrom('testphpvinod@gmail.com','Test PHP');
    $mail->Subject = "Your Registration Successful";
    $mail->Body = 'Hello '.$username.', You have succesfully registered';

    $mail->AddAddress($email);
    $result = $mail->Send();

    if($result == 1){
      $params=array('sender_id'=>'FSTSMS', 'message'=>'Hello '.$username.', You have succesfully registered', 'language'=>'english', 'route'=>'p','numbers'=>$mobile);
      $post_data = json_encode($params);
      $defaults = array(
      CURLOPT_URL => 'https://www.fast2sms.com/dev/bulk',//form action
      CURLOPT_POST => true,//method
      CURLOPT_POSTFIELDS => $post_data,//input feilds
      CURLINFO_HEADER_OUT => true,
      CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'authorization: xiCL2ucXABlhDp5JrGnSqmPbOjZ8zk1otdvVIQTeYU0a6NEfM4qNuD7basXVOHTgpEtZhYBwcnGoRri0')
      );
      $ch = curl_init();
      curl_setopt_array($ch, ($defaults));
      $rest = curl_exec($ch);//Hit SMS API and get back the result
    }
    else{
      array_push($errors, "Message sending failed");
    }
    
  	$_SESSION['username'] = $username;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: index.php');
  }
}

if (isset($_POST['login_user'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
  
    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }
  
    if (count($errors) == 0) {
        $password = md5($password);
        $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
        $results = mysqli_query($db, $query);
        if (mysqli_num_rows($results) == 1) {
          $_SESSION['username'] = $username;
          $_SESSION['success'] = "You are now logged in";
          header('location: index.php');
        }else {
            array_push($errors, "Wrong username/password combination");
        }
    }
  }

  ?>