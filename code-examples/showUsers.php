<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
  </head>
  <body>
    <h2>User List</h2>
    <?php
      $connection = mysqli_connect('localhost','root','root','Example')
        or die('Error connecting to MySQL server.' . mysql_error());
      $query = "SELECT first_name, family_name FROM Users";
      $result = mysqli_query($connection,$query)
        or die('Error making select users query' . mysql_error());
      echo '<table border="1">';
      while ($row = mysqli_fetch_array($result))
      {
        echo '<tr><td>' . $row['first_name']. '</td><td>' .
          $row['family_name']. '</tr></td>';
      }
      echo '</table>';

      mysqli_close($connection);
    ?>
  </body>
</html>