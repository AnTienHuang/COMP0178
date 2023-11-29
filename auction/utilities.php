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

function connect(){
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
  return $con;
}

function get_item($item_id){
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

  $q = "SELECT * FROM Item WHERE id = $item_id";

  $items = mysqli_query($con, $q);

  return $items;
}

function get_bid($item_id){
  $con = connect();

  $q = "SELECT * FROM Bid WHERE id = $bid_id";

  $bids = mysqli_query($con, $q);

  return $bids;
}

function get_latest_bids_by_item($item_id){
  $con = connect();

  $q = "SELECT
          bid_main.id,
          bid_main.bidStatus,
          bid_main.buyerId
        FROM Bid bid_main
        WHERE bid_main.itemId = $item_id
        AND bid_main.id = (
          SELECT id
          FROM Bid
          WHERE buyerId = bid_main.buyerId AND itemId = $item_id
          ORDER BY price DESC
          LIMIT 1)
  ";

  $bids = mysqli_query($con, $q);

  return mysqli_fetch_assoc($bids); 
}

function update_bids_status($item_id, $won_bid_id){
  $con = connect();

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
  $con = connect();

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

function notify_seller_close($item_id, $seller_id){

  $con = connect();
  while($item = mysqli_fetch_assoc(get_item($item_id))) :
    $item_status = $item['itemStatus'];
  endwhile;
  $created_time = date('Y-m-d H:i:s');
  $q = "INSERT INTO Notification (userId, itemId, notificationType, createdTime, message) 
  VALUES ($seller_id, $item_id, 'Auction Close', '$created_time', 'Auction ended as $item_status')
  ";

  if (mysqli_query($con, $q)) {
    $last_id = mysqli_insert_id($con);
    $success = true;
    echo "New record created successfully. Last inserted ID is: " . $last_id;
  } else {
    echo "Error: " . $q . "<br>" . mysqli_error($con);
    exit();
  }
  mysqli_close($con);
}

function notify_buyer_close($item_id){
  $con = connect();
  $created_time = date('Y-m-d H:i:s');
  while($item = mysqli_fetch_assoc(get_item($item_id))) :
    $item_title = $item['title'];
  endwhile;


  while($bid = mysqli_fetch_assoc(get_latest_bids_by_item($item_id))) :
    $bid_status = $bid['bidStatus'];
    $buyer_id = $bid['buyerId'];
    $bid_id = $bid['id'];
    $message = "Your bid for $item_title ended as $bid_status";

    $q = "INSERT INTO Notification (userId, itemId, notificationType, createdTime, message, bidID) 
    VALUES ($buyer_id, $item_id, 'Auction Close', '$created_time', '$message', $bid_id)
    ";
  
    if (mysqli_query($con, $q)) {
      $last_id = mysqli_insert_id($con);
      echo "New record created successfully. Last inserted ID is: " . $last_id;
    } else {
      echo "Error: " . $q . "<br>" . mysqli_error($con);
    }
  endwhile;

  mysqli_close($con);
}

function print_notifications($time, $message)
{
  // Print HTML
  echo('
    <li class="list-group-item d-flex justify-content-between">
    <div class="p-2 mr-5"><h5>'.$time.'</h5><br>' . $message . '</div>
  </li>'
  );
}
?>