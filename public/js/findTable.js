jQuery(document).ready(function() {
    var input_find_table = $('#input_find_table');
    // console.log(input_find_table.val());
    // console.log('początek');
    input_find_table.on('input',function(){
        var tekst = input_find_table.val();
        var kosztorys_id = $('#input_find_table').attr('kosztorys_id');
        var adres = "/cl/table/indexAjax";
        if(typeof kosztorys_id !== 'undefined')
        adres += '?kosztorys_id='+kosztorys_id;
        $.ajax({
                url: adres,
                type: "GET",
                data: {
                    str: tekst
                },
                success: function (msg) {
                    // console.log('sukces'); 
                    $("#kontener").text(tekst);
                    $('#table_list').html(msg);
                     //var users_list = $("#users_list");
                     //users_list.replaceWith(response.find('#users_list'));
                }
                ,error: function (err) {
                    $("#kontener").text(err.Message);
                }
        });
    });
   
});