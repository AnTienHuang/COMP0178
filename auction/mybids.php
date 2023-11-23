<?php     
include_once("header.php");
require_once("utilities.php");
include_once("db.php");
ini_set('display_errors','On');
ini_set('error_reporting',E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>
<div class="container">

<h2 class="my-3">My bids</h2>

<div id="searchSpecs">
<!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
<form method="get" action="mybids.php">
  <div class="row">
    <div class="col-md-3 pr-0">
      <div class="form-inline">
        <label for="bid_status" class="mx-2">Filter by</label>
        <select class="form-control" name="bid_status">
          <option value=""> Select a status </option>
          <option value="Winning"> Winning </option>
          <option value="Losing"> Losing </option>
          <option value="Won"> Won </option>
          <option value="Lost"> Lost </option>
        </select>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-inline">
        <label class="mx-2" for="order_by">Sort by:</label>
        <select class="form-control" name="order_by">
          <option selected value="highest_bid_price asc">Price (low to high)</option>
          <option value="highest_bid_price desc">Price (high to low)</option>
          <option value="endTime asc">Soonest expiry</option>
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
  // This page is for showing a user the auctions they've bid on.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  
  // TODO: Check user's credentials (cookie/session).
  
  // TODO: Perform a query to pull up the auctions they've bidded on.
  
  // TODO: Loop through results and print them out as list items.
  if (empty($_GET['bid_status'])) {
    // TODO: Define behavior if a category has not been specified.
    $status_condition = '';
  }
  else {
    $bid_status = $_GET['bid_status'];
    $status_condition = "AND b.bidStatus = '{$bid_status}'";
  }
  
  if (empty($_GET['order_by'])) {
    // TODO: Define behavior if an order_by value has not been specified.
    $order_by_condition = '';
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
        b.id AS bid_id, 
        b.price AS bid_price, 
        b.bidStatus, 
        MAX(b2.price) AS highest_bid_price,
        u.firstName AS user_first_name,
        u.lastName AS user_last_name,
        i.endTime,
        i.id AS itemId,
        i.title,
        i.description,
        COUNT(b.id) AS num_of_bids
      FROM Bid b
      JOIN User u ON b.buyerId = u.id
      LEFT JOIN Bid b2 ON b.itemId = b2.itemId
      LEFT JOIN item i ON i.id = b.itemId
      WHERE b.buyerId = $user_id $status_condition
      AND (b.itemId, b.bidTime) IN (
            SELECT itemId, MAX(bidTime)
            FROM Bid
            WHERE buyerId = $user_id
            GROUP BY itemId
        )
      GROUP BY b.id, b.price, b.bidStatus, u.firstName, u.lastName, i.endTime
      $order_by_condition";

  $bids = mysqli_query($con, $q);
  $row_num = mysqli_num_rows($bids);
  if(mysqli_num_rows($bids) == 0){
    echo"There is no bid placed yet.";
    exit();
  }
  elseif(!$bids){
    echo"There is an error when querying bids";
  }
  else {
    $results_per_page = 10;
    $max_page = ceil($row_num / $results_per_page);
  }
}
  while($row = mysqli_fetch_assoc($bids)) : 
      $item_id = $row['itemId'];
      $title = $row['title'];  
      $description = $row['description'];  
      $current_price = $row['highest_bid_price'];  
      $num_bids = $row['num_of_bids'];  
      $end_time = $row['endTime'];
      $bid_status = $row['bidStatus'];
      $bid_price = $row['bid_price'];
      print_mybids_li($item_id, $title, $description, $current_price, $bid_price, $num_bids, $end_time, $bid_status);
  endwhile;
?>
<div>
  <br>
  <br>
  <br>
  <p> aNote: If you have placed multiple bids on an item, only the latest one will be listed here. <p>
</div>

<?php mysqli_close($con); ?>
<?php include_once("footer.php")?>