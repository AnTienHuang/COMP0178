<?php include_once("header.php")?>
<div class="container my-5">
<?php 
    include "db.php";
    ini_set('display_errors','On');
    ini_set('error_reporting',E_ALL);
    // foreach($_POST as $key => $value){
    //     echo"{$key} = {$value} <br>";
    // }
    
    // Get variables
    $seller_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_time = date('Y-m-d H:i:s');
    $end_time = $_POST['end_time'];
    $reserved_price = $_POST['reserved_price'];
    $starting_price = $_POST['starting_price'];
    $category_id = $_POST['category'];
    
    // Check if variables are valid
    // 1. check is category set
    // echo "categoryID: ";
    // echo $category_id;
    if (empty($category_id)){
        echo('<div class="text-center">Select a category to create you auction.</div><br><dib><a href="create_auction.php">Go back.</a></div>');
        exit();
    }
    // 2. check if reserved price has value
    if (is_null($reserved_price)){
        // echo $reserved_price . "<br>";
        echo('<div class="text-center">Reserved price cannot be null.</div><br><dib><a href="create_auction.php">Go back.</a></div>');
        exit();
    }
    // 3. check if end time is valid and format for mysql
    if(strlen($end_time) == 16){
        $dateTime = date_create($end_time);
        $end_time_formatted = $dateTime->format('Y-m-d H:i:s');
    }
    else{
        echo('<div class="text-center">Please input a valid auction end time.</div><br><dib><a href="create_auction.php">Go back.</a></div>');
        exit();
    }
    // 4. check is end date in past
    if ($start_time > $end_time_formatted){
        echo('<div class="text-center">End time cannot be in past.</div><br><dib><a href="create_auction.php">Go back.</a></div>');
        exit();
    }
    
    
    // Insert new item
    $q = "INSERT INTO Item (sellerId, title, description, itemStatus, startTime, endTime, reservedPrice, startingPrice) 
    VALUES ($seller_id, '$title', '$description', 'Open', '$start_time', '$end_time_formatted', $reserved_price, $starting_price)
    ";
    // echo $q;
    // echo"<br>";
    $success = false;
    if (mysqli_query($con, $q)) {
        $last_id = mysqli_insert_id($con);
        $success = true;
        // echo "New record created successfully. Last inserted ID is: " . $last_id;
      } else {
        echo "Error: " . $q . "<br>" . mysqli_error($con);
        exit();
    }
    if (!$success){
        echo('<div class="text-center">An error occurred during creating auction, please check you input and try again.</div><br><dib><a href="create_auction.php">Go back.</a></div>');
        exit();
    }
    
    // Insert new Item_Category
    $q2 = "INSERT INTO Item_Category (itemId, categoryId)
    VALUES ('$last_id', '$category_id')";
    if (mysqli_query($con, $q2)) {
        $last_id2 = mysqli_insert_id($con);
        $success = true;
        // echo "New record created successfully. Last inserted ID is: " . $last_id;
      } else {
        echo "Error: " . $q2 . "<br>" . mysqli_error($con);
        exit();
    }
    if (!$success){
        echo('<div class="text-center">An error occurred during creating auction, please check you input and try again.</div><br><dib><a href="create_auction.php">Go back.</a></div>');
        exit();
    } 
    // If all is successful, let user know.
    echo('<div class="text-center">Auction successfully created! <a href="mylistings.php">View your new listing.</a></div>');

    
    ?>  
</div>
<?php mysqli_close($con); ?>
<?php include_once("footer.php")?>