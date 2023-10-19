<?php
  $connection = mysqli_connect("localhost", "chatadmin", "adminpassword", "chatroom");

  if (mysqli_connect_errno())
    echo 'Failed to connect to the MySQL server: '. mysqli_connect_error();

?>