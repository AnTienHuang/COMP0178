<?php 
    include_once("header.php");
    require_once("utilities.php");
    include("db.php");
    ini_set('display_errors','On');
    ini_set('error_reporting',E_ALL);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // [debug]
    // foreach($_POST as $key => $value){
    //     echo"{$key} = {$value} <br>";
    // }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve values from the submitted form
        $item_id = $_POST['item_id'];
        $current_price = $_POST['current_price'];
        $new_bid_price = $_POST['new_bid_price'];
        $starting_price = $_POST['starting_price'];
        $item_title = $_POST['item_title'];
        $end_time = $_POST['end_time'];
    
        if(is_expired($end_time)){
            echo('<br><div class="text-center">Ohh, the auction has ended. ¯\ _(ツ)_/¯<br>So sad.</div><br><div class="text-center"><a href="listing.php?item_id=' . $item_id . '">Go back.</a></div>');
            exit(); 
        }
        if($new_bid_price < $starting_price){
            echo('<br><div class="text-center">You cannot place a bid with price lower than starting price.</div><br><div class="text-center"><a href="listing.php?item_id=' . $item_id . '">Go back.</a></div>');
            exit();
        }
        if($new_bid_price < $current_price){
            echo('<br><div class="text-center">You cannot place a bid with price lower than current price.</div><br><div class="text-center"><a href="listing.php?item_id=' . $item_id . '">Go back.</a></div>');
            exit();
        }
        else{
            $now = date('Y-m-d H:i:s');
            $buyer_id = $_SESSION['user_id'];
            $q = "INSERT INTO Bid (bidStatus, bidTime, buyerId, itemId, price) VALUES ('Winning', '$now', $buyer_id, $item_id, $new_bid_price)
            ";
            $success = false;
            if (mysqli_query($con, $q)) {
                $new_bid_id = mysqli_insert_id($con);
                $success = true;
                // echo "New record created successfully. Last inserted ID is: " . $last_id;
              } else {
                echo "Error: " . $q . "<br>" . mysqli_error($con);
                exit();
            }
            $q2 = "UPDATE Bid
                    SET bidStatus = 'Losing'
                    WHERE itemId = $item_id AND id != $new_bid_id
            ";
            if(mysqli_query($con, $q2)){
                $success = true;
                // echo "New record created successfully. Last inserted ID is: " . $last_id;
            }else{
                echo "Error: " . $q2 . "<br>". mysqli_error($con);
                exit();
            }
            
        // create notifications for winning bid
            $q3 = "INSERT INTO Notification (userId, itemId, notificationType, createdTime, message, bidID) 
            VALUES ($buyer_id, $item_id, 'Auction Update', '$now', 'Your bid for $item_title at £$new_bid_price is Winning', $new_bid_id)
            ";
            if(mysqli_query($con, $q3)){
                $success = true;
                // echo "New record created successfully. Last inserted ID is: " . $last_id;
            }else{
                echo "Error: " . $q3 . "<br>". mysqli_error($con);
                exit();
            }
        
        // create notifications for losing bids
            $q4 = "SELECT
                    bid_main.id,
                    bid_main.buyerId,
                    bid_main.price
                    FROM Bid bid_main
                    WHERE bid_main.itemId = $item_id
                    AND bid_main.id != $new_bid_id
                    AND bid_main.id = (
                    SELECT id
                    FROM Bid
                    WHERE buyerId = bid_main.buyerId AND itemId = $item_id
                    ORDER BY price DESC
                    LIMIT 1)
            ";

            $losing_bids = mysqli_query($con, $q4);
            while($losing_bid = mysqli_fetch_assoc($losing_bids)):
                $losing_buyer_id = $losing_bid['buyerId'];
                $losing_bid_id = $losing_bid['id'];
                $losing_bid_price = $losing_bid['price'];

                $q5 = "INSERT INTO Notification (userId, itemId, notificationType, createdTime, message, bidID) 
                VALUES ($losing_buyer_id, $item_id, 'Auction Update', '$now', 'Your bid for $item_title at £$losing_bid_price is Losing (Current price: $new_bid_price)', $losing_bid_id)
                ";
                if(mysqli_query($con, $q5)){
                    $success = true;
                    // echo "New record created successfully. Last inserted ID is: " . $last_id;
                }else{
                    echo "Error: " . $q5 . "<br>". mysqli_error($con);
                    exit();
                }           
            endwhile;
        // create notifications for watchlist items
            $q6 = "SELECT
                    userId AS watcher_id
                    FROM WatchList
                    WHERE itemId = $item_id
            ";

            $watchLists = mysqli_query($con, $q6);
            while($watchList = mysqli_fetch_assoc($watchLists)):
                $watcher_id = $watchList['watcher_id'];

                $q7 = "INSERT INTO Notification (userId, itemId, notificationType, createdTime, message) 
                VALUES ($watcher_id, $item_id, 'WatchList Update', '$now', 'The item \'$item_title\' in your watch list received a new bid at £$new_bid_price')
                ";
                if(mysqli_query($con, $q7)){
                    $success = true;
                    // echo "New record created successfully. Last inserted ID is: " . $last_id;
                }else{
                    echo "Error: " . $q7 . "<br>". mysqli_error($con);
                    exit();
                }           
            endwhile;
            if (!$success){
                echo('<br><div class="text-center">An error occured when placing bid, please try again.</div><br><div class="text-center"><a href="listing.php?item_id=' . $item_id . '">Go back.</a></div>');
                exit();
            }else{
                echo('<br><div class="text-center">Bid placed successfully.</div><br><div class="text-center"><a href="browse.php">Browse other items.</a></div>');
            }
        }

    }

    mysqli_close($con);
    include_once("footer.php")
?>