<?php 
    include_once("header.php");
    require("utilities.php");
    include("db.php");
    ini_set('display_errors','On');
    ini_set('error_reporting',E_ALL);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // Get info from the URL:
    $item_id = $_GET['item_id'];
    $user_id = $_SESSION['user_id'];

    // TODO: Use item_id to make a query to the database.
    $q = "SELECT 
              main.itemId, 
              main.categoryId, 
              main.category_name, 
              main.highest_bid_price, 
              main.num_of_bids, 
              main.title,
              main.description,
              main.endTime,
              main.startingPrice,
              u.firstName AS leading_buyer_first_name, 
              u.lastName AS leading_buyer_last_name
          FROM (
              SELECT 
                  ic.itemId, 
                  ic.categoryId, 
                  c.name AS category_name, 
                  MAX(b.price) AS highest_bid_price, 
                  COUNT(b.id) AS num_of_bids,
                  i.title,
                  i.description,
                  i.endTime,
                  MAX(b.id) AS max_bid_id,
                  i.startingPrice
              FROM Item_Category ic
              JOIN Category c ON ic.categoryId = c.id
              JOIN Item i ON ic.itemId = i.id
              LEFT JOIN Bid b ON i.id = b.itemId
              WHERE i.Id = $item_id
              GROUP BY ic.itemId, ic.categoryId, c.name
              ORDER BY highest_bid_price DESC
          ) AS main
          LEFT JOIN Bid b ON main.max_bid_id = b.id
          LEFT JOIN User u ON b.buyerId = u.id
          ";
    $items = mysqli_query($con, $q);
    $row_num = mysqli_num_rows($items);
    while($row = mysqli_fetch_assoc($items)) : 
      $item_id = $row['itemId'];
      $title = $row['title'];  
      $description = $row['description'];  
      $current_price = $row['highest_bid_price'];  
      $num_bids = $row['num_of_bids'];  
      $end_time = $row['endTime'];  
      $leading_buyer_name = $row['leading_buyer_first_name'] . " " . $row['leading_buyer_last_name'];
      $starting_price = $row['startingPrice'];
  endwhile;
    // TODO: Note: Auctions that have ended may pull a different set of data,
    //       like whether the auction ended in a sale or was cancelled due
    //       to lack of high-enough bids. Or maybe not.
    
    // Calculate time to auction end:
    $now = new DateTime();
    $end_time_formatted = date_create($end_time);
    if ($now < $end_time_formatted) {
      $time_remaining = ' (' . display_time_remaining($end_time) . ')';
    }
    
    // TODO: If the user has a session, use it to make a query to the database
    //       to determine if the user is already watching this item.
    //       For now, this is hardcoded.
    $q0 = "SELECT * 
           FROM WatchList
           WHERE itemId = $item_id
           AND userId = $user_id 
           ";
    $r = mysqli_query($con, $q0);
    if(mysqli_num_rows($r) > 0){
      $watching = true;
    }else{
      $watching = false;
    }
?>


<div class="container">

<div class="row"> <!-- Row #1 with auction title + watch button -->
  <div class="col-sm-8"> <!-- Left col -->
    <h2 class="my-3"><?php echo($title); ?></h2>
  </div>
  <div class="col-sm-4 align-self-center"> <!-- Right col -->
<?php
  /* The following watchlist functionality uses JavaScript, but could
     just as easily use PHP as in other places in the code */
  if ($now < $end_time_formatted):
?>
    <div id="watch_nowatch" <?php if($watching) echo('style="display: none"');?> >
      <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">+ Add to watchlist</button>
    </div>
    <div id="watch_watching" <?php if(!$watching) echo('style="display: none"');?> >
      <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
      <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">Remove watch</button>
    </div>
<?php endif /* Print nothing otherwise */ ?>
  </div>
</div>

<div class="row"> <!-- Row #2 with auction description + bidding info -->
  <div class="col-sm-8"> <!-- Left col with item info -->

    <div class="itemDescription">
    <?php echo($description); ?>
    </div>

  </div>

  <div class="col-sm-4"> <!-- Right col with bidding info -->

    <p>
<?php if ($now > $end_time_formatted): ?>
     This auction ended <?php echo"{$end_time_formatted->format('Y-m-d H:i:s')}" ?>
     
     <!-- TODO: Print the result of the auction here? -->
<?php else: ?>
     Auction ends <?php echo"{$end_time} <br> {$time_remaining}" ?></p>  
     <p class="lead">Starting price: £<?php echo(number_format($starting_price, 2)); ?></p>
     <p class="lead">Current bid: £<?php echo(number_format($current_price, 2)); echo" by " . $leading_buyer_name ?></p>

    <!-- Bidding form -->
    <form method="POST" action="place_bid.php">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text">£</span>
        </div>
      <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
      <input type="hidden" name="current_price" value="<?php echo $current_price; ?>">
      <input type="hidden" name="starting_price" value="<?php echo $starting_price; ?>">
      <input type="hidden" name="end_time" value="<?php echo $end_time; ?>">
	    <input type="number" class="form-control" name="new_bid_price">
      </div>
      <button type="submit" class="btn btn-primary form-control">Place bid</button>
    </form>
<?php endif ?>

  
  </div> <!-- End of right col with bidding info -->

</div> <!-- End of row #2 -->



<?php mysqli_close($con); ?>
<?php include_once("footer.php")?>


<script> 
  // JavaScript functions: addToWatchlist and removeFromWatchlist.

  function addToWatchlist(button) {
    console.log("These print statements are helpful for debugging btw");

    // This performs an asynchronous call to a PHP function using POST method.
    // Sends item ID as an argument to that function.
    $.ajax('watchlist_funcs.php', {
      type: "POST",
      data: {functionname: 'add_to_watchlist', arguments: [<?php echo($item_id);?>]},

      success: 
        function (obj, textstatus) {
          // Callback function for when call is successful and returns obj
          console.log("Success");
          var objT = obj.trim();
          console.log(objT);
          if (objT == "success") {
            $("#watch_nowatch").hide();
            $("#watch_watching").show();
          }
          else {
            var mydiv = document.getElementById("watch_nowatch");
            mydiv.appendChild(document.createElement("br"));
            mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
          }
        },

      error:
        function (obj, textstatus) {
          console.log("Error");
        }
    }); // End of AJAX call

  } // End of addToWatchlist func

  function removeFromWatchlist(button) {
    // This performs an asynchronous call to a PHP function using POST method.
    // Sends item ID as an argument to that function.
    $.ajax('watchlist_funcs.php', {
      type: "POST",
      data: {functionname: 'remove_from_watchlist', arguments: [<?php echo($item_id);?>]},

      success: 
        function (obj, textstatus) {
          // Callback function for when call is successful and returns obj
          console.log("Success");
          var objT = obj.trim();
  
          if (objT == "success") {
            $("#watch_watching").hide();
            $("#watch_nowatch").show();
          }
          else {
            var mydiv = document.getElementById("watch_watching");
            mydiv.appendChild(document.createElement("br"));
            mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
          }
        },

      error:
        function (obj, textstatus) {
          console.log("Error");
        }
    }); // End of AJAX call

  } // End of addToWatchlist func
</script>