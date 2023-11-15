<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Create New User</title>
  </head>
  <body>
    <?php
      function isDataValid()
      {
        $errorMessage = null;
        if (!isset($_POST['firstName']) or trim($_POST['firstName']) == '')
          $errorMessage = 'You must enter your first name';
        else if (!isset($_POST['familyName']) or trim($_POST['familyName']) == '')
          $errorMessage = 'You must enter your family name';
        if ($errorMessage !== null)
        {
          echo <<<EOM
          <p>Error: $errorMessage</p>
EOM;
          return False;
        }
        return True;
      }

      function getUser()
      {
        $user = array();
        $user['firstName'] = $_POST['firstName'];
        $user['familyName'] = $_POST['familyName'];
        return $user;
      }

      function printUser($user)
      {
        echo "<p>First Name: ${user['firstName']}</p>";
        echo "<p>Last Name: ${user['familyName']}</p>";
      }

      function saveToDatabase($user)
      {
        $connection = mysqli_connect('localhost','example','ucl','Example')
        or die('Error connecting to MySQL server.' . mysql_error());
        $query = "INSERT INTO Users (first_name, family_name)".
          "VALUES ('${user['firstName']}','${user['familyName']}')";
        $result = mysqli_query($connection,$query)
          or die('Error making saveToDatabase query' . mysql_error());
        mysqli_close($connection);
      }

      if (isDataValid())
      {
        $newUser = getUser();
        saveToDatabase($newUser);
        echo "<h2>User added</h2>";
        printUser($newUser);
      }
    ?>
    <p>
      <a href="newuserform.html">Return to input form</a><br />
      <a href="index.html">Go to index</a>
    </p>
  </body>
</html>