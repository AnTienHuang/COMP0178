<?php
    $db_server = "localhost";
    $db_user = "example";
    $db_password = "";
    $db_name = "auction_test";
    $con = "";

    // try{
    $con = mysqli_connect($db_server,
        $db_user,
        $db_password,
        $db_name
    );

    if($con){
        echo"connected";
      }
      else{
        echo"not connected";
      }
    // }
    // catch(){
    //     echo"there is an error"
    // }

    // $mysqli = new mysqli($db_server, $db_user, $db_password, $db_name);

    // Check connection
    // if ($mysqli -> connect_errno) {
    // echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    // exit();
    // }
?>