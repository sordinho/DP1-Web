<?php
/**
 * Created by Davide Sordi
 * Using PhpStorm
 * Date: 12/06/2019
 */

include('functions.php');
checkHTTPS();
checkSession();

$user = $_SESSION['s267570_user'];
$countBooked = 0;
$seats = array();
foreach ($_POST as $key => $value) {
    if ($value == "BS") {
        $countBooked++;
        array_push($seats, $key);
    }
}

if ($countBooked == 0)
    die(header("location:user-home.php?error=true&errors=" . urlencode("You need to book at least one seat")));

$dbConn = DBConnect();

$queryGetBookings = "SELECT * FROM bookings WHERE user='$user' and status='booked' FOR UPDATE ";
$queryBuySeats = "UPDATE  bookings SET status='sold' WHERE user='$user' and status='booked'";

try {
    mysqli_autocommit($dbConn, false);

    if (!$resultGetBookings = mysqli_query($dbConn, $queryGetBookings))
        throw new Exception("failed to get user's bookings");

    if (mysqli_num_rows($resultGetBookings) == $countBooked) {
        // query update seats
        if (!$resultBuySeats = mysqli_query($dbConn, $queryBuySeats))
            throw new Exception("failed to buy user's seats");

        // commit updates
        if (!mysqli_commit($dbConn))
            throw new Exception("Commit failed");

        $dieMessage = "location:user-home.php?msg=true&info=" . urlencode("Seats purchased correctly");
    } else {
        // 1 or more seats were already purchased
        $dieMessage = "location:user-home.php?error=true&errors=" . urlencode("1 or more seats are not available");
    }
} catch (Exception $e) {
    $dieMessage = "location:user-home.php?error=true&errors=" . urlencode("Unexpected error occurred, reload and try again");
    mysqli_rollback($dbConn);
    mysqli_autocommit($dbConn, true);
    mysqli_close($dbConn);
    die(header($dieMessage));
}
mysqli_autocommit($dbConn, true);
mysqli_close($dbConn);
die(header($dieMessage));

?>