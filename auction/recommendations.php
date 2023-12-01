<?php 
include_once("header.php");
require_once("utilities.php");
include("db.php");
ini_set('display_errors','On');
ini_set('error_reporting',E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>

<div class="container">

<h2 class="my-3">Recommendations for you</h2>

<?php
  // This page is for showing a buyer recommended items based on their bid 
  // history. It will be pretty similar to browse.php, except there is no 
  // search bar. This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  
  // TODO: Check user's credentials (cookie/session).
  
  // TODO: Perform a query to pull up auctions they might be interested in.
  
  // TODO: Loop through results and print them out as list items.
  if (!(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true)) {
    echo"Please login first and make sure you have the right permission";
    exit();
  }
  else{
    if (!isset($_GET['page'])) {
      $curr_page = 1;
    }
    else {
      $curr_page = $_GET['page'];
    } 
    $user_id = $_SESSION['user_id'];
    // get the top 5 recent added items in the category of the user's latest 5 unique bid items
    $q = "SELECT *
    FROM Item
    JOIN -- categories of the latest 5 unique bid item from the user 
        (SELECT ic.categoryId, b.itemId, b.buyerId
          FROM Item_Category AS ic
          JOIN
              (SELECT itemId,
              MAX(bidTime),
              buyerId
              FROM bid
              WHERE buyerId = $user_id
              AND bidStatus <> 'Won'
              GROUP BY itemId
              ORDER BY MAX(bidTime) DESC
              LIMIT 5
              ) AS b 
          ON ic.itemId = b.itemId) AS a
    ON Item.Id = a.itemId
    JOIN
        (SELECT 
            COUNT(id) as num_of_bids,
            MAX(price) as current_price,
            itemId
         FROM bid
         GROUP BY itemId) AS b2
    ON Item.Id = b2.itemId
    WHERE id NOT IN 
        (SELECT DISTINCT itemId
        FROM bid
        WHERE buyerId = $user_id)
    AND itemStatus = 'Open'
";

$sql = "SELECT i.title, i.id AS itemId, i.description,
MAX(b.price) AS highest_bid_price,
COUNT(b.id) AS num_of_bids,
i.endTime,
c.name AS category_name
FROM Item i
JOIN Item_Category ic ON i.id = ic.itemId
JOIN Category c ON ic.categoryId = c.id
LEFT JOIN Bid b ON i.id = b.itemId
WHERE c.id IN (SELECT DISTINCT c.categoryId
      FROM WatchList w
      JOIN Item_Category c ON w.itemId = c.itemId
      WHERE w.userId = $user_id)
AND itemStatus = 'Open'
GROUP BY i.id, c.name
LIMIT 5";

$items = mysqli_query($con, $q);
$row_num_items = mysqli_num_rows($items);
$items1 = mysqli_query($con, $sql);
$row_num_items1 = mysqli_num_rows($items1);

if(mysqli_num_rows($items) == 0 and mysqli_num_rows($items1) == 0){
echo"There is no recommendation yet.";
exit();
}
elseif(!$items){
echo"There is an error when querying items";
}
else {
$results_per_page = 10;
$max_page = ceil($row_num_items / $results_per_page);
}
} 
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
            $current_price = $row['current_price'];
            $num_bids = $row['num_of_bids'];  
            $end_date = $row['endTime'];
            $category_name = $row['category_name'];
            // $end_date = date_create($row['endTime']);
            print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date, $category_name);
        endwhile;

        while($row = mysqli_fetch_assoc($items1)) : 
          $item_id = $row['itemId'];
          $title = $row['title'];  
          $description = $row['description'];  
          $current_price = $row['highest_bid_price'];  
          $num_bids = $row['num_of_bids'];  
          $end_date = $row['endTime'];
          $category_name = $row['category_name'];
          //$end_date = date_create($row['endTime']);
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
      <a class="page-link" href="recommendations.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
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
      <a class="page-link" href="recommendations.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }
  
  if ($curr_page != $max_page) {
    echo('
    <li class="page-item">
      <a class="page-link" href="recommendations.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
        <span class="sr-only">Next</span>
      </a>
    </li>');
  }
  

mysqli_close($con); ?>

<?php include_once("footer.php")?>