<?php     
include_once("header.php");
require_once("utilities.php");
include("db.php");
ini_set('display_errors','On');
ini_set('error_reporting',E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>
<div class="container">

<h2 class="my-3">My Bid Notifications</h2>

<div id="searchSpecs">
<!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
<form method="get" action="bid_noti.php">
  <div class="row">
    <div class="col-md-3 pr-0">
      <div class="form-inline">
        <label class="mx-2" for="order_by">Sort by:</label>
        <select class="form-control" name="order_by">
          <option selected value="createdTime desc">Time received (New to Old)</option>
          <option selected value="createdTime asc">Time received (Old to New)</option>
        </select>
      </div>
    </div>
    <div class="col-md-1 px-0">
      <button type="submit" class="btn btn-primary">Search</button>
    </div>
  </div>
</form>
</div> <!-- end search specs bar -->
<?php
  if (empty($_GET['order_by'])) {
    // TODO: Define behavior if an order_by value has not been specified.
    $order_by_condition = 'ORDER BY createdTime desc';
  }
  else {
    $order_by = $_GET['order_by'];
    $order_by_condition = "ORDER BY {$order_by}";
  }
  
  if (!isset($_GET['page'])) {
    $curr_page = 1;
  }
  else {
    $curr_page = $_GET['page'];
  }

  if (!(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true)) {
    echo"Please login first and make sure you have the right permission";
    exit();
}
else{
  $user_id = $_SESSION['user_id'];
  // TODO: Perform a query to pull up their auctions.
  $q = "SELECT 
        *
        FROM Notification
        Where userID = $user_id
        AND (bidId is not null OR notificationType = 'WatchList Update')
      $order_by_condition";
  $notifications = mysqli_query($con, $q);
  if(mysqli_num_rows($notifications) == 0){
    echo"There is no notification yet.";
    exit();
  }
  elseif(!$notifications){
    echo"There is an error when querying notifications";
  }
  else{  
    $results_per_page = 10;
    $max_page = ceil($row_num / $results_per_page);
  }
}
  while($row = mysqli_fetch_assoc($notifications)) : 
    $time = $row['createdTime'];
    $message = $row['message'];
    print_notifications($time, $message);
  endwhile;
?>

<?php mysqli_close($con); ?>
<?php include_once("footer.php")?>