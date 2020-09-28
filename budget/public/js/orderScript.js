jQuery (function ()
{
    $(".parentBudget").budgetAccordion();

    $('[data-toggle="tooltip"]').tooltip();

    /*ウィンドウの高さを取得して、グリッドの高さを指定*/
    settingGridHeight();
    $(".table-orderFixed").css('display','none').fadeIn(0);

    $(window).resize(function () {
        settingGridHeight();
    });
    /*高さ調整660*/
    function settingGridHeight() {
        /*ウィンドウの高さを取得して、グリッドの高さを指定*/
        var h = window.innerHeight ? window.innerHeight : $(window).height();
        var yosan = 0;
        if ($(".bottom-fixed-200").css('display') == 'none') {
            yosan = 130;
        }

        $("#budgetTree").css('height', h - 350 + 'px');
        $(".leftside-fixed-240 .tabContent .sectionOrderRequest").css('height', h - 400 + yosan + 'px');
        if ($(".table-orderFixed").length > 0) {
            $(".table-orderFixed tbody").css('height', h - 400 + yosan + 'px');
            var tblwidth = parseInt($(".table-orderFixed").css('width').replace('px',''));
            tblwidth = tblwidth - 1040;
            $(".table-orderFixed thead th:nth-child(3)").css('width',tblwidth + 'px');
            $(".table-orderFixed tbody td:nth-child(3)").css('width',tblwidth + 'px');
        }else{
            if ($(".divNoData").length){
                $(".divNoData").css('height', h - 350 + yosan + 'px');
            }
        }
        if ($(".table-orderProcessingFixed").length > 0) {
            $(".table-orderProcessingFixed tbody").css('height', h - 400 + yosan + 'px');
            var tblwidth = parseInt($(".table-orderProcessingFixed").css('width').replace('px',''));
            console.log(tblwidth);
            tblwidth = tblwidth - 850;
            $(".table-orderProcessingFixed thead th:nth-child(4)").css('width',tblwidth + 'px');
            $(".table-orderProcessingFixed tbody td:nth-child(4)").css('width',tblwidth + 'px');
        }else{
            if ($(".divNoData").length){
                $(".divNoData").css('height', h - 350 + yosan + 'px');
            }
        }
        var tblwidth_yosan = parseInt($(".table-yosan-under-list").css('width').replace('px',''));
        tblwidth_yosan = tblwidth_yosan - 780;
        $(".table-yosan-under-list thead th:nth-child(1)").css('width',tblwidth_yosan + 'px');
        $(".table-yosan-under-list tbody td:nth-child(1)").css('width',tblwidth_yosan + 'px');
    }

    $("#btnToOrderList").click(function() {
        $("#divOrderRequestList").hide();
        $("#divOrderList").show();
        settingGridHeight();
    });

    $("#btnReturnOrderRequestList").click(function() {
        $("#divOrderRequestList").show();
        $("#divOrderList").hide();
        settingGridHeight();
    });

    $("#btnHowToOrder").click(function() {
        $("#modal-howto-order").modal('show');
    });

    /*一覧の行をダブルクリック */
    $(".table-orderFixed-tr").dblclick(function() {
        $("#detailAmount").html($(this).children("td").eq(3).html());
        $("#detailProductName").html($(this).children("td").eq(2).html());
        $("#detailStandard").html($(this).children("td").eq(4).html());
        $("#detailCatalogCode").html($(this).children("td").eq(5).html());
        $("#detailMakerName").html($(this).children("td").eq(6).html());
        $("#detailUnitPrice").html($(this).children("td").eq(7).html());
        $("#detailSupplierName").html("");

        $("#modal-detail").modal('show');
    });

    $(".table-orderFixed-tr").on('contextmenu',function(e){
        if ($(this).hasClass('table-fixed-selectRow')){
            var orderrequestid = $(this).children('td').eq(11).html();
            popmenu({
                id : 'popmenu-budgetlist',
                key : orderrequestid,
            });
        }
      	return false;
    });

    $("ul.childBudget > li").on('contextmenu',function(e){
        if ($(this).hasClass('table-fixed-selectRow')){
            var orderrequestid = $(this).children('span').eq(4).html();
            popmenu({
                id : 'popmenu-orderrequestlist',
                key : orderrequestid,
            });
        }
      	return false;
    });

    /*選択した予算リストの色を変更する*/
    $(".childBudget > li").click(function () {
        $(".childBudget > li").removeClass("table-fixed-selectRow");
        $(".childBudget > li").removeClass("table-fixed-nonselect");
        $(this).addClass("table-fixed-selectRow");
    });
    
    /*予算一覧表示・非表示*/
    $("#toggle-button-order").click(function() {
        if ($(".bottom-fixed-200").css('display') == 'none') {
            $(".bottom-fixed-200").show();
            var h = window.innerHeight ? window.innerHeight : $(window).height();
            if ($(".table-orderFixed").length){
                $(".table-orderFixed tbody").css('height', h - 400 + 'px');
            }
            else{
                if ($(".divNoData").length){
                    $(".divNoData").css('height', h - 350 + 'px');
                }               
            }
            $("#budgetTree").css('height', h - 350 + 'px');
            if ($(".table-orderProcessingFixed").length) {
                $(".table-orderProcessingFixed tbody").css('height', h - 400 + 'px');
            }else{
                if ($(".divNoData").length){
                    $(".divNoData").css('height', h - 350 + 'px');
                }
            }
        }
        else {
            //$(".bottom-fixed-200").hide('blind',600);
            $(".bottom-fixed-200").hide();
            var h = window.innerHeight ? window.innerHeight : $(window).height();
            if ($(".table-orderFixed").length){
                $(".table-orderFixed tbody").css('height', h - 270 + 'px');
            }
            else{
                if ($(".divNoData").length){
                    $(".divNoData").css('height', h - 220 + 'px');
                }               
            }
            $("#budgetTree").css('height', h - 220 + 'px');
            if ($(".table-orderProcessingFixed").length) {
                $(".table-orderProcessingFixed tbody").css('height', h - 270 + 'px');
            }else{
                if ($(".divNoData").length){
                    $(".divNoData").css('height', h - 220 + 'px');
                }
            }
        }
    });

    $('.inpOrderInputNumber').on({
        "keypress":function(e) {
            if (e.which == 13) {
                if ($(this).val() !== "" && isFinite($(this).val())) {
                    var id=$(this).parent().parent().find('input[name=orderreqId]').val();
                    $(this).parent().children('.spnOrderInputNumber').html(Number($(this).val()).toLocaleString());
                    $(this).css('display','none');
                    $(this).parent().children('.spnOrderInputNumber').css('display','inline-block');
                    var price = Number($(this).parent().parent().children('.tdOrderInputNumber').children('.inpOrderUnitPrice').val());
                    var ordernum = $(this).parent().parent().children('.tdOrderInputNumber').children('.inpOrderRequestNumber').val();
                    console.log('id:'+id);
                    console.log('price:'+price);
                    console.log('ordernum:'+ordernum);
                    var deferred = updateOrder(id,price,ordernum);
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
                var id=$(this).parent().parent().find('input[name=orderreqId]').val();
                $(this).parent().children('.spnOrderInputNumber').html(Number($(this).val()).toLocaleString());
                $(this).css('display','none');
                $(this).parent().children('.spnOrderInputNumber').css('display','inline-block');
                var price = Number($(this).parent().parent().children('.tdOrderInputNumber').children('.inpOrderUnitPrice').val());
                var ordernum = $(this).parent().parent().children('.tdOrderInputNumber').children('.inpOrderRequestNumber').val();
                var deferred = updateOrder(id,price,ordernum);
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
    $(".table-orderFixed-tr > td:not(.tdOrderInputNumber)").click(function() {
        $('.inpOrderInputNumber').css('display','none');
        $('.spnOrderInputNumber').css('display','inline-block');
    });

    function updateOrder(id,price,ordernum) {
        processing();
        var deferred = new $.Deferred();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'Order/updateListPrice',
            type: 'GET',
            datatype: 'json',
            data : {'id' : id, 'price' : price, 'ordernum' : ordernum}
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

/*発注依頼に予算IDを付与する*/
function updateOrderRequestGiveBudget(budgetid,orderrequestid){
    processing();
    var deferred = new $.Deferred();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: 'Order/updateOrderRequestGiveBudget',
        type: 'GET',
        datatype: 'json',
        data : {'budgetid' : budgetid, 'orderrequestid' : orderrequestid}
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
