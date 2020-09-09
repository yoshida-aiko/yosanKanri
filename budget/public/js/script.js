jQuery (function ()
{
    //画像やカレンダーからドロップできてしまっていたので追加 2017/12/27
    $(document).on('drop dragover', function (e) {
        e.stopPropagation();
        e.preventDefault();
    });

    $(document).keydown(function (event) {
        // クリックされたキーのコード
        var keyCode = event.keyCode;
        // Ctrlキーがクリックされたか (true or false)
        var ctrlClick = event.ctrlKey;
        // Altキーがクリックされたか (true or false)
        var altClick = event.altKey;
        // キーイベントが発生した対象のオブジェクト
        var obj = event.target;

        // バックスペースキーを制御する
        if (keyCode == 8) {
            // テキストボックス／テキストエリアを制御する
            if ((obj.tagName.toUpperCase() == "INPUT" && obj.type.toUpperCase() == "TEXT")
                || obj.tagName.toUpperCase() == "TEXTAREA") {
                // 入力できる場合は制御しない
                if (!obj.readOnly && !obj.disabled) {
                    return true;
                }
            }
            return false;
        }

        // Alt + ←を制御する
        if (altClick && (keyCode == 37 || keyCode == 39)) {
            return false;
        }

    });
    
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