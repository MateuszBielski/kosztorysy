jQuery(document).ready(function() {
    var input_find_catalog = $('#input_find_catalog');
    console.log(input_find_catalog.val());
    
    input_find_catalog.on('input',function(){
        var tekst = input_find_catalog.val();
        $.ajax({
                // url: "/muo/ajax",
                url: "/catalog/indexAjax",
                type: "GET",
                data: {
                    str: tekst
                },
                success: function (msg) {
                    console.log('sukces'); 
                    $("#kontener").text(tekst);
                    $('#catalog_list').html(msg);
                     //var users_list = $("#users_list");
                     //users_list.replaceWith(response.find('#users_list'));
                }
                ,error: function (err) {
                    $("#kontener").text(err.Message);
                }
        });
    });
   
});