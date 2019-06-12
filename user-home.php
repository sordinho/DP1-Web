<?php

/**
 * Created by Davide Sordi
 * Using PhpStorm
 * Date: 06/06/2019
 */

include('functions.php');
checkHTTPS();
checkSession();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Your Home</title>
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="functions.js"></script>
    <noscript>
        <div class='noscript'>
            <h1>Attention: you need to enable javascript in order to use the website.</h1>
        </div>
    </noscript>
</head>
<body>
<script>checkCookies();</script>
<header>
    <h1>Sordinho AirLines</h1>
</header>
<form action="buySeats.php" method="post">
    <aside>
        <h1 class="aside_menu">Menu</h1>
        <h4 class="aside_menu">Welcome back <br><br>
            <?php
            if (isset($_SESSION['s267570_user']))
                echo $_SESSION['s267570_user'];
            else
                myRedirect("login.php");
            ?>
        </h4>
        <a class="buttonLink" href="logout.php">Logout</a>
        <hr>
        <h1 class="aside_menu">Status</h1>
        <?php
        printSeatInformation(true);


        if (isset($_GET['error']))
            echo "<div class='error'>" . $_GET["errors"] . "</div>";
        if (isset($_GET['msg']))
            echo "<div class='infoOK'>" . $_GET["info"] . "</div>";

        ?>
    </aside>

    <article>
        <section>
            <h1>Seats Map</h1>
            <table id='seatTable'>
                <?php
                printSeatTable(true);
                ?>
            </table>
            <p>Click on a <mark class="freeSeat">free seat</mark> for booking it. Click on a seat <mark class="myBookedSeat">booked by yourself</mark> for freeing it</p>
        </section>
    </article>
</form>

<footer><p class="copyright">Copyright © Polito-PD1 Web 2019 &nbsp;&nbsp;&nbsp;&nbsp; Designed by: Davide Sordi &nbsp;&nbsp;&nbsp;&nbsp;
        <a href="https://github.com/sordinho">GitHub</a></p></footer>
</body>
</html>
