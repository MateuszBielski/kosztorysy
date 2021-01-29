jQuery(document).ready(function() {
    var input_obmiar = $('#pozycja_kosztorysowa_obmiar');
    console.log(input_obmiar.val());
    
    input_obmiar.on('input',function(){
        var obmiar = input_obmiar.val();
        if( !obmiar )obmiar = 0.0;
        var kosztorys_id = $('#div_poz_oznaczenie').attr('kosztorys_id');
        var price_list_id = $('#div_poz_oznaczenie').attr('price_list_id');
        var table_row_id = $('#div_poz_oznaczenie').attr('table_row_id');
        var adres = "/pozycja/kosztorysowa/przeliczAjax";
        adres += '?obmiar='+obmiar;
        if(typeof kosztorys_id !== 'undefined')
        adres += '&kosztorys_id='+kosztorys_id;
        if(typeof price_list_id !== 'undefined')
        adres += '&price_list_id='+price_list_id;
        if(typeof table_row_id !== 'undefined')
        adres += '&table_row_id='+table_row_id;
        
        console.log(adres);
        
        $.ajax({
                // url: "/muo/ajax",
                url: adres,
                type: "GET",
                // data: {
                //     str: tekst
                // },
                success: function (msg) {
                     
                    $('#table_naklady').html(msg);
                     //var users_list = $("#users_list");
                     //users_list.replaceWith(response.find('#users_list'));
                }
                ,error: function (err) {
                    $("#table_naklady").text(err.Message);
                }
        });
    });
   
});
{/* <input type="text" id="pozycja_kosztorysowa_obmiar" name="pozycja_kosztorysowa[obmiar]" value="1"> */}