<?php

/**
 * Created by Davide Sordi
 * Using PhpStorm
 * Date: 11/06/2019
 *
 * page called for booking, or freeing a seat
 */

include('functions.php');
checkSession();

// if the request is not a valid one redirect to user home.
if ((!isset($_SESSION['s267570_user'])) || (!isset($_POST['seatID'])) || (!isset($_POST['newStatus']))) {
    echo "error";
    die(header("location:user-home.php"));
}

$user = $_SESSION['s267570_user'];

// set a new DB connection
$dbConn = DBConnect();

// for security we need to prevent sql injection from post requests
$seat = mysqli_real_escape_string($dbConn, $_POST['seatID']);
$newStatus = mysqli_real_escape_string($dbConn, $_POST['newStatus']);

// Utility queries
$queryCheckFree = "SELECT * from bookings where seat='$seat' FOR UPDATE";
$queryInsertBooking = "INSERT INTO  bookings(seat,status,user) values ('$seat','$newStatus','$user')";
$queryUpdateBooking = "UPDATE  bookings SET status='$newStatus', user='$user' WHERE seat='$seat'";
$queryDeleteBooking = "DELETE FROM  bookings WHERE seat='$seat'";
$queryCheckMyBooking = "SELECT * from bookings where seat='$seat' FOR UPDATE";


try {
    mysqli_autocommit($dbConn, false); //disable autocommit
    if ($newStatus == 'free') {

        // check if booking was mine and set it to free (delete from db)
        if (!$resultCheckBeforeFree = mysqli_query($dbConn, $queryCheckMyBooking))
            throw new Exception("failed query check seat before freeing");

        $seatStatus = mysqli_fetch_assoc($resultCheckBeforeFree);
        if ($seatStatus) {
            if ($seatStatus['user'] == $user) {
                if (!mysqli_query($dbConn, $queryDeleteBooking))
                    throw new Exception("failed to update booking");
                $response = 'freeSeat';
            } else {
                // seat was not related to the user (booked or bought from another user)
                $response = $seatStatus['status'];
            }
        }
    } elseif ($newStatus == 'booked') {

        // try to book the seat if it is not sold
        if (!$resultCheckFree = mysqli_query($dbConn, $queryCheckFree))
            throw new Exception("failed query check seat free before booking");

        $seatStatus = mysqli_fetch_assoc($resultCheckFree);
        if ($seatStatus) {
            // was booked or sold
            // query update or exit
            if ($seatStatus['status'] == "sold") {
                // seat was already sold
                $response = 'soldSeat';
            } else {
                // update Seat with booking of current user
                if (!mysqli_query($dbConn, $queryUpdateBooking))
                    throw new Exception("failed to insert new booking");
                $response = 'myBookedSeat';
            }
        } else {
            // was free
            // query insert booking
            if (!mysqli_query($dbConn, $queryInsertBooking))
                throw new Exception("failed to insert new booking");
            $response = 'myBookedSeat';
        }


    }
    // commit queries
    if (!mysqli_commit($dbConn))
        throw new Exception("Commit failed");

    // all was good return
    echo $response;

} catch (Exception $e) {
    mysqli_rollback($dbConn);
    mysqli_autocommit($dbConn, true);
    mysqli_close($dbConn);
}

// enable autocommit and close connection
mysqli_autocommit($dbConn, true);
mysqli_close($dbConn);

?>