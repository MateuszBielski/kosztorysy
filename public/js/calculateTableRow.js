jQuery(document).ready(function() {
    var input_survey = $('#input_survey');
    var tr_id = $('#table_row_id').val();
    console.log(tr_id);
    input_survey.on('input',function(){
        var survey = input_survey.val();
        $.ajax({
                url: "/cost/item/calculateAjax",
                type: "GET",
                data: {
                    id: tr_id,
                    survey: survey
                },
                success: function (msg) {
                    // console.log('sukces'); 
                    $('#rightcol').html(msg);
                     //var users_list = $("#users_list");
                     //users_list.replaceWith(response.find('#users_list'));
                }
                ,error: function (err) {
                    $("#kontener").text(err.Message);
                }
        });
    });
   
});