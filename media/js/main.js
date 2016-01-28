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
    
    jQuery(".dataTable").DataTable();
    
    jQuery(".submenu>a").click(function(e){
        e.preventDefault();
    });
    jQuery(".submenu").hover(function(){
        jQuery(this).find("ul").show('fast');
    },function(){
        jQuery(this).find("ul").hide('fast');
    });
});