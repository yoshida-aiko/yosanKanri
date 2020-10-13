jQuery (function ()
{
    processing();

    /*ウィンドウの高さを取得して、グリッドの高さを指定*/
    settingGridHeight();
 
    $(window).resize(function () {
        settingGridHeight();
    });
    /*高さ調整*/
    function settingGridHeight() {
        /*ウィンドウの高さを取得して、グリッドの高さを指定*/
        var h = window.innerHeight ? window.innerHeight : $(window).height();

        if ($(".table-budgetFixed").length > 0) {
            $(".table-budgetFixed tbody").css('height', (h - 320)/3*2 + 'px');
            $(".table-budgetDetailFixed tbody").css('height', (h - 320)/3 + 'px');
            var tblwidth = parseInt($(".table-budgetFixed").css('width').replace('px',''));
            tblwidth_1 = tblwidth - 740;
            $(".table-budgetFixed thead th:nth-child(2)").css('width',tblwidth_1 + 'px');
            $(".table-budgetFixed tbody td:nth-child(2)").css('width',tblwidth_1 + 'px');
            tblwidth_2 = tblwidth - 500;
            $(".table-budgetDetailFixed thead th:nth-child(2)").css('width',tblwidth_2 + 'px');
            $(".table-budgetDetailFixed tbody td:nth-child(2)").css('width',tblwidth_2 + 'px');
        }else{
            if ($(".divNoData").length){
                $(".divNoData").css('height', (h - 190) / 2  + 'px');
            }
        }
    }

    $('.inpExecDate').datepicker();


})
$(window).on("load", function(){
    setTimeout('init()',1000);
});

function init() {
    $.unblockUI();
}

