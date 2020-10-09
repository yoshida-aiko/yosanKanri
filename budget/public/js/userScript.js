jQuery (function ()
{

    /*ウィンドウの高さを取得して、グリッドの高さを指定*/
    settingGridHeight();
    $(".table-masterFixed").css('display','none').fadeIn(0);

    $(window).resize(function () {
        settingGridHeight();
    });
    /*高さ調整*/
    function settingGridHeight() {
        /*ウィンドウの高さを取得して、グリッドの高さを指定*/
        var h = window.innerHeight ? window.innerHeight : $(window).height();
        var w = window.innerWidth ? window.innerWidth : $(window).width();
        $(".table-masterFixed tbody").css('height', h - 320 + 'px');
        var tblwidth = parseInt($(".table-masterFixed").css('width').replace('px',''));
        tblwidth = tblwidth - 120;
        /*$(".table-masterFixed thead th:nth-child(2)").css('width',tblwidth + 'px');
        $(".table-masterFixed tbody td:nth-child(2)").css('width',tblwidth + 'px');*/
    }
    
    /*ユーザーマスタ　クリアボタンクリック時*/
    $("#btn_user_clear").click(function() {
        $("#frmUserMaster > div.form-group > input[type=text]").val("");
        $("#frmUserMaster > div.form-group > input[type=tel]").val("");
        $("#frmUserMaster > div.form-group > input[type=email]").val("");
        $("#frmUserMaster > div.form-group > input[type=hidden]").val("");
        $("#frmUserMaster > div.form-group > textarea").val("");
        $("#frmUserMaster > div.form-group input[type=checkbox]").prop('checked',false);
        
        $("#resetLinkLabel").removeClass('resetLinkOn').removeClass('resetLinkOff');
        $("#resetLinkAnchor").removeClass('resetLinkOn').removeClass('resetLinkOff');
        $("#passwordLabel").removeClass('resetLinkOn').removeClass('resetLinkOff');
        $("#resetLinkInput").removeClass('resetLinkOn').removeClass('resetLinkOff');

        $("#resetLinkLabel").addClass('resetLinkOff');
        $("#resetLinkAnchor").addClass('resetLinkOff');
        $("#passwordLabel").addClass('resetLinkOn');
        $("#resetLinkInput").addClass('resetLinkOn');

        $(".alert-danger").css('display','none');
    });
})