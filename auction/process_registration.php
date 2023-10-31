<?php

// TODO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation 
// options.

    include 'db.php';

    if (isset($_POST['accountType'])){
        
        $account_type = $_POST['accountType'];
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $password = mysqli_real_escape_string($con, $_POST['password']);
        $is_valid = true;

        foreach($_POST as $key => $value){
            echo"{$key} = {$value} <br>";
        }
        # check if there are missing values
            // (Done in the form)

        # check if user exists
        $query = "SELECT *
                    FROM User
                    WHERE email = '$email'";
        $users = mysqli_query($con, $query);
        $row_cnt = mysqli_num_rows($users);
        // printf("Select returned %d rows.\n", $row_cnt);
        // foreach ($users as $user) {
        //     printf("User ID: %s \n", $user["userId"]);
        //     echo"<br>";
        //     printf("User Email: %s \n", $user["email"]);
        //     echo"<br>";
        // }
        if($row_cnt > 0){
            $error = "User already exists";
            $is_valid = false;
        }

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