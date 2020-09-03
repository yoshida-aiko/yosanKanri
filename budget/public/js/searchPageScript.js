jQuery (function ()
{  
    /*items = document.getElementById('table-searchFixed').getElementsByClassName('table-searchFixed');

    Array.prototype.forEach.call(items, function (item) {
        $(item).on('dragstart', onDragStart);
        $(item).on('dragend', onDragEnd);
    });

    // dropzoneのリスナーを設定
    var $dropzone = $('#sectionOrderRequest')
        .on('dragover', onDragOver)
        .on('dragenter', onDragEnter)
        .on('dragleave', onDragLeave)
        .on('drop', onDrop);    
    */




    $("input[name=tabCart]").val([$("input[name=searchFormTab]:checked").val()]);
    $("input[name=tabFavorite]").val([$("input[name=searchFormTab]:checked").val()]);

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
        $(".leftside-fixed-240 .tabContent").css('height', h - 180 + 'px');
        $(".leftside-fixed-280 .tabContent .sectionOrderRequest").css('height', (h - 180) / 2 - 50 + 'px');
        if ($(".table-searchFixed").length > 0) {
            $(".table-searchFixed tbody").css('height', h - 230 + 'px');
            var tblwidth = parseInt($(".table-searchFixed").css('width').replace('px',''));
            tblwidth = tblwidth - 755;
            $(".table-searchFixed thead th:nth-child(3)").css('width',tblwidth + 'px');
            $(".table-searchFixed tbody td:nth-child(3)").css('width',tblwidth + 'px');
        }
    }
    /*絞込検索　表示・非表示ボタン*/
    $("#toggle-button-searchpage").click(function() {
        $(".leftside-fixed-240").animate( { width: 'toggle', opacity: "toggle"},
            { complete: function() {settingGridHeight();},
        }, 1000 );
        
    });

    /*試薬検索条件エリア　クリアボタン*/
    $("#btnClearReagent").click(function() {
        $('input[name="searchReagentNameR"]').val("");
        $('input[name="searchStandardR"]').val("");
        $('input[name="searchCasNoR"]').val("");
        $('input[name="searchCatalogCodeR"]').val("");
        $('input[name="makerCheckboxR[]"]').prop('checked',false);
    });
    /*試薬検索条件エリア　メーカー全て解除/選択*/
    $("#makerCheckboxRAll").click(function () {
        if($(this).is(':checked')) {
            $('input[name="makerCheckboxR[]"]').prop('checked',true);
        } else {
            $('input[name="makerCheckboxR[]"]').prop('checked',false);
        }
    });
    /*物品検索条件エリア　クリアボタン*/
    $("#btnClearArticle").click(function() {
        $('input[name="searchReagentNameA"]').val("");
        $('input[name="searchStandardA"]').val("");
        $('input[name="searchCasNoR"]').val("");
        $('input[name="makerCheckboxA[]"]').prop('checked',false);
    });
    /*試薬検索条件エリア　メーカー全て解除/選択*/
    $("#makerCheckboxAAll").click(function () {
        if($(this).is(':checked')) {
            $('input[name="makerCheckboxA[]"]').prop('checked',true);
        } else {
            $('input[name="makerCheckboxA[]"]').prop('checked',false);
        }
    });

    /*試薬　検索ボタンクリック*/
    $("#submitReagent").click(function() {
        $("#submit_key").val("1");
    });
    /*物品　検索ボタンクリック */
    $("#submitArticle").click(function() {
        $("#submit_key").val("2");
    });

    /*カート追加ボタンクリック*/
    $("input[name=btnCart]").click(function() {
        $("input[name=cartFavorite_submit_key]").val('btnCart');
        $("#submit_key").val("btnCart");
    });
    /*お気に入り追加ボタンクリック*/
    $("input[name=btnFavorite]").click(function() {
        $("input[name=cartFavorite_submit_key]").val('btnFavorite');
        $("#submit_key").val("btnFavorite");
    });

    /*フォルダ作成ボタン*/
    $("#btnFolderAdd").click(function() {
        $("#modal-folderadd").modal('show');
    });
    /*フォルダクリアボタン*/
    $("#btnFolderClear").click(function() {
        $("#FolderName").val("");
    });

    /*検索、発注依頼、お気に入りのタブ（試薬・物品）をを共通でセットする*/
    $("input[name=searchFormTab]").change(function() {
        $("input[name=tabCart]").val([$(this).val()]);
        $("input[name=tabFavorite]").val([$(this).val()]);
    });
    $("input[name=tabCart]").change(function() {
        $("input[name=searchFormTab]").val([$(this).val()]);
        $("input[name=tabFavorite]").val([$(this).val()]);
    });
    $("input[name=tabFavorite]").change(function() {
        $("input[name=tabCart]").val([$(this).val()]);
        $("input[name=searchFormTab]").val([$(this).val()]);
    });
    /*選択したarticleに色を付ける*/
    $(".sectionOrderRequest article").click(function() {
        $(".sectionOrderRequest article").removeClass("table-fixed-selectRow");
        $(".sectionOrderRequest article").removeClass("table-fixed-nonselect");
        $(this).addClass("table-fixed-selectRow");
    });

})