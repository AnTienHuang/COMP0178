<?php 
    include_once("db.php");
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
        // echo"<br>There is no auction ending now.<br>";
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
    ?>


