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
        $(".leftside-fixed-240 .tabContent .sectionOrderRequest").css('height', h - 200 + 'px');
        if ($(".table-orderRequestFixed").length > 0) {
            $(".table-orderRequestFixed tbody").css('height', h - 235 + 'px');
            var tblwidth = parseInt($(".table-orderRequestFixed").css('width').replace('px',''));
            tblwidth = tblwidth - 1045;
            $(".table-orderRequestFixed thead th:nth-child(4)").css('width',tblwidth + 'px');
            $(".table-orderRequestFixed tbody td:nth-child(4)").css('width',tblwidth + 'px');
        }
    }

    $("input[name=tabFavorite]").val([1]);

    /*絞込検索　表示・非表示ボタン*/
    $("#toggle-button-favorite").click(function() {
        $(".leftside-fixed-240").animate( { width: 'toggle', opacity: "toggle"},
            { complete: function() {settingGridHeight();},
        }, 1000 );
        
    });

    /*一覧の行をダブルクリック */
    $(".table-orderRequestFixed-tr").dblclick(function() {
        $("#detailAmount").html($(this).children("td").eq(4).html());
        $("#detailProductName").html($(this).children("td").eq(3).html());
        $("#detailStandard").html($(this).children("td").eq(11).html());
        $("#detailCatalogCode").html($(this).children("td").eq(5).html());
        $("#detailMakerName").html($(this).children("td").eq(6).html());
        $("#detailUnitPrice").html($(this).children("td").eq(7).html());
        $("#detailSupplierName").html($(this).children("td").eq(12).html());

        $("#modal-detail").modal('show');
    });
    /*行のダブルクリックイベントをキャンセル*/
    $(".tdOrderInputNumber input").dblclick(function(event) {
        event.stopPropagation();
    });
    $(".tdOrderRemark input").dblclick(function(event) {
        event.stopPropagation();
    });
    /*新規商品入力*/
    $("#btnNewProduct").click(function() {
        $("#modal-newProduct").modal('show');
    });
    /*全て選択or解除チェック*/
    $("input[name=chkTargetAll").click(function() {
        if($(this).is(':checked')) {
            $('input[name="chkTarget[]"]').prop('checked',true);
        } else {
            $('input[name="chkTarget[]"]').prop('checked',false);
        }
    });

    /*発注依頼*/
    $("#btnOrderRequest").click(function() {
        var arrayChecked = $("#table-orderRequestFixed tr").children('td').children('input[type=checkbox]:checked');
        var arrayCartIds = [];
        if (arrayChecked.length <= 0){
            alert('登録対象を選択してください');
        }
        else {
            if (confirm('登録しますか？' + '【対象：' + arrayChecked.length + '件】')) {
                arrayChecked.each(function (index,element){
                    arrayCartIds.push($(element).parent().parent().find('input[name=cartId]').val());
                });
                var userid = $("#selOrderRequestUser option:selected").val();
                var today = getToday('/');
                var deferred = orderRequest(arrayCartIds,userid,today);
                deferred.done(function(){
                    $.unblockUI();
                    $('input[name="chkTarget[]"]').prop('checked',false);
                    location.reload();
                });
            }
        }
        

    });

    $('.inpOrderInputNumber').on({
        "keypress":function(e) {
            if (e.which == 13) {
                if ($(this).val() !== "" && isFinite($(this).val())) {
                    var id=$(this).parent().parent().find('input[name=cartId]').val();
                    $(this).parent().children('.spnOrderInputNumber').html(Number($(this).val()).toLocaleString());
                    $(this).css('display','none');
                    $(this).parent().children('.spnOrderInputNumber').css('display','inline-block');
                    var price = Number($(this).parent().parent().children('.tdOrderInputNumber').children('.inpOrderUnitPrice').val());
                    var ordernum = $(this).parent().parent().children('.tdOrderInputNumber').children('.inpOrderRequestNumber').val();
                    var remark = $(this).parent().parent().children('.tdOrderRemark').children('.pOrderRemark').html();
                    var deferred = updateOrder(id,price,ordernum,remark);
                    deferred.done(function(){
                        $.unblockUI();
                    });  
                    var totalfee = (price * ordernum).toLocaleString();
                    $(this).parent().nextAll('.tdOrderTotalFee').html(totalfee);
                }
            }
        },
        "blur":function() {
            if ($(this).val() !== "" && isFinite($(this).val())) {
                var id=$(this).parent().parent().find('input[name=cartId]').val();
                $(this).parent().children('.spnOrderInputNumber').html(Number($(this).val()).toLocaleString());
                $(this).css('display','none');
                $(this).parent().children('.spnOrderInputNumber').css('display','inline-block');
                var price = Number($(this).parent().parent().children('.tdOrderInputNumber').children('.inpOrderUnitPrice').val());
                var ordernum = $(this).parent().parent().children('.tdOrderInputNumber').children('.inpOrderRequestNumber').val();
                var remark = $(this).parent().parent().children('.tdOrderRemark').children('.pOrderRemark').html();
                var deferred = updateOrder(id,price,ordernum,remark);
                deferred.done(function(){
                    $.unblockUI();
                });  
                var totalfee = (price * ordernum).toLocaleString();
                $(this).parent().nextAll('.tdOrderTotalFee').html(totalfee);
            }
        }
    });
    $(".tdOrderInputNumber").click(function () {
        if ($(this).children('.spnOrderInputNumber').css('display')!='none') {
            $('.inpOrderInputNumber').css('display','none');
            $('.spnOrderInputNumber').css('display','inline-block');
            $(this).children('.spnOrderInputNumber').css('display','none');
            $(this).children('.inpOrderInputNumber').css('display','inline-block');
            $(this).children('.inpOrderInputNumber').focus();
        }
    });
    $(".table-orderRequestFixed-tr > td:not(.tdOrderInputNumber,.tdOrderRemark)").click(function() {
        $('.inpOrderInputNumber').css('display','none');
        $('.spnOrderInputNumber').css('display','inline-block');
        $('.inpOrderRemark').css('display','none');
        $('.pOrderRemark').css('display','inline-block');
    });


    $('.inpOrderRemark').on({
        "keypress":function(e) {
            if (e.which == 13) {
                var id=$(this).parent().parent().find('input[name=cartId]').val();
                $(this).parent().children('.pOrderRemark').html($(this).val());
                $(this).parent().children('.pOrderRemark').prop('title',$(this).val());
                $(this).css('display','none');
                $(this).parent().children('.pOrderRemark').css('display','inline-block');
                var remark = $(this).parent().children('.pOrderRemark').html();
                var deferred = updateOrder(id,'-1','-1',remark);
                deferred.done(function(){
                    $.unblockUI();
                });  
            }
        },
        "blur":function() {
            var id=$(this).parent().parent().find('input[name=cartId]').val();
            $(this).parent().children('.pOrderRemark').html($(this).val());
            $(this).parent().children('.pOrderRemark').prop('title',$(this).val());
            $(this).css('display','none');
            $(this).parent().children('.pOrderRemark').css('display','inline-block');
            var remark = $(this).parent().children('.pOrderRemark').html();
            var deferred = updateOrder(id,'-1','-1',remark);
            deferred.done(function(){
                $.unblockUI();
            });
        }
    });
    $(".tdOrderRemark").click(function () {
        if ($(this).children('.pOrderRemark').css('display')!='none') {
            $('.inpOrderRemark').css('display','none');
            $('.pOrderRemark').css('display','inline-block');
            $(this).children('.pOrderRemark').css('display','none');
            $(this).children('.inpOrderRemark').css('display','inline-block');
            $(this).children('.inpOrderRemark').focus();
        }
    });    

    var jdataReagent = $.parseJSON($("#hidFavoriteTreeReagent").val());
    var isToCartDisabled = false;
    jsTreeCreate(jdataReagent,'OrderRequest','favoriteTreeReagent',isToCartDisabled);

    var jdataArticle = $.parseJSON($("#hidFavoriteTreeArticle").val());
    jsTreeCreate(jdataArticle,'OrderRequest','favoriteTreeArticle',isToCartDisabled);

    function updateOrder(id,price,ordernum,remark) {
        processing();
        var deferred = new $.Deferred();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'OrderRequest/updateListPrice',
            type: 'GET',
            datatype: 'json',
            data : {'id' : id, 'price' : price, 'ordernum' : ordernum, 'remark' : remark}
        })
        // Ajaxリクエスト成功時の処理
        .done(function(data) {
            if (data['status'] !== 'OK') {
                alert('データ更新に失敗しました' + data['status']);
            }
        })
        // Ajaxリクエスト失敗時の処理
        .fail(function(data) {
            alert('データ更新に失敗しました' + data.message);
        })
        .always(function(data) {
            deferred.resolve();           
        });
        
        return deferred;
    }

    function orderRequest(arrayCartIds,userid,today) {
        processing();
        var deferred = new $.Deferred();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'OrderRequest/orderRequest',
            type: 'GET',
            datatype: 'json',
            data : {'arrayCartIds' : arrayCartIds, 'orderuserid' : userid, 'today' : today}
        })
        // Ajaxリクエスト成功時の処理
        .done(function(data) {
            if (data['status'] !== 'OK') {
                alert('データ更新に失敗しました' + data['status']);
            }
        })
        // Ajaxリクエスト失敗時の処理
        .fail(function(data) {
            alert('データ更新に失敗しました' + data['status']);
        })
        .always(function(data) {
            deferred.resolve();           
        });
        
        return deferred;
    }

})