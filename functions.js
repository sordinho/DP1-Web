/**
 * Created by Davide Sordi
 * Using PhpStorm
 * Date: 06/06/2019
 */

/**
 * This function uses a regular expression to validate an emailAddress
 * @param emailToCheck: email address to validate
 * @returns {boolean} : valid or not (true / false)
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

/**
 * This function validate a password as described in project requirement
 *
 * @param psswdToCheck: password to validate
 * @returns {boolean} : valid or not (true / false)
 */
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

/**
 * Wrapper function for validation, in case of registration we need to validate both mail and pass
 * while in case of login we check only mail validity
 * @param page
 * @returns {boolean}
 */
function validateForm(page) {
    if (page === "login") {
        var emailToCheck = document.getElementById("mail").value;
        return validateEmail(emailToCheck);
    }
    if (page === "register") {
        var emailToCheck = document.getElementById("mail").value;
        var psswdToCheck = document.getElementById("passwd").value;
        return validateEmail(emailToCheck) && validatePassword(psswdToCheck);
    }
}

/**
 * Javascript function to check if cookies are enabled.
 * Redirect on error page in case are not enabled
 */
function checkCookies() {
    var cookies = navigator.cookieEnabled;
    if (!cookies) {
        window.location.replace("error.php");
    }
}

/**
 * Ajax request gereator based on used browser
 */
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

/**
 * Ajax async request for booking or freeing seats
 * @param seatID: id of the seat to book or free
 */
function bookAjax(seatID) {
    var className = document.getElementById(seatID).className; // get class of button pressed
    var newStatus = "";

    // set new status based on previous
    if (className === "freeSeat" || className === "bookedSeat") {
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
    // Post parameters: seat ID and new status to set
    req.send("seatID=" + seatID + "&newStatus=" + newStatus);
}

/**
 * Callback function for ajax request
 * @param seatID:  id of the seat to book or free
 */
function updateSeatStatus(seatID) {
    if (req.readyState === 4 && (req.status === 0 || req.status === 200)) {

        //if no errors occured set new status as class (modify color)
        if (req.responseText !== "error")
            document.getElementById(seatID).className = req.responseText;

        // if seat was sold during the request, mark it as sold and display an alert
        if (document.getElementById(seatID).className === "soldSeat") {
            document.getElementById(seatID).disabled = true;
            alert("Seat: " + seatID + " is sold.");
        }

        // if seat is booked for current user we need to set hidden input value for purchase function
        if (req.responseText === "myBookedSeat")
            document.getElementById(seatID + "_HIDDEN").value = "BS";

        // if seat is now free (and was mine) we need to delete hidden value
        if (req.responseText === "freeSeat")
            document.getElementById(seatID + "_HIDDEN").value = "";
    }
}
