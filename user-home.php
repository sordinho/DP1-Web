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
    <ul>
        <li><a href="logout.php">Logout</a></li>
    </ul>
    <hr>
    <h1 class="aside_menu">Status</h1>
        <?php
        printSeatInformation(true);
        ?>
</aside>

<article>
    <section>
        <h1>Seats Table</h1>
        <table id='seatTable'>
            <?php
            printSeatTable(true);
            ?>
        </table>
    </section>
</article>


<footer><p class="copyright">Copyright © Polito-PD1 Web 2019 &nbsp;&nbsp;&nbsp;&nbsp; Designed by: Davide Sordi &nbsp;&nbsp;&nbsp;&nbsp;
        <a href="https://github.com/sordinho">GitHub</a></p></footer>
</body>
</html>
