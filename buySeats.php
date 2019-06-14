<?php
/**
 * Created by Davide Sordi
 * Using PhpStorm
 * Date: 12/06/2019
 *
 * page called to buy some booked seats
 */

include('functions.php');
checkHTTPS();
checkSession();


$user = $_SESSION['s267570_user'];

// check there is at least one booked seat coming from user
$countBooked = 0;
foreach ($_POST as $key => $value) {
    if ($value == "BS") {
        $countBooked++;
    }
}

// die with error in url
if ($countBooked == 0)
    die(header("location:user-home.php?error=true&errors=" . urlencode("You need to book at least one seat")));

// create DB connection
$dbConn = DBConnect();

// utility queries
$queryGetBookings = "SELECT * FROM bookings WHERE user='$user' and status='booked' FOR UPDATE ";
$queryBuySeats = "UPDATE  bookings SET status='sold' WHERE user='$user' and status='booked'";
$queryFreeSeats = "DELETE FROM  bookings where status='booked' and user='$user'";


try {
    mysqli_autocommit($dbConn, false); // disable autocommit

    // get all bookings for the current user
    if (!$resultGetBookings = mysqli_query($dbConn, $queryGetBookings))
        throw new Exception("failed to get user's bookings");

    // if number of booked seats correspond to the one evaluated at top of the page we can proceed
    if (mysqli_num_rows($resultGetBookings) == $countBooked) {

        // query update seats
        if (!$resultBuySeats = mysqli_query($dbConn, $queryBuySeats))
            throw new Exception("failed to buy user's seats");

        // commit updates
        if (!mysqli_commit($dbConn))
            throw new Exception("Commit failed");

        // set a info message to inform user all was good
        $dieMessage = "location:user-home.php?msg=true&info=" . urlencode("Seats purchased correctly");
    }
    // if number of booked seats does not correspond we have a problem
    else {
        // 1 or more seats were already purchased, we try to free all previous bookings for the user
        if (!$resultFreeBookings = mysqli_query($dbConn, $queryFreeSeats))
            throw new Exception("failed to free user's bookings");

        // set a error message to inform user some seats were sold
        $dieMessage = "location:user-home.php?error=true&errors=" . urlencode("1 or more seats are not available");
    }
} catch (Exception $e) {
    // in case of exception we set error message for the user, do rollback and re-enable autocommit and close connection
    $dieMessage = "location:user-home.php?error=true&errors=" . urlencode("Unexpected error occurred, reload and try again");
    mysqli_rollback($dbConn);
    mysqli_autocommit($dbConn, true);
    mysqli_close($dbConn);
    die(header($dieMessage));
}
//re-enable autocommit and close connection. then redirect with message
mysqli_autocommit($dbConn, true);
mysqli_close($dbConn);
die(header($dieMessage));

?>