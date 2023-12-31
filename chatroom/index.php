<?php include 'database.php'; ?>
<?php 
  $query = "SELECT * FROM messages ORDER BY id DESC";
  $messages = mysqli_query($connection, $query);
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8"/>
    <title>CHAT!</title>
    <link rel="stylesheet" href="css/style.css" type="text/css">
  </head>
  <body>
    <div id="container">
      <header>
        <h1>Chat Room</h1>
      </header>
      <div id="messages">
        <ul>
          <?php while($row = mysqli_fetch_assoc($messages)) : ?>
            <li class="message">
              <span><?php echo $row['time'] ?> - </span><strong>
              <?php echo $row['user']?></strong>
              : <?php echo $row['message'] ?>
            </li>     
          <?php endwhile; ?>
        </ul>
      </div>
      <div id="input">
        <?php if (isset($_GET['error'])) : ?>
          <div class="error"><?php echo $_GET['error']; ?></div>
        <?php endif; ?>
        <form method="post" action="process.php">
          <input type="text" id="user" name="user" placeholder="Enter Your Name"/>
          <input type="text" id="newmessage" name="message" placeholder="Enter A Message"/>
          <input id="show-btn" type="submit" name="submit" value="Show It"/>
        </form>
      </div>
    </div>
  </body>
</html>
