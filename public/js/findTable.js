jQuery(document).ready(function() {
    var input_find_table = $('#input_find_table');
    // console.log(input_find_table.val());
    // console.log('początek');
    input_find_table.on('input',function(){
        var tekst = input_find_table.val();
        $.ajax({
                url: "/cl/table/indexAjax",
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