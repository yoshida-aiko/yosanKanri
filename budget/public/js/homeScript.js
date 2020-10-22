jQuery (function ()
{

    /*ウィンドウの高さを取得して、グリッドの高さを指定*/
    settingGridHeight();
    

    $(window).resize(function () {
        settingGridHeight();
    });
    /*高さ調整*/
    function settingGridHeight() {
        /*ウィンドウの高さを取得して、グリッドの高さを指定*/
        var h = window.innerHeight ? window.innerHeight : $(window).height();
        var w = window.innerWidth ? window.innerWidth : $(window).width();
        $(".leftside-fixed-280").children(".info").css('height', h - 194 + 'px');
        $(".table-progressFixed tbody").css('height', h - 242 + 'px');
        var tblwidth = parseInt($(".table-progressFixed").css('width').replace('px',''));
        tblwidth = tblwidth - 1100;
        $(".table-progressFixed thead th:nth-child(4)").css('width',tblwidth + 'px');
        $(".table-progressFixed tbody td:nth-child(4)").css('width',tblwidth + 'px');
    }

    $('#LimitDate').datepicker({
        minDate: new Date(),
    });

    /*掲示板新規作成ボタンクリック時*/
    $("#btnModalBulletinBoad").click(function() {
            
        $("#RegistDate").val(getToday('/'));
        $("#Title").val("");
        $("#Contents").val("");
        $("#LimitDate").val(getAddMonth('/',1));
        $("#BulletinBoadId").val("");
        $("#RegistUserId").val("");
        $("#DeleteFlag").val("");
        
        $("#submit_bulletinboad_delete").css('display','none');
        $("#modal-bulletinboad").modal('show');
    });
    /*掲示板クリアボタン*/
    $("#btnBulletinBoadClear").click(function() {
        $("#Title").val("");
        $("#Contents").val("");
        $("#LimitDate").val("");       
    });
    /*掲示板ダブルクリック　更新ポップアップ表示*/
    $(".bulletinArticle").dblclick(function() {
        var time = $(this).children("time").html();
        var userid = $(this).children("input[name='RegistUserIdlist']").val();
        var title = $(this).children("h6").text();
        var contents = $(this).children("p").text();
        var limitdate = $(this).children("input[name='LimitDatelist']").val();
        var id = $(this).children("input[name='BulletinBoadIdlist']").val();
        $("#RegistDate").val(time);
        $("#Title").val(title);
        $("#Contents").val(contents);
        $("#LimitDate").val(limitdate);
        $("#LimitDate").prop("min",getToday('-'));
        $("#BulletinBoadId").val(id);
        $("#RegistUserId").val(userid);
        $("#DeleteFlag").val("");

        $("#submit_bulletinboad_delete").css('display','inline-block');
        $("#modal-bulletinboad").modal('show');
    });

    /*選択したarticleに色を付ける*/
    $(".info article").click(function() {
        $(".info article").removeClass("table-fixed-selectRow");
        $(".info article").removeClass("table-fixed-nonselect");
        $(this).addClass("table-fixed-selectRow");
    });
    
})
$(window).on("load", function(){
    loadingStart();
});