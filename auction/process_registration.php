<?php

// TODO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation 
// options.

    include 'db.php';

    if (isset($_POST['accountType'])){
        
        $account_type = $_POST['accountType'];
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $password = mysqli_real_escape_string($con, $_POST['password']);
        $password2 = mysqli_real_escape_string($con, $_POST['passwordConfirmation']);
        $first_name = mysqli_real_escape_string($con, $_POST['firstname']);
        $last_name = mysqli_real_escape_string($con, $_POST['lastname']);
        $valid = true;

        // foreach($_POST as $key => $value){
        //     echo"{$key} = {$value} <br>";
        // }
        # check if there are missing values
            // (Done in the form)

        # check if user exists
        $query = "SELECT *
                    FROM User
                    WHERE email = '$email'";
        $users = mysqli_query($con, $query);
        $row_cnt = mysqli_num_rows($users);
        // [DEBUG]
            // printf("Select returned %d rows.\n", $row_cnt);
            // foreach ($users as $user) {
            //     printf("User ID: %s \n", $user["userId"]);
            //     echo"<br>";
            //     printf("User Email: %s \n", $user["email"]);
            //     echo"<br>";
            // }
        if($row_cnt > 0){
            $error = "User already exists";
            $valid = false;
        }

        # check if passwords are the same
        if($password != $password2){
            $error = "Passwords does not match";
            $valid = false;
        }

        # return error if invalid, otherwise register and go to reg_success.php
        if(!$valid){
            header("Location: register.php?error=" . urldecode($error));
            exit();
        }
        else{
            #insert user record to DB
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $is_seller = 0;
            if($account_type == "Seller"){
                $is_seller = 1;
            }
            try{
                $insert = "INSERT INTO User (firstName, lastName, password, email, isSeller)
                VALUES ('$first_name', '$last_name', '$hash', '$email', '$is_seller')";
                mysqli_query($con, $insert);
                header("Location: reg_success.php");
                exit();
            }
            catch(mysqli_sql_exceptiono $e){
                $error = "Invalid user input, please check.";
                header("Location: register.php?error=" . urldecode($error));
                exit();
            }
        }
        // echo"Is valid: {$valid}";
    }

?>