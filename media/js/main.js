// Close a system alert.
function closeAlert(alert) {
    alert.fadeOut(300, function () {
        alert.remove();
    });
}

jQuery(document).ready(function () {
    // Bind function closeAlert() to the click event of close buttons in system alerts.
    jQuery('.close-alert').click(function () {
        closeAlert(jQuery(this).parent());
    });

    // Call closeAlert() after 3 seconds since the document is ready.
    setTimeout(function () {
        jQuery('.close-alert').click();
    }, 3000);
});