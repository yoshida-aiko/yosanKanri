jQuery (function ($)
{  
    /*$("#table-searchFixed-tr").draggable();

    $("#sectionOrderRequest").droppable({
        accept : ".table-searchFixed-tr",
        drop: function(event, ui) {
            console.log("drop" + ui);
        }
    });*/


    $(".numCartOrderRequestNumber").on('input', function() {
        var cartid = $(this).parent("div").children("input[name=CartId]").val();
        var ordernumber = $(this).val();
        
        var deferred = updateOrderRequestNumber(cartid,ordernumber);
        deferred.done(function(){
            $.unblockUI();
        }); 
    });

    var jdataReagent = $.parseJSON($("#hidFavoriteTreeReagent").val());

    jsTreeCreate(jdataReagent,'SearchPage','favoriteTreeReagent');

    var jdataArticle = $.parseJSON($("#hidFavoriteTreeArticle").val());

    jsTreeCreate(jdataArticle,'SearchPage','favoriteTreeArticle');
    

    function updateOrderRequestNumber(cartid,ordernumber){
        processing();
        var deferred = new $.Deferred();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'SearchPage/updateCartOrderRequestNum',
            type: 'GET',
            datatype: 'json',
            data : {'update_id' : cartid, 'order_number' : ordernumber}
        })
        // Ajaxリクエスト成功時の処理
        .done(function(data) {
            if (data['status'] !== 'OK') {
                alert('データ更新に失敗しました');
            }
        })
        // Ajaxリクエスト失敗時の処理
        .fail(function(data) {
            alert('データ更新に失敗しました');
        })
        .always(function(data) {
            deferred.resolve();           
        });

        return deferred;
    }

    //$("input[name=searchFormTab]").val([1]);
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
    /*一覧の行をダブルクリック*/
    $(".table-searchFixed-tr").dblclick(function() {
        $("#detailAmount").html($(this).children("td").eq(1).html());
        $("#detailProductName").html($(this).children("td").eq(2).html());
        $("#detailStandard").html($(this).children("td").eq(3).html());
        $("#detailCatalogCode").html($(this).children("td").eq(4).html());
        $("#detailMakerName").html($(this).children("td").eq(5).html());
        $("#detailUnitPrice").html($(this).children("td").eq(6).html());
        $("#detailSupplierName").html($(this).children("td").eq(8).html());

        $("#modal-detail").modal('show');
    });

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

    /*検索、発注依頼、お気に入りのタブ（試薬・物品）をを共通でセットする*/
    $("input[name=searchFormTab]").change(function() {
        $("input[name=tabCart]").val([$(this).val()]);
        $("input[name=tabFavorite]").val([$(this).val()]);
        $("input[name=tabSelectFolder]").val($(this).val());
    });
    $("input[name=tabCart]").change(function() {
        $("input[name=searchFormTab]").val([$(this).val()]);
        $("input[name=tabFavorite]").val([$(this).val()]);
        $("input[name=tabSelectFolder]").val($(this).val());
    });
    $("input[name=tabFavorite]").change(function() {
        $("input[name=tabCart]").val([$(this).val()]);
        $("input[name=searchFormTab]").val([$(this).val()]);
        $("input[name=tabSelectFolder]").val($(this).val());
    });
    /*選択したarticleに色を付ける*/
    $(".sectionOrderRequest article").click(function() {
        $(".sectionOrderRequest article").removeClass("table-fixed-selectRow");
        $(".sectionOrderRequest article").removeClass("table-fixed-nonselect");
        $(this).addClass("table-fixed-selectRow");
    });




    /*$("#chkShared").change(function() {
        console.log("chkShared:" + $("#chkShared").val());
        if ($("#chkShared:checked").val() === '共用') {
            $("#submit_chkSharedKey").val($("#chkShared:checked").val());
            $(".searchConditionForm").submit();
        }else{
            $("#submit_chkSharedKey").val('');
        }
    });
    $("#submit_chkSharedKey").change(function() {
        console.log("submit_chkSharedKey:" + $("#submit_chkSharedKey").val());
        $("#chkShared:checked").val($("#submit_chkSharedKey").val());
    });*/

    /*function checkOrderRequest(btn){*/
    $("input[name=btnCart]").click(function() {
        var id = $(this).parent('form').children('.hidUpdateId').val();
        var myform =  $(this).parent('form');
        var ret = checkOrderRequest(id);
        var deferred = ret.deferred;
        deferred.done(function(){
            $.unblockUI();
            if (ret.result) {
                myform.submit();
            }
        });  
    });

    function checkOrderRequest(id) {
        var ret = new Object();
        processing();
        var deferred = new $.Deferred();
        var result = true;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'SearchPage/checkOrderRequest',
            type: 'GET',
            datatype: 'json',
            data : {'update_id' : id}
        })
        // Ajaxリクエスト成功時の処理
        .done(function(data) {
            if (data['status'] === 'Duplicate') {
                if (!confirm("同じ商品が既に発注依頼されています。登録しますか？")){
                    result = false;
                }
            }
        })
        // Ajaxリクエスト失敗時の処理
        .fail(function(data) {
            alert('データチェックに失敗しました' + data['status']);
            result = false;
        })
        .always(function(data) {
            deferred.resolve();
        });
        
        ret.result = result;
        ret.deferred = deferred;

        return ret;

    }

})


