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
    $("#btnModalBulletinBoard").click(function() {
            
        $("#RegistDate").val(getToday('/'));
        $("#Title").val("");
        $("#Contents").val("");
        $("#LimitDate").val(getAddMonth('/',1));
        $("#BulletinBoardId").val("");
        $("#RegistUserId").val("");
        $("#DeleteFlag").val("");
        
        $("#btnBulletinboardDelete").css('display','none');
        $("#modal-bulletinboard").modal('show');
    });
    /*掲示板クリアボタン*/
    $("#btnBulletinBoardClear").click(function() {
        $("#Title").val("");
        $("#Contents").val("");
        $("#LimitDate").val(getAddMonth('/',1));       
    });
    /*掲示板　更新ポップアップ表示*/
    $(".editicon").click(function() {
        var userid = $(this).parent("article").children("input[name='RegistUserIdlist']").val();
        var time = $(this).parent("article").children("time").html();
        
        var title = $(this).parent("article").children("h6").text();
        var contents = $(this).parent("article").children("p").text();
        var limitdate = $(this).parent("article").children("input[name='LimitDatelist']").val();
        var id = $(this).parent("article").children("input[name='BulletinBoardIdlist']").val();
        $("#RegistDate").val(time);
        $("#Title").val(title);
        $("#Contents").val(contents);
        $("#LimitDate").val(limitdate);
        $("#LimitDate").prop("min",getToday('-'));
        $("#BulletinBoardId").val(id);
        $("#RegistUserId").val(userid);
        $("#DeleteFlag").val("");

        $("#btnBulletinboardDelete").css('display','inline-block');
        $("#modal-bulletinboard").modal('show');
    });

    /*選択したarticleに色を付ける*/
    $(".info article").click(function() {
        $(".info article").removeClass("table-fixed-selectRow");
        $(".info article").removeClass("table-fixed-nonselect");
        $(this).addClass("table-fixed-selectRow");
    });
    /*掲示板保存ボタンクリック時 */
    $("#btnBulletinboardSave").click(function() {
        var message = "";
        $("#divError").css('display','none');
        $("#divError li").remove();
        
        if ($("#Title").val()==""){
            message += '<li>' + requireTitle[selLang] + '</li>';
        }
        else{
            if($("#Title").val().length > 50){
                message += '<li>' + maxTitle[selLang] + '</li>';
            }
        }
        if ($("#Contents").val()==""){
            message += '<li>' + requireContents[selLang] + '</li>';
        }
        else{
            if($("#Contents").val().length > 500){
                message += '<li>' + maxContents[selLang] + '</li>';
            }
        }
        if ($("#LimitDate").val()==""){
            message += '<li>' + requireLimitDate[selLang] + '</li>';
        }

        if (message != ""){
            $("#divError").css('display','block');
            $("#divError").append(message);
            return false;
        }
        if (confirm(confirmSave[selLang])){
            var deferred = insertBulletinboard();
            deferred.done(function(){
                $.unblockUI();
                location.reload();
            });
        }
    });

    /*掲示板削除ボタンクリック時 */
    $("#btnBulletinboardDelete").click(function() {
        if (confirm(confirmDelete[selLang])){
            var deferred = deleteBulletinboard();
            deferred.done(function(){
                $.unblockUI();
                location.reload();
            });
        }
    });

    /*掲示板閉じるボタンクリック時 */
    $("#btnBulletinBoardClose").click(function() {
        $("#divError").css('display','none');
        $("#divError li").remove();
        $("#modal-bulletinboard").modal('hide');
    });

    function insertBulletinboard(){
        var ref = {
            'BulletinBoardId' : $("#BulletinBoardId").val(),
            'RegistDate' : $("#RegistDate").val(),
            'Title' : $("#Title").val(),
            'Contents' : $("#Contents").val(),
            'LimitDate' : $("#LimitDate").val()
        }
        processing();
        var deferred = new $.Deferred();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'home/bulletinBoardStore',
            type: 'GET',
            datatype: 'json',
            data : ref
        })
        // Ajaxリクエスト成功時の処理
        .done(function(data) {
            if (data['status'] !== 'OK') {
                alert(processingFailed[selLang] + data['status']);
            }
        })
        // Ajaxリクエスト失敗時の処理
        .fail(function(data) {
            alert(processingFailed[selLang] + data['status']);
        })
        .always(function(data) {
            deferred.resolve();           
        });
        
        return deferred;        
    }

    function deleteBulletinboard(){
        var ref = {
            'BulletinBoardId' : $("#BulletinBoardId").val()
        }
        processing();
        var deferred = new $.Deferred();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'home/bulletinBoardDestroy',
            type: 'GET',
            datatype: 'json',
            data : ref
        })
        // Ajaxリクエスト成功時の処理
        .done(function(data) {
            if (data['status'] !== 'OK') {
                alert(processingFailed[selLang] + data['status']);
            }
        })
        // Ajaxリクエスト失敗時の処理
        .fail(function(data) {
            alert(processingFailed[selLang] + data['status']);
        })
        .always(function(data) {
            deferred.resolve();           
        });
        
        return deferred;        
    }    


})


$(window).on("load", function(){
    loadingStart();
});