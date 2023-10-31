<?php
include 'db.php';
$account_type = "Buyer";
$email = "test";
$password = "test";
$password2 = "test";
$first_name = "test";
$last_name = "test";

$hash = password_hash($password, PASSWORD_DEFAULT);
$is_seller = 0;
if($account_type == "Seller"){
    $is_seller = 1;
}
$insert = "INSERT INTO User (firstName, lastName, password, email, isSeller)
VALUES ('$first_name', '$last_name', '$hash', '$email', '$is_seller')";
$user = mysqli_query($con, $insert);
$row_cnt = mysqli_num_rows($users);
echo"debug <br>";
// [DEBUG]
    // printf("Select returned %d rows.\n", $row_cnt);
    foreach ($users as $user) {
        printf("User ID: %s \n", $user["userId"]);
        echo"<br>";
        printf("User Email: %s \n", $user["email"]);
        echo"<br>";
    }
?>