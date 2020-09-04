jQuery (function ()
{

    $(".table-fixed tr").click(function() {
        $(".table-fixed tr").removeClass("table-fixed-selectRow");
        $(".table-fixed tr").removeClass("table-fixed-nonselect");
        $(this).addClass("table-fixed-selectRow");
    });
})

function getToday(splitchar) {
    var today = new Date();
    return today.getFullYear() + splitchar + 
        ( "0"+( today.getMonth()+1 ) ).slice(-2) + splitchar +
        ( "0"+today.getDate() ).slice(-2);
}