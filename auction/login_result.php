<?php

// TODO: Extract $_POST variables, check they're OK, and attempt to login.
// Notify user of success/failure and redirect/give navigation options.

// For now, I will just set session variables and redirect.
include "db.php";
session_start();

// foreach($_POST as $key => $value){
//     echo"{$key} = {$value} <br>";
// }
// $test = mysqli_real_escape_string($con, "aaa");
// $test_hash = password_hash($test, PASSWORD_DEFAULT);
// $q_select_user = "SELECT userId, password 
// FROM User
// WHERE email = 'aaa@email.com'
// LIMIT 1
// ";
// $res = mysqli_query($con, $q_select_user) or die('Error matching user login credential' . mysql_error());
// $row = mysqli_fetch_array($res);
// $p = $row['password'];
// echo"$p";
// if(password_verify($test, $test_hash3)){
//     echo"aaaa";
// }
// // $test2 = mysqli_real_escape_string($con, "aaa");
// $test_hash2 = password_hash(mysqli_real_escape_string($con, "aaa"), PASSWORD_DEFAULT);
 

// echo"$test_hash <br>";
// echo"$test_hash2 <br>";

if(isset($_POST['email']) && isset($_POST['password'])){
    // echo"aaa";
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $pass = mysqli_real_escape_string($con, $_POST['password']);
    // echo"email: $email <br>";
    // echo"pass: $pass <br>";
    try{
        $q_select_user = "SELECT *
                            FROM User
                            WHERE id = '$email'
                            ";
        $res = mysqli_query($con, $q_select_user) or die('Error matching user login credential' . mysql_error());
        if(mysqli_num_rows($res) > 0){
            $row = mysqli_fetch_array($res);
            if(password_verify($pass, $row['password'])){
                $_SESSION['logged_in'] = true;
                $_SESSION['name'] = $row['firstName'];
                $_SESSION['account_type'] = $row['accountType'];
                $_SESSION['user_id'] = $row['id'];

                // echo"aa";        
                echo('<div class="text-center">You are now logged in! You will be redirected shortly.</div>');
            }
            else{
                echo('Invalid username or password, please try again');
            }
        }
        else{
            echo('Invalid username or password, please try again');
        }
    }
    catch(Exception $e){
        echo $e->getMessage();
    }


}

// $_SESSION[]



// Redirect to index after 5 seconds
header("refresh:2;url=index.php");
mysqli_close($con);
?>

