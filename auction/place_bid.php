<?php 
    include_once("header.php");
    require_once("utilities.php");
    include_once("db.php");
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
                $last_id = mysqli_insert_id($con);
                $success = true;
                // echo "New record created successfully. Last inserted ID is: " . $last_id;
              } else {
                echo "Error: " . $q . "<br>" . mysqli_error($con);
                exit();
            }
            $q2 = "UPDATE Bid
                    SET bidStatus = 'Losing'
                    WHERE itemId = $item_id AND id != $last_id
            ";
            if(mysqli_query($con, $q2)){
                $success = true;
                // echo "New record created successfully. Last inserted ID is: " . $last_id;
            }else{
                echo "Error: " . $q2 . "<br>". mysqli_error($con);
                exit();
            } 
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