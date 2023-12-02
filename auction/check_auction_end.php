<?php 
    include("db.php");
    include_once("utilities.php");
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
            i.title,
            i.reservedPrice,
            i.startingPrice,
            b.current_price,
            b.id AS bid_id,
            buyer.firstName AS won_by_first_name,
            buyer.lastName AS won_by_last_name
        FROM Item AS i
        LEFT JOIN
            (SELECT 
                MAX(price) AS current_price,
                buyerId,
                id,
                itemId
            FROM Bid
            GROUP BY itemId, buyerId, id
            ORDER BY current_price DESC) AS b 
        ON b.itemId = i.id
        JOIN User AS seller ON seller.id = i.sellerId
        JOIN User AS buyer ON buyer.id = b.buyerId
        WHERE i.itemStatus = 'Open'
        AND i.endTime < '$time'
        LIMIT 1
    ";
    $items = mysqli_query($con, $q);
    $row_num = mysqli_num_rows($items);
    if(mysqli_num_rows($items) == 0){
        // echo"<br>There is no auction ending now.<br>";
    }
    else{
        // echo"<br>";
        // echo"q: $q";
    // create auction close notification for seller
        while($row = mysqli_fetch_assoc($items)) :
            $item_id = $row['item_id'];
            $item_title = $row['title'];
            $seller_id = $row['sellerId'];
            $reserved_price = $row['reservedPrice'];
            $starting_price = $row['startingPrice'];
            $current_price = $row['current_price'];
            $won_by = $row['won_by_first_name'] . " " . $row['won_by_last_name'];
            $bid_status = 'Lost';
            $item_status = 'Closed-No-bid';
            $won_bid_id = 000;
            if ($current_price > $starting_price && empty($reserved_price) && !empty($current_price)){
                $item_status = 'Closed-Won';
                $won_bid_id = $row['bid_id'];
                $q2 = "INSERT INTO Notification (userId, itemId, notificationType, createdTime, message) 
                VALUES ($seller_id, $item_id, 'Auction Close', '$time', 'Auction for $item_title ended as Closed-Won and is won by $won_by.')
                ";
            } elseif ($current_price > $starting_price && $current_price > $reserved_price && !empty($reserved_price) && !empty($current_price)){
                $item_status = 'Closed-Won';
                $won_bid_id = $row['bid_id'];
                $q2 = "INSERT INTO Notification (userId, itemId, notificationType, createdTime, message) 
                VALUES ($seller_id, $item_id, 'Auction Close', '$time', 'Auction for $item_title ended as Closed-Won and is won by $won_by.')";
            }
            else{
                $q2 = "INSERT INTO Notification (userId, itemId, notificationType, createdTime, message) 
                VALUES ($seller_id, $item_id, 'Auction Close', '$time', 'Auction for $item_title ended as $item_status')
                ";
            }
            // echo"<br> WON_Bid_ID: $won_bid_id";
            // echo"<br> Item Status: $item_status";
            update_bids_status($item_id, $won_bid_id);
            update_item_status($item_id, $item_status);


            // echo"<br> Q: $q2 <br>";
            if (mysqli_query($con, $q2)) {
              $last_id = mysqli_insert_id($con);
              $success = true;
            //   echo "New record created successfully. Last inserted ID is: " . $last_id;
            } else {
              echo "Error: " . $q2 . "<br>" . mysqli_error($con);
              exit();
            }
// create auction close notification for buyers;
            $q3 = "SELECT
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
            // echo"<br> Q: $q3 <br>";
        
            $bids = mysqli_query($con, $q3);
            while($bid = mysqli_fetch_assoc($bids)) :
                $bid_status = $bid['bidStatus'];
                $buyer_id = $bid['buyerId'];
                $bid_id = $bid['id'];
                $message = "Your bid for $item_title ended as $bid_status";
            
                $q3 = "INSERT INTO Notification (userId, itemId, notificationType, createdTime, message) 
                VALUES ($buyer_id, $item_id, 'Bid Close', '$time', '$message')
                ";
              
                if (mysqli_query($con, $q3)) {
                  $last_id = mysqli_insert_id($con);
                //   echo "New record created successfully. Last inserted ID is: " . $last_id;
                } else {
                  echo "Error: " . $q3 . "<br>" . mysqli_error($con);
                }
              endwhile;

        endwhile;
    }
    mysqli_close($con);
    ?>


