jQuery (function ($)
{  
    var itemClassSelected = 1;
    if (sessionStorage.getItem('searchPageItemClasSelected') !== null){
        itemClassSelected = sessionStorage.getItem('searchPageItemClasSelected');
    }
    $("input[name=searchFormTab]").val([itemClassSelected]);
    $("input[name=tabCart]").val([itemClassSelected]);
    $("input[name=tabFavorite]").val([itemClassSelected]);

    if (sessionStorage.getItem('searchPageNarrowDownSearchIsVisible') !== null){
        if (sessionStorage.getItem('searchPageNarrowDownSearchIsVisible')==='true'){
            $(".leftside-fixed-240").show();
        }
        else {
            $(".leftside-fixed-240").hide();
        }
    }
    else {
        $(".leftside-fixed-240").hide();
    }
    if(sessionStorage.getItem('IsFavoriteSharedChecked') !== null){
        var isShared = sessionStorage.getItem('IsFavoriteSharedChecked')=='true' ? true : false;
        $("#chkShared").prop('checked',isShared);
    }

    $("input[name=btnCartDelete]").click(function() {
        var cartid = $(this).parent().parent("article").find("input[name=CartId]").val();
        cartListDelete(cartid);
    });

    /*カートボタンhover時 */
    $("input[name=btnCart]").hover(
        function() {
            $(".h6-title-cart").addClass('h6-title-cart-pink');
        },
        function() {
            $(".h6-title-cart").removeClass('h6-title-cart-pink');
        }
    );
    /*お気に入りボタンhover時 */
    $("input[name=btnFavorite]").hover(
        function() {
            $(".h6-title-favorite").addClass('h6-title-cart-yellow');
        },
        function() {
            $(".h6-title-favorite").removeClass('h6-title-cart-yellow');
        }
    );

    $('.sectionOrderRequest > article > div.orderRequestContent >h6').tooltip({
        content: function() {
            return $(this).attr('title');
        }
    });


    $(".numCartOrderRequestNumber").on('input', function() {
        var cartid = $(this).parent("div").children("input[name=CartId]").val();
        var ordernumber = $(this).val();
        
        var deferred = updateOrderRequestNumber(cartid,ordernumber);
        deferred.done(function(){
            $.unblockUI();
        }); 
    });


    
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
                alert(processingFailed[selLang]);
            }
        })
        // Ajaxリクエスト失敗時の処理
        .fail(function(data) {
            alert(processingFailed[selLang]);
        })
        .always(function(data) {
            deferred.resolve();           
        });

        return deferred;
    }

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
            { complete: function() {
                settingGridHeight();
                sessionStorage.setItem('searchPageNarrowDownSearchIsVisible', $(".leftside-fixed-240").is(':visible'));
            },
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

    /*カート追加ボタンクリック*/
    /*$("input[name=btnCart]").click(function() {
        $("input[name=cartFavorite_submit_key]").val('btnCart');
    });*/

    /*お気に入り追加ボタンクリック*/
    /*$("input[name=btnFavorite]").click(function() {
        $("input[name=cartFavorite_submit_key]").val('btnFavorite');
    });*/

    /*検索、発注依頼、お気に入りのタブ（試薬・物品）をを共通でセットする*/
    $("input[name=searchFormTab]").change(function() {
        sessionStorage.setItem('searchPageItemClasSelected',$(this).val());
        $("input[name=tabCart]").val([$(this).val()]);
        $("input[name=tabFavorite]").val([$(this).val()]);
        $("input[name=tabSelectFolder]").val($(this).val());
    });
    $("input[name=tabCart]").change(function() {
        sessionStorage.setItem('searchPageItemClasSelected',$(this).val());
        $("input[name=searchFormTab]").val([$(this).val()]);
        $("input[name=tabFavorite]").val([$(this).val()]);
        $("input[name=tabSelectFolder]").val($(this).val());
    });
    $("input[name=tabFavorite]").change(function() {
        sessionStorage.setItem('searchPageItemClasSelected',$(this).val());
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

    $("input[name=btnCart]").click(function() {
        var id = $(this).parent('td').children('.hidUpdateId').val();
        var ret = checkOrderRequest(id);
        var deferred = ret.deferred;
        deferred.done(function(){
            $.unblockUI();
            if (ret.result) {
                cartAdd(id);
            }
        });  
    });

    /* お気に入りへ移動*/
    $("input[name=btnFavorite]").click(function() {
        var id = $(this).parent('td').children('.hidUpdateId').val();
        var ret = favoriteAddAjax(id);
        var deferred =  ret.deferred;
        deferred.done(function(){
            console.log(ret.result);
            if (ret.result){
                location.reload();
            }
            else {
                location.href = './Error/systemError';
            }
        });
    });

    function favoriteAddAjax(id) {
        
        processing();
        var isShared = false;
        if (sessionStorage.getItem('IsFavoriteSharedChecked')!==null){
            isShared = sessionStorage.getItem('IsFavoriteSharedChecked')=='true'? true : false;
        }
        var ret = new Object();
        ret.result = true;
        var deferred = new $.Deferred();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'SearchPage/favoriteAddProcess',
            type: 'GET',
            datatype: 'json',
            data : {'update_id' : id, 'isFavoriteSharedChecked' : isShared}
        })
        // Ajaxリクエスト成功時の処理
        .done(function(data) {
            if(data['status'] == 'NG'){
                ret.result = false;
                // alert(processingFailed[selLang]);
            }
        })
        // Ajaxリクエスト失敗時の処理
        .fail(function(data) {
            alert(processingFailed[selLang] + data['status']);
            result = false;
        })
        .always(function(data) {
            deferred.resolve();
        });
    
        ret.deferred = deferred.promise();
        return ret;
    }

    function checkOrderRequest(id) {
        var ret = new Object();
        processing();
        var deferred = new $.Deferred();
        ret.result = true;
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
            if (data['status'] == 'Duplicate') {
                if (confirm(confirmReorder[selLang])){
                    ret.result = true;                    
                }
            }
            else if(data['status'] == 'NG'){
                alert(processingFailed[selLang]);
            }
        })
        // Ajaxリクエスト失敗時の処理
        .fail(function(data) {
            alert(processingFailed[selLang] + data['status']);
            result = false;
        })
        .always(function(data) {
            deferred.resolve();
        });
                
        ret.deferred = deferred;
        return ret;

    }

    
    function cartAdd(id){
        var ret = cartAddAjax(id);
        var deferred = ret.deferred;
        deferred.done(function(){
            // $.unblockUI();
            console.log(ret.result);
                if (ret.result){
                    location.reload();
                }
                else {
                    location.href = './Error/systemError';
                }
        });  
    }

    function cartAddAjax(id) {
        var ret = new Object();
        processing();
        var deferred = new $.Deferred();
        ret.result = true;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'SearchPage/cartAddProcess',
            type: 'GET',
            datatype: 'json',
            data : {'update_id' : id}
        })
        // Ajaxリクエスト成功時の処理
        .done(function(data) {
            if(data['status'] == 'NG'){
                // alert(processingFailed[selLang]);
                ret.result = false;
            }
        })
        // Ajaxリクエスト失敗時の処理
        .fail(function(data) {
            alert(processingFailed[selLang] + data['status']);
            ret.result = false;
        })
        .always(function(data) {
            deferred.resolve();
        });
        
        ret.deferred = deferred;

        return ret;

    }
    
    $("#favoriteTreeReagent").favoriteTreeCreate('SearchPage','favoriteTree');
})
$(window).on("load", function(){
    loadingStart();
});