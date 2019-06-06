<?php
include('functions.php');
checkHTTPS();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Register</title>
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

    <ul>
        <li><a href="https://localhost/DP1-Web/index.php">Home</a></li>
        <li><a href="https://localhost/DP1-Web/login.php">Login</a></li>
    </ul>
</aside>

<article>
    <section>
        <h1 class="page_title">Register</h1>

        <form action="" method="post" onsubmit="return validateForm('register')">
            <div class="input_log_reg">
                <label for="mail"><b>Username</b></label>
                <input type="email" name="mail" id="mail" placeholder="Enter Email" required>
            </div>
            <div class="input_log_reg">
                <label for="passwd"><b>Password</b></label>
                <input type="password" name="password" id="passwd" placeholder="Enter Password" required>
            </div>
            <button type="submit" class="log_reg">Register</button>
        </form>

    </section>
</article>


<footer><p class="copyright">Copyright © Polito-PD1 Web 2019 &nbsp;&nbsp;&nbsp;&nbsp; Designed by: Davide Sordi &nbsp;&nbsp;&nbsp;&nbsp;
        <a href="https://github.com/sordinho">GitHub</a></p></footer>
</body>
</html>