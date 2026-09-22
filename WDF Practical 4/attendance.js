let attendance = 85;

document.getElementById("attendanceButton").onclick = function() {

    if (attendance >= 75) {

        document.getElementById("attendanceMessage").innerHTML =
            "Your attendance is " + attendance + "%. You are eligible.";

    } else {

        document.getElementById("attendanceMessage").innerHTML =
            "Your attendance is " + attendance + "%. Your attendance is low.";

    }

};