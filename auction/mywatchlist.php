<?php
include_once("header.php");
require_once("utilities.php");
include("db.php");
ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>

<div class="container">

    <h2 class="my-3">My Watchlist</h2>

    <?php
    // Check if the user is logged in
    if (!(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true)) {
        echo "Please login first and make sure you have the right permission";
        exit();
    } else {
        $user_id = $_SESSION['user_id'];

        // TODO: Perform a query to pull up the items in the watchlist for the user.
        $q = "SELECT
                w.itemId,
                i.title,
                i.description,
                i.currentPrice,
                i.startTime,
                i.endTime
              FROM WatchList w
              JOIN Item i ON w.itemId = i.id
              WHERE w.userId = $user_id";

        $watchlist_items = mysqli_query($con, $q);

        if (!$watchlist_items) {
            echo "There is an error when querying watchlist items";
        } else {
            $row_num = mysqli_num_rows($watchlist_items);

            if ($row_num == 0) {
                echo "Your watchlist is empty.";
                exit();
            }
        }
    }
    ?>

    <!-- TODO: Loop through results and print them out as list items. -->
    <?php
    while ($row = mysqli_fetch_assoc($watchlist_items)) :
        $item_id = $row['itemId'];
        $title = $row['title'];
        $description = $row['description'];
        $current_price = $row['currentPrice'];
        $start_time = $row['startTime'];
        $end_time = $row['endTime'];

        // Use a function to print list items (similar to print_mybids_li)
        print_watchlist_li($item_id, $title, $description, $current_price, $start_time, $end_time);
    endwhile;
    ?>

    <div>
        <br>
        <br>
        <br>
        <p>Note: This is your watchlist. You can view and manage items you're interested in.</p>
    </div>

</div>

<?php mysqli_close($con); ?>
<?php include_once("footer.php") ?>

