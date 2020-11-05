jQuery (function ()
{
    if (sessionStorage.getItem('orderFavoriteIsVisible') !== null){
        if (sessionStorage.getItem('orderFavoriteIsVisible')=='true'){
            $(".leftside-fixed-240").show();
        }
        else {
            $(".leftside-fixed-240").hide();
        }
    }
    else {
        $(".leftside-fixed-240").hide();
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
            { complete: function() {
                settingGridHeight();
                sessionStorage.setItem('orderFavoriteIsVisible', $(".leftside-fixed-240").is(':visible'));
            },
        }, 1000 );
        
    });
    /*新商品入力　保存ボタン*/
    $("#submit_newProduct_save").click(function() {
        
        var message = "";
        $("#divError").css('display','none');
        $("#divError li").remove();
        
        if (!$("input[name=newItemClass]:checked").val()){
            message += '<li>' + requireItemClass[selLang] + '</li>';
        }
        if ($("#newProductName").val()==""){
            message += '<li>' + requireItemName[selLang] + '</li>';
        }
        else{
            if($("#newProductName").val().length > 50){
                message += '<li>' + maxlengthItemName[selLang] + '</li>';
            }
        }
        if ($("#newSupplier option:selected").val()==""){
            message += '<li>' + requirePrioritySupplier[selLang] + '</li>';
        }
        if ($("#newUnitPrice").val()==""){
            message += '<li>' + requireUnitPrice[selLang] + '</li>';
        }
        else{
            var floatprice = parseFloat($("#newUnitPrice").val());
            if (isNaN(floatprice)){
                message += '<li>' + numericUnitPrice[selLang] + '</li>';
            }
            else if(floatprice > 99999999) {
                message += '<li>' + maxAmountUnitPrice[selLang] + '</li>';
            }         
        }
        if (message != ""){
            $("#divError").css('display','block');
            $("#divError").append(message);
            return false;
        }
        if (confirm(confirmSave[selLang])){
            var deferred = insertNewProduct();
            deferred.done(function(){
                $.unblockUI();
                location.reload();
            });
        }
    });
    /*クリアボンタをクリック */
    $("#btnClear").click(function() {
        $("#divError").css('display','none');
        $("#divError li").remove();
        ("#newProductName").val("");
        $("#newStandard").val("");
        $("#newAmountUnit").val("");
        $("#newCatalogCode").val("");
        $("#newMaker").val("");
        $("#newSupplier").prop('selectedIndex',0);
        $("#newUnitPrice").val("");
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
            alert(pleaseSelect[selLang]);
        }
        else {
            if (confirm(confirmRegist[selLang].replace('{0}',arrayChecked.length))) {
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
                    if($(this).hasClass('inpOrderRequestNumber')){
                        var maxnum = parseInt($(this).attr('max'));
                        var minnum = parseInt($(this).attr('min'));
                        var thisnum = parseInt($(this).val());
                        if (thisnum > maxnum){
                            $(this).val($(this).parent().children('.spnOrderInputNumber').html());
                        }
                        if (thisnum < minnum){
                            $(this).val($(this).parent().children('.spnOrderInputNumber').html());
                        }
                    }
                    var id=$(this).parent().parent().find('input[name=cartId]').val();
                    var val = Number($(this).val()).toLocaleString();
                    if ($(this).hasClass("inpOrderUnitPrice")){
                        val = '\\' + val;
                    }
                    $(this).parent().children('.spnOrderInputNumber').html(val);
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
                    $(this).parent().nextAll('.tdOrderTotalFee').html('\\' + totalfee);
                }
            }
        },
        "blur":function() {
            if ($(this).val() !== "" && isFinite($(this).val())) {
                if($(this).hasClass('inpOrderRequestNumber')){
                    var maxnum = parseInt($(this).attr('max'));
                    var minnum = parseInt($(this).attr('min'));
                    var thisnum = parseInt($(this).val());
                    if (thisnum > maxnum){
                        $(this).val($(this).parent().children('.spnOrderInputNumber').html());
                    }
                    if (thisnum < minnum){
                        $(this).val($(this).parent().children('.spnOrderInputNumber').html());
                    }
                }
                var id=$(this).parent().parent().find('input[name=cartId]').val();
                var val = Number($(this).val()).toLocaleString();
                if ($(this).hasClass("inpOrderUnitPrice")){
                    val = '\\' + val;
                }
                $(this).parent().children('.spnOrderInputNumber').html(val);
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
                $(this).parent().nextAll('.tdOrderTotalFee').html('\\' + totalfee);
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

    if(sessionStorage.getItem('IsFavoriteSharedChecked') !== null){
        var isShared = sessionStorage.getItem('IsFavoriteSharedChecked')=='true' ? true : false;
        $("#chkShared").prop('checked',isShared);
    }
    
    $("#favoriteTreeReagent").favoriteTreeCreate('OrderRequest','favoriteTree',false);
       
    
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

    function insertNewProduct(){
        var ref = {
            'ItemClass' : $("input[name=newItemClass]:checked").val(),
            'SupplierId' : $("#newSupplier option:selected").val(),
            'MakerNameJp' : $("#newMaker").val(),
            'CatalogCode' : $("#newCatalogCode").val(),
            'ItemNameJp' : $("#newProductName").val(),
            'AmountUnit' : $("#newAmountUnit").val(),
            'Standard' : $("#newStandard").val(),
            'UnitPrice' : $("#newUnitPrice").val()
        }
        processing();
        var deferred = new $.Deferred();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'OrderRequest/newProductStore',
            type: 'GET',
            datatype: 'json',
            data : ref
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
$(window).on("load", function(){
    loadingStart();
    $("#table-orderRequestFixed > tbody > tr").each(function() {
        var target = $(this).find(".inpOrderRequestNumber");
        var minnum = target.attr('min');
        var maxnum = target.attr('max');
        target.attr('title',betweenNumber[selLang].replace('{0}',minnum).replace('{1}',maxnum));
    });
    
    /*.tooltip({
        content: function() {
            console.log($(this).attr('title'));
            return $(this).attr('title');
        }
    });*/

});