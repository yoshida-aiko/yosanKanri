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
    }

    // 執行期間　datepicker
    $('#useStartDate').datepicker();
    $('#useEndDate').datepicker();

    // 年度が変更された場合　hidden項目に設定
    $('[name=’fiscalYear’]').change(function() {
		var year = $(this).val();
        $('input:hidden[name="year"]').val("year");
	});

    /*マスタ　クリアボタンクリック時*/
   $("#btn_Budget_clear").click(function() {
       $("#frmBudgetMaster > div.form-group > input[type=text]").val("");
       $("#frmBudgetMaster > div.form-group > input[type=hidden]").val("");
       $("#frmBudgetMaster > div.form-group > select > option").prop('selected',false);
   });
})