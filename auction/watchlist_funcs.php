 <?php
    include("db.php");
    ini_set('display_errors','On');
    ini_set('error_reporting',E_ALL);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    session_start();
    if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
      return'aaa';
    }

    // Extract arguments from the POST variables:
    $item_id = $_POST['arguments'][0];
    $user_id = $_SESSION['user_id'];
    $now = date('Y-m-d H:i:s');
    $success = false;

    if ($_POST['functionname'] == "add_to_watchlist") {
        // TODO: Update database and return success/failure.  
        $q = "INSERT INTO WatchList (itemId, userId, addedTime)
              VALUES ($item_id, $user_id, '$now') 
              ";
    }
    else if ($_POST['functionname'] == "remove_from_watchlist") {
        // TODO: Update database and return success/failure.
        $q = "DELETE FROM WatchList
              WHERE userId = $user_id 
              AND itemId = $item_id
        ";
    }
    // $r = mysqli_query($con, $q);
    // if(mysqli_num_rows($r) > 0){
    //     return $r;
    // }else{
    //   return "aa";
    // }

    if(mysqli_query($con, $q)) {
      echo'success';
    } else {
      echo "Error: " . $q . "<br>" . mysqli_error($con);
      exit();
    }

    // Note: Echoing from this PHP function will return the value as a string.
    // If multiple echo's in this file exist, they will concatenate together,
    // so be careful. You can also return JSON objects (in string form) using
    // echo json_encode($res).

    mysqli_close($con);
?>