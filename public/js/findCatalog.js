jQuery(document).ready(function() {
    var input_find_catalog = $('#input_find_catalog');
    console.log(input_find_catalog.val());
    
    input_find_catalog.on('input',function(){
        var tekst = input_find_catalog.val();
        var kosztorys_id = $('#input_find_catalog').attr('kosztorys_id');
        var adres = "/catalog/indexAjax";
        if(typeof kosztorys_id !== 'undefined')
        adres += '?kosztorys_id='+kosztorys_id;
        console.log(adres);
        $.ajax({
                // url: "/muo/ajax",
                url: adres,
                type: "GET",
                data: {
                    str: tekst
                },
                success: function (msg) {
                     
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