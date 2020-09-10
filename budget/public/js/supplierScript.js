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
         $(".table-masterFixed tbody").css('height', h - 320 + 'px');
         var tblwidth = parseInt($(".table-masterFixed").css('width').replace('px',''));
         tblwidth = tblwidth - 120;
     }

     /*マスタ　クリアボタンクリック時*/
    $("#btn_supplier_clear").click(function() {
        $("#frmSupplierMaster > div.form-group > input[type=text]").val("");
        $("#frmSupplierMaster > div.form-group > input[type=tel]").val("");
        $("#frmSupplierMaster > div.form-group > input[type=email]").val("");
        $("#frmSupplierMaster > div.form-group > input[type=hidden]").val("");
    });

})