jQuery (function ()
{

    /*入力エリアでのEnterkeyでsubmitされるのを防ぐ*/
    $("input[type=text]").keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){//enter);
            event.preventDefault();
        }
    });
    $("input[type=tel]").keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){//enter);
            event.preventDefault();
        }
    });
    $("input[type=email]").keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){//enter);
            event.preventDefault();
        }
    });
    $("input[type=checkbox]").keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){//enter);
            event.preventDefault();
        }
    });
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
    //  設定画面のバイリンガルが使用するの場合、必須マークをつける
    var bilingual = $('input:hidden[name="bilingual"]').val();
    if (bilingual == '1') {
        $("#lblUserNameEn").addClass('required');
    }

    // 英語の場合、スタイル調整
    if (selLang == 1) {
        $("#author > label").css('width','100');
        $(".frmMasterInput >div.form-group > label").css('width','220');      
    }
          
    // ２秒後に登録成功のブロックを非表示 
    $(".alert-success").fadeOut( 2000 );

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