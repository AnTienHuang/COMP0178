<?php 
include_once("header.php");
require_once("utilities.php");
include("db.php");
ini_set('display_errors','On');
ini_set('error_reporting',E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>


<div class="container">

<h2 class="my-3">Browse listings</h2>

<div id="searchSpecs">
<!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
<form method="get" action="browse.php">
  <div class="row">
    <div class="col-md-5 pr-0">
      <div class="form-group">
        <label for="keyword" class="sr-only">Search keyword:</label>
	    <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text bg-transparent pr-0 text-muted">
              <i class="fa fa-search"></i>
            </span>
          </div>
          <input type="text" class="form-control border-left-0" name="keyword" placeholder="Search for anything">
        </div>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-group">
        <label for="cat" class="sr-only">Search within:</label>
        <select class="form-control" name="cat">
          <option value=""> Search in all category </option>
          <?php 
              $result=mysqli_query($con,'SELECT id,name FROM category'); 
              while($row=mysqli_fetch_assoc($result)) { 
                  echo "<option value='$row[id]'>$row[name]</option>"; 
              } 
          ?> 
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


</div>

<?php
  // Retrieve these from the URL
  if (empty($_GET['keyword'])) {
    // TODO: Define behavior if a keyword has not been specified.
    $keyword_condition = '';
  }
  else {
    $keyword = $_GET['keyword'];
    $keyword_condition = "AND i.title LIKE '%{$keyword}%'";
  }

  if (empty($_GET['cat'])) {
    // TODO: Define behavior if a category has not been specified.
    $category_condition = '';
  }
  else {
    $category_id = $_GET['cat'];
    $category_condition = "AND ic.categoryId = {$category_id}";
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

  /* TODO: Use above values to construct a query. Use this query to 
  retrieve data from the database. (If there is no form data entered,
  decide on appropriate default value/default query to make. */
  $q = "SELECT 
        i.title,
        i.description,
        i.endTime,
        ic.itemId,
        ic.categoryId,
        c.name AS category_name,
        COUNT(bid.Id) AS num_of_bids,
        MAX(b.price) AS highest_bid_price
        FROM Item_Category ic
        JOIN Category c ON ic.categoryId = c.id
        LEFT JOIN (
          SELECT itemId, MAX(price) AS price
          FROM Bid
          GROUP BY itemId
        ) b ON ic.itemId = b.itemId
        LEFT JOIN (
          SELECT *
          FROM item
        ) i ON ic.itemId = i.id
        LEFT JOIN Bid bid ON i.id = bid.itemId
        WHERE i.itemStatus = 'Open' {$keyword_condition} {$category_condition} 
        GROUP BY ic.itemId, ic.categoryId, c.name
        {$order_by_condition}
  ";

  // echo"{$q}";
  $items = mysqli_query($con, $q);
  $row_num = mysqli_num_rows($items);
  if(mysqli_num_rows($items) == 0){
    echo"<br> <p>There is no aution created yet.</p>";
    exit();
  }
  elseif(!$items){
    echo"There is an error when querying items";
  }
  else {
    $results_per_page = 10;
    $max_page = ceil($row_num / $results_per_page);
  }
  
  /* For the purposes of pagination, it would also be helpful to know the
     total number of results that satisfy the above query */
?>

<div class="container mt-5">

<!-- TODO: If result set is empty, print an informative message. Otherwise... -->

<ul class="list-group">

<!-- TODO: Use a while loop to print a list item for each auction listing
     retrieved from the query -->

</ul>
    <?php 
        while($row = mysqli_fetch_assoc($items)) : 
            $item_id = $row['itemId'];
            $title = $row['title'];  
            $description = $row['description'];  
            $current_price = $row['highest_bid_price'];  
            $num_bids = $row['num_of_bids'];  
            $end_date = $row['endTime'];
            $category_name = $row['category_name'];
            print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date, $category_name);
        endwhile;
    ?>
    
<!-- Pagination for results listings -->
<nav aria-label="Search results pages" class="mt-5">
  <ul class="pagination justify-content-center">
  
<?php

  // Copy any currently-set GET variables to the URL.
  $querystring = "";
  foreach ($_GET as $key => $value) {
    if ($key != "page") {
      $querystring .= "$key=$value&amp;";
    }
  }
  
  $high_page_boost = max(3 - $curr_page, 0);
  $low_page_boost = max(2 - ($max_page - $curr_page), 0);
  $low_page = max(1, $curr_page - 2 - $low_page_boost);
  $high_page = min($max_page, $curr_page + 2 + $high_page_boost);
  
  if ($curr_page != 1) {
    echo('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
        <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
        <span class="sr-only">Previous</span>
      </a>
    </li>');
  }
    
  for ($i = $low_page; $i <= $high_page; $i++) {
    if ($i == $curr_page) {
      // Highlight the link
      echo('
    <li class="page-item active">');
    }
    else {
      // Non-highlighted link
      echo('
    <li class="page-item">');
    }
    
    // Do this in any case
    echo('
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }
  
  if ($curr_page != $max_page) {
    echo('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
        <span class="sr-only">Next</span>
      </a>
    </li>');
  }
?>

  </ul>
</nav>


</div>
<?php 

?>
<?php mysqli_close($con); ?>

<?php include_once("footer.php")?>