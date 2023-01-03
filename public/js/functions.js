
$(document).ready(function () {

    function changeState(target_id) {
        var x = document.getElementById(target_id);
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }

});