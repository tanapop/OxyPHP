function closeAlert(alert) {
    alert.fadeOut(300, function () {
        alert.remove();
    });
}

jQuery(document).ready(function () {
    jQuery('.close-alert').click(function () {
        closeAlert(jQuery(this).parent());
    });

    setTimeout(function () {
        jQuery('.close-alert').click();
    }, 3000);
});