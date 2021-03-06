jQuery (function ()
{

    /*入力エリアでのEnterkeyでsubmitされるのを防ぐ*/
    $("input[type=text]").keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){//enter);
            event.preventDefault();
        }
    });
    
    document.body.style.overflow = "hidden";
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
    }

    //  設定画面のバイリンガルが使用するの場合、必須マークをつける
    var bilingual = $('input:hidden[name="bilingual"]').val();
    if (bilingual == '1') {
      $("#lblBudgetNameEn").addClass('required');
    }

    // 執行期間　datepicker
    $('#useStartDate').datepicker({
        onSelect: function(dateText) {         
            var id = $('#id').val();
            var today = new Date(); 
            var start =   $(this).datepicker( 'getDate' );
            var preStart = $('#hidStatDt').val();
            var preStartDate = new Date(preStart);
            if (id!="" && preStart < dateText) {
                alert(beforeOriginalDate[selLang].replace('{0}',preStart));              
                $(this).datepicker( 'setDate' ,preStart);
                return;
            }
            var end = $('#useEndDate').val();
            if (end!="" && dateText > end) {
                //終了日より開始日が大きい場合
                alert(rangOfTheDate[selLang]);               
                $(this).datepicker( 'setDate' ,preStart);
                return;
            }
            var date = today;
            if (start > today) {
                date = start;
             } 
            $('#useEndDate').datepicker("option", "minDate", date);
        }  
    });

    $('#useEndDate').datepicker({
        minDate:new Date()
    });
    
    // 年度が変更された場合　hidden項目に設定
    $('[name=’fiscalYear’]').change(function() {
		var year = $(this).val();
        $('input:hidden[name="year"]').val("year");
    });

    // ２秒後に登録成功のブロックを非表示 
    $(".alert-success").fadeOut( 2000 );
     
    /*マスタ　クリアボタンクリック時*/
    $("#btn_Budget_clear").click(function() {
        $("#frmBudgetMaster > div.form-group > input[type=text]").val("");
        $("#frmBudgetMaster > div.form-group > input[type=number]").val("");
        $("#frmBudgetMaster > div.form-group > input[type=hidden]").val("");
        $(".alert-danger").css('display','none');
    });
})