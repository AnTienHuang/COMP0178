<?php

// TODO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation 
// options.

    include 'db.php';

    if (isset($_POST['accountType'])){
        
        $account_type = $_POST['accountType'];
        $email = mysqli_real_escape_string($connection, $_POST['email']);
        $password = mysqli_real_escape_string($connection, $_POST['password']);

        $is_valid = true;

        # check if there are missing values
            // (Done in the form)
            //
            // foreach($_POST as $key => $value){
            //     echo"{$key} = {$value} <br>";
            //     if(empty($value)){
            //         $is_valid = false;
            //     }
            // }
            //


        # check if user exists
        $query = "SELECT userId
                    FROM User
                    WHERE userId = ''";

        $user = $mysqli->query("SELECT id FROM test ORDER BY id ASC");

        $error = "User already exists";
        # check if passwords are the same




            // date_default_timezone_set('Europe/London');
            // $time = date('H:i:s', time());
        
            // if (!isset($user) || $user == '' || !isset($message) || $message == '') {
            //   $error = "Please fill in your name and a message";
            //   header("Location: index.php?error=" . urlencode($error));
            //   exit();
            // }
            // else {
            //   $query = "INSERT INTO messages (user, message, time) 
            //             VALUES ('$user', '$message', '$time')";
            //   if (!mysqli_query($connection, $query)) {
            //     die('Error: ' . mysqli_error($connection));
            //   }
            //   else {
            //     header('Location: index.php');
            //     exit();
            //   }
            // }
            if(!$is_valid){
                header("Location: register.php?error=" . urldecode($error));
                exit();
            }
            
            echo"Is valid: {$is_valid}";
    }

?>