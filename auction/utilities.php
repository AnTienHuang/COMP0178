<?php

function is_expired($end_time){
  $now = new DateTime();
  $end_time_formatted = date_create($end_time);
  if ($now > $end_time_formatted) {
    return True;
  }
  return False;
}

// display_time_remaining:
// Helper function to help figure out what time to display
function display_time_remaining($end_time) {

    if (is_expired($end_time)) {
      return 'This auction has ended';
    }
    else {
      $now = new DateTime();
      $end_time_formatted = date_create($end_time);
      $time_to_end = date_diff($now, $end_time_formatted);
      if ($time_to_end->days == 0 && $time_to_end->h == 0) {
        // Less than one hour remaining: print mins + seconds:
        $time_remaining = $time_to_end->format('%im %Ss');
      }
      else if ($time_to_end->days == 0) {
        // Less than one day remaining: print hrs + mins:
        $time_remaining = $time_to_end->format('%hh %im');
      }
      else {
        // At least one day remaining: print days + hrs:
        $time_remaining = $time_to_end->format('%ad %hh');
      }
      $time_remaining = $time_remaining . ' remaining';
      return $time_remaining;
    }
}

// print_listing_li:
// This function prints an HTML <li> element containing an auction listing
function print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time, $category)
{
  // Truncate long descriptions
  if (strlen($desc) > 250) {
    $desc_shortened = substr($desc, 0, 250) . '...';
  }
  else {
    $desc_shortened = $desc;
  }
  
  // Fix language of bid vs. bids
  if ($num_bids == 1) {
    $bid = ' bid';
  }
  else {
    $bid = ' bids';
  }
  
  $time_remaining = display_time_remaining($end_time);
  
  // Print HTML
  echo('
    <li class="list-group-item d-flex justify-content-between">
    <div class="p-2 mr-5"><h5><a href="listing.php?item_id=' . $item_id . '">' . $title . '</a></h5>(Category: ' . $category . ')<br><br>' . $desc_shortened . '</div>
    <div class="text-center text-nowrap"><span style="font-size: 1.5em">£' . number_format($price, 0) . '</span><br/>' . $num_bids . $bid . '<br/>' . $time_remaining . '</div>
  </li>'
  );
}

function print_mybids_li($item_id, $title, $desc, $current_price, $bid_price, $num_bids, $end_time, $bid_status)
{
  // Truncate long descriptions
  if (strlen($desc) > 250) {
    $desc_shortened = substr($desc, 0, 250) . '...';
  }
  else {
    $desc_shortened = $desc;
  }
  
  // Fix language of bid vs. bids
  if ($num_bids == 1) {
    $bid = ' bid';
  }
  else {
    $bid = ' bids';
  }
  
  $time_remaining = display_time_remaining($end_time);
  
  // Print HTML
  echo('
    <li class="list-group-item d-flex justify-content-between">
    <div class="p-2 mr-5"><h5><a href="listing.php?item_id=' . $item_id . '">' . $title . '</a></h5>(Bid Status: ' . $bid_status . ')<br>' . '<br>' . $desc_shortened . '</div>
    <div class="text-center text-nowrap"><span style="font-size: 1.5em">Current price: £' . number_format($current_price, 0) . '</span><br><span style="font-size: 1.5em">Your price: £' . number_format($bid_price, 0) . '</span><br/>' . $num_bids . $bid . '<br/>' . $time_remaining . '</div>
  </li>'
  );
}

function update_bids_status($item_id, $won_bid_id){
  $db_server = "localhost";
  $db_user = "example";
  $db_password = "";
  $db_name = "auction_test";
  $con = "";

  // try{
  $con = mysqli_connect($db_server,
      $db_user,
      $db_password,
      $db_name
  );

  $q = "UPDATE Bid
  SET bidStatus = 'Lost'
  WHERE itemId = $item_id
  AND id != $won_bid_id
  ";
  if(mysqli_query($con, $q)){
    echo "Successfully updated Lost bids";
  }else{
    echo "Error: " . $q . "<br>". mysqli_error($con);
    exit();
  }
  $q2 = "UPDATE Bid
  SET bidStatus = 'Won'
  WHERE id = $won_bid_id
  ";
  if(mysqli_query($con, $q2)){
    echo "Successfully updated Won bid";
  }else{
    echo "Error: " . $q2 . "<br>". mysqli_error($con);
    exit();
  }
  mysqli_close($con);
}

function update_item_status($item_id, $item_status){
  $db_server = "localhost";
  $db_user = "example";
  $db_password = "";
  $db_name = "auction_test";
  $con = "";

  // try{
  $con = mysqli_connect($db_server,
      $db_user,
      $db_password,
      $db_name
  );
  $q = "UPDATE Item
  SET itemStatus = '$item_status'
  WHERE id = $item_id
  ";
  if(mysqli_query($con, $q)){
    echo "Successfully updated closed item";
  }else{
    echo "Error: " . $q . "<br>". mysqli_error($con);
    exit();
  }
  mysqli_close($con);
}

function check_auction_end($con){
  ini_set('display_errors','On');
  ini_set('error_reporting',E_ALL);
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

  $time = date('Y-m-d H:i:s');
  // 2023-11-22 21:08:00

  $q = "SELECT
          i.id AS item_id,
          i.sellerId, 
          i.itemStatus,
          i.endTime,
          i.reservedPrice,
          i.startingPrice,
          b.current_price,
          b.buyerId,
          b.id AS bid_id,
          seller.email AS seller_email,
          buyer.email AS buyer_email
      FROM Item AS i
      LEFT JOIN
          (SELECT 
              MAX(price) AS current_price,
              buyerId,
              id,
              itemId
          FROM Bid
          GROUP BY itemId, buyerId, id
          ORDER BY current_price DESC
          LIMIT 1) AS b 
      ON b.itemId = i.id
      JOIN User AS seller ON seller.id = i.sellerId
      LEFT JOIN User AS buyer ON buyer.id = b.buyerId
      WHERE i.itemStatus = 'Open'
      AND i.endTime < '$time'
  ";
  $items = mysqli_query($con, $q);
  $row_num = mysqli_num_rows($items);
  if(mysqli_num_rows($items) == 0){
      echo"<br>There is no auction ending now.<br>";
  }
  else{
      while($row = mysqli_fetch_assoc($items)) :
          $item_id = $row['item_id'];
          $seller_id = $row['sellerId'];
          // $end_time = $row['endTime'];
          $reserved_price = $row['reservedPrice'];
          $starting_price = $row['startingPrice'];
          $current_price = $row['current_price'];
          $buyer_id = $row['buyerId'];
          $seller_email = $row['seller_email'];
          $buyer_email = $row['buyer_email'];
          $bid_status = 'Lost';
          $item_status = 'Closed-No-bid';
          $won_bid_id = 000;
          if ($current_price > $starting_price && empty($reserved_price) && !empty($current_price)){
              $item_status = 'Closed-Won';
              $won_bid_id = $row['bid_id'];
          } elseif ($current_price > $starting_price && $current_price > $reserved_price && !empty($reserved_price) && !empty($current_price)){
              $item_status = 'Closed-Won';
              $won_bid_id = $row['bid_id'];
          }
          // notify_seller($item_id, $item_status, $seller_email, $seller_id);
          // notify_buyers($item_id);
          update_bids_status($item_id, $won_bid_id);
          update_item_status($item_id, $item_status);
      endwhile;
  }
  mysqli_close($con);
}
?>