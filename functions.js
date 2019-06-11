/**
 * Created by Davide Sordi
 * Using PhpStorm
 * Date: 06/06/2019
 */

function validateEmail(emailToCheck) {
    var regularExpression = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (regularExpression.test(emailToCheck)) {
        return true;
    } else {
        alert("This is not a valid mail address");
        return false;
    }
}

function validatePassword(psswdToCheck) {
    var regularExpression = /(\.*[a-z]+\.*[0-9]+\.*)|(\.*[a-z]+[A-Z]+\.*)|(\.*[0-9]+[a-z]+\.*)|(\.*[A-Z]+[a-z]+\.*)/;
    if (regularExpression.test(psswdToCheck)) {
        return true;
    } else {
        alert(
            "Password must be long at least 2 chars and contains at least: 1 lowercase and, 1 uppercase or a digit "
        );
        return false;
    }
}

function validateForm(page) {
    if (page == "login") {
        var emailToCheck = document.getElementById("mail").value;
        return validateEmail(emailToCheck);
    }
    if (page == "register") {
        var emailToCheck = document.getElementById("mail").value;
        var psswdToCheck = document.getElementById("passwd").value;
        return validateEmail(emailToCheck) && validatePassword(psswdToCheck);
    }
}

function checkCookies() {
    var cookies = navigator.cookieEnabled;
    if (!cookies) {
        document.write(
            "<div class='noscript'><h1>Attention: you need to enable COOKIES in order to use the website.</h1></div>"
        );
    }
}

function ajaxRequest() {
    try {
        // Non IE Browser?
        var request = new XMLHttpRequest();
    } catch (e1) {
        // No
        try {
            // IE 6+?
            request = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e2) {
            // No
            try {
                // IE 5?
                request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e3) {
                // No AJAX Support
                request = false;
            }
        }
    }
    return request;
}

function bookAjax(seatID) {
    var className = document.getElementById(seatID).className;
    var newStatus = "";
    if ((className === "freeSeat") || (className === "bookedSeat")) {
        newStatus = "booked";
    }
    if (className === "myBookedSeat") {
        newStatus = "free";
    }

    // Ajax request
    req = ajaxRequest();
    req.open("POST", "bookSeat.php", true);
    req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    req.onreadystatechange = function () {
        updateSeatStatus(seatID);
    };
    req.send("seatID=" + seatID + "&newStatus=" + newStatus);
}

function updateSeatStatus(seatID) {
    if (req.readyState === 4) {
        document.getElementById(seatID).className = req.responseText;
        if (document.getElementById(seatID).className === 'soldSeat') {
            document.getElementById(seatID).disabled = true;
            alert('Seat: ' + seatID + " is sold.")
        }
    }
}
