jQuery (function ()
{

    /*(".table-orderFixed").css('display','none').fadeIn(0);*/
    if (sessionStorage.getItem('orderSetDisplayScreen')){
        $("#" + sessionStorage.getItem('orderSetDisplayScreen')).css('display','none').fadeIn(500);
        if (sessionStorage.getItem('orderSetDisplayScreen')=='divOrderList'){
            $(".table-yosan-under-list").css('min-width','1190px');
        }else if (sessionStorage.getItem('orderSetDisplayScreen')=='divOrderRequestList'){
            $(".table-yosan-under-list").css('min-width','1590px');
        }
    }
    else{
        $("#divOrderRequestList").css('display','none').fadeIn(500);
        $(".table-yosan-under-list").css('min-width','1590px');
    }
    $(".bottom-fixed-200").css('display','none').fadeIn(500);
    $(".parentBudget").budgetAccordion();

    $('[data-toggle="tooltip"]').tooltip();

    /*ウィンドウの高さを取得して、グリッドの高さを指定*/
    settingGridHeight();
 
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
        }
        if ($(".table-orderProcessingFixed").length > 0) {
            $(".table-orderProcessingFixed tbody").css('height', h - 400 + yosan + 'px');
            var tblwidth = parseInt($(".table-orderProcessingFixed").css('width').replace('px',''));
            
            tblwidth = tblwidth - 850;
            $(".table-orderProcessingFixed thead th:nth-child(4)").css('width',tblwidth + 'px');
            $(".table-orderProcessingFixed tbody td:nth-child(4)").css('width',tblwidth + 'px');
        }else{
            if ($(".divNoData").length){
                $(".divNoData").css('height', h - 350 + yosan + 'px');
            }
        }
        var tblwidth_yosan = parseInt($(".table-yosan-under-list").css('width').replace('px',''));
        tblwidth_yosan = tblwidth_yosan - 773;
        $(".table-yosan-under-list thead th:nth-child(1)").css('width',tblwidth_yosan + 'px');
        $(".table-yosan-under-list tbody td:nth-child(1)").css('width',tblwidth_yosan + 'px');
    }

    /*発注リスト画面表示*/
    $("#btnToOrderList").click(function() {
        show_display('divOrderList');
    });

    /*発注依頼一覧画面表示*/
    $("#btnReturnOrderRequestList").click(function() {
        show_display('divOrderRequestList');
    });
    /*全て選択or解除チェック*/
    $("input[name=chkTargetAll").click(function() {
        if($(this).is(':checked')) {
            $('input[name="chkTarget[]"]').prop('checked',true);
        } else {
            $('input[name="chkTarget[]"]').prop('checked',false);
        }
    });

    /*ToolTipの設定 */
    $('.childBudget > li > span:first-child').tooltip({
        content: function() {
            return $(this).attr('title');
        }
    });
    
    function show_display(id) {
        if (id=='divOrderList'){
            $("#divOrderRequestList").hide();
            $("#divOrderList").show('slide', {direction: "right"}, 800);
            $(".table-yosan-under-list").css('min-width','1190px');
        }
        else if (id=='divOrderRequestList'){
            $("#divOrderRequestList").show('slide', {direction: "left"}, 800);
            $("#divOrderList").hide();
            $(".table-yosan-under-list").css('min-width','1590px');
        }
        else {
            $("#divOrderRequestList").show('slide', {direction: "left"}, 800);
            $("#divOrderList").hide();
            $(".table-yosan-under-list").css('min-width','1590px');
            id="divOrderRequestList";            
        }
        sessionStorage.setItem('orderSetDisplayScreen',id);
        settingGridHeight();
    }

    /*一覧の行をダブルクリック */
    $(".table-orderFixed-tr").dblclick(function() {
        $("#detailAmount").html($(this).children("td").eq(3).html());
        $("#detailProductName").html($(this).children("td").eq(2).html());
        $("#detailStandard").html($(this).children("td").eq(4).html());
        $("#detailCatalogCode").html($(this).children("td").eq(5).html());
        $("#detailMakerName").html($(this).children("td").eq(6).html());
        $("#detailUnitPrice").html($(this).children("td").eq(7).html());
        $("#detailSupplierName").html($(this).children("td").eq(12).html());
        $("#detailRemark").html($(this).children("td").eq(13).html());
        $("#modal-detail").modal('show');
    });
    /*行のダブルクリックイベントをキャンセル*/
    $(".tdOrderInputNumber input").dblclick(function(event) {
        event.stopPropagation();
    });
    
    /*予算リストへのコンテキストメニュー */
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

    /*予算リストから削除するコンテキストメニュー*/
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
                    /*if($(this).hasClass('inpOrderRequestNumber')){*/
                        var maxnum = parseInt($(this).attr('max'));
                        var minnum = parseInt($(this).attr('min'));
                        var thisnum = parseInt($(this).val());
                        if (thisnum > maxnum){
                            $(this).val($(this).parent().children('.spnOrderInputNumber').html());
                        }
                        if (thisnum < minnum){
                            $(this).val($(this).parent().children('.spnOrderInputNumber').html());
                        }
                    /*}*/
                    var id=$(this).parent().parent().find('input[name=orderreqId]').val();
                    var val = Number($(this).val()).toLocaleString();
                    if ($(this).hasClass("inpOrderUnitPrice")){
                        val = '\\' + val;
                    }
                    $(this).parent().children('.spnOrderInputNumber').html(val);
                    $(this).css('display','none');
                    $(this).parent().children('.spnOrderInputNumber').css('display','inline-block');
                    var price = Number($(this).parent().parent().children('.tdOrderInputNumber').children('.inpOrderUnitPrice').val());
                    var ordernum = $(this).parent().parent().children('.tdOrderInputNumber').children('.inpOrderRequestNumber').val();
                    var ret = updateOrder(id,price,ordernum);
                    var deferred = ret.deferred;
                    deferred.done(function(){
                        $.unblockUI();
                        if (!ret.result){
                            location.href = './Error/systemError';
                        }
                    });  
                    var totalfee = (price * ordernum).toLocaleString();
                    $(this).parent().nextAll('.tdOrderTotalFee').html('\\' + totalfee);
                }
            }
            else {
                return onlyNumber(e);
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
                var id=$(this).parent().parent().find('input[name=orderreqId]').val();
                var val = Number($(this).val()).toLocaleString();
                if ($(this).hasClass("inpOrderUnitPrice")){
                    val = '\\' + val;
                }
                $(this).parent().children('.spnOrderInputNumber').html(val);
                $(this).css('display','none');
                $(this).parent().children('.spnOrderInputNumber').css('display','inline-block');
                var price = Number($(this).parent().parent().children('.tdOrderInputNumber').children('.inpOrderUnitPrice').val());
                var ordernum = $(this).parent().parent().children('.tdOrderInputNumber').children('.inpOrderRequestNumber').val();
                var ret = updateOrder(id,price,ordernum);
                var deferred = ret.deferred;
                deferred.done(function(){
                    $.unblockUI();
                    if (!ret.result){
                        location.href = './Error/systemError';
                    }
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
    $(".table-orderFixed-tr > td:not(.tdOrderInputNumber)").click(function() {
        $('.inpOrderInputNumber').css('display','none');
        $('.spnOrderInputNumber').css('display','inline-block');
    });

    $('.selOrderSelectSupplier').on({
        "keypress":function(e) {
            if (e.which == 13) {
                var id=$(this).parent().parent().find('input[name=orderreqId]').val();
                var selval = $(this).val();
                var prevval = $(this).parent().children('.spnSupplierId').html();
                var seltext = $(this).children('option:selected').text();
                $(this).parent().children('.spnSupplierId').html(selval);
                $(this).parent().children('.spnOrderSelectSupplier').html(seltext);
                $(this).css('display','none');
                $(this).parent().children('.spnOrderSelectSupplier').css('display','inline-block');
                if (prevval != selval){
                    var ret = updateSupplier(id,selval);
                    var deferred = ret.deferred;
                    deferred.done(function(){
                        $.unblockUI();
                        if (!ret.result){
                            location.href = './Error/systemError';
                        }
                    });  
                }
            }
        },
        "blur":function() {
            var id=$(this).parent().parent().find('input[name=orderreqId]').val();
            var selval = $(this).val();
            var prevval = $(this).parent().children('.spnSupplierId').html();
            var seltext = $(this).children('option:selected').text();
            $(this).parent().children('.spnSupplierId').html(selval);
            $(this).parent().children('.spnOrderSelectSupplier').html(seltext);
            $(this).css('display','none');
            $(this).parent().children('.spnOrderSelectSupplier').css('display','inline-block');
            if (prevval != selval){
                var ret = updateSupplier(id,selval);
                var deferred = ret.deferred;
                deferred.done(function(){
                    $.unblockUI();
                    if (!ret.result){
                        location.href = './Error/systemError';
                    }
                });  
        }
        }
    });    
    $(".tdOrderSelectSupplier").click(function () {
        if ($(this).children('.spnOrderSelectSupplier').css('display')!='none') {
            $('.selOrderSelectSupplier').css('display','none');
            $('.spnOrderSelectSupplier').css('display','inline-block');
            $(this).children('.spnOrderSelectSupplier').css('display','none');
            $(this).children('.selOrderSelectSupplier').css('display','inline-block');
            $('.selOrderSelectSupplier').val($(this).children('.spnSupplierId').html());
            $(this).children('.selOrderSelectSupplier').focus();
        }
    });    

    $(".table-orderProcessingFixed-t > td:not(.tdOrderSelectSupplier)").click(function() {
        $('.spnOrderSelectSupplier').css('display','inline-block');
    });

    //リスト削除
    $("input[name=btnListOrderDelete]").click(function() {
        if (confirm(confirmDelete[selLang])){
            $(this).parent("form").submit();
        }
    });

    /*発注方法ポップアップ画面表示*/
    $("#btnHowToOrder").click(function() {
        $("#modal-howto-order").modal('show');
    });

    $("#btnCircle_Email").click(function() {
        checkUpdateExecOrder(0);
    });
    $("#btnCircle_PDF").click(function() {
        checkUpdateExecOrder(1);
    });
    $("#btnCircle_Other").click(function() {
        checkUpdateExecOrder(9);
    });

    function checkUpdateExecOrder(howToOrderFlag){
        var arrayChecked = $("#table-orderProcessingFixed tr").children('td').children('input[type=checkbox]:checked');
        var arrayOrderRequestIds = [];
        if (arrayChecked.length <= 0){
            alert('発注対象を選択してください');
        }
        else {
            if (confirm('発注処理を行いますか？' + '【対象：' + arrayChecked.length + '件】')) {
                $("#modal-howto-order").modal('hide');
                arrayChecked.each(function (index,element){
                    arrayOrderRequestIds.push($(element).parent().parent().find('input[name=orderreqId]').val());
                });
                if (howToOrderFlag==1){/*PDF出力*/
                    $("input[name=arrayOrderRequestIds]").val(arrayOrderRequestIds);
                    $("#table-orderProcessingFixed tr").children('td').children('input[type=checkbox]:checked').parent('td').parent('tr').remove();
                    $("#frmPdfOutput").submit();
                }
                else{
                    var ret = updateExecOrder(arrayOrderRequestIds,howToOrderFlag);
                    var deferred = ret.deferred;
                    deferred.done(function(){
                        if (ret.result){
                            $.unblockUI();
                            location.reload();
                        }else{
                            location.href = './Error/systemError';
                        }
                    });
                }
            }
        }
    }


    function updateExecOrder(arrayOrderRequestIds,howToOrderFlag) {
        var ret = new Object();
        processing();
        var deferred = new $.Deferred();
        ret.result = true;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'Order/orderExec',
            type: 'GET',
            datatype: 'json',
            data : {'arrayOrderRequestIds' : arrayOrderRequestIds, 'howToOrderFlag' : howToOrderFlag}
        })
        // Ajaxリクエスト成功時の処理
        .done(function(data) {
            if (data['status'] == 'NG' || data['status'] == undefined) {
                //alert(data['errorMsg']);
                ret.result = false;
            }
        })
        // Ajaxリクエスト失敗時の処理
        .fail(function(data) {
            //alert(processingFailed[selLang] +  alert(data['errorMsg']));
            ret.result = false;
        })
        .always(function(data) {
            deferred.resolve();           
        });
        
        ret.deferred = deferred;

        return ret;
    }

    function updateOrder(id,price,ordernum) {
        var ret = new Object();
        processing();
        var deferred = new $.Deferred();
        ret.result = true;
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
            console.log(data['status'] );
            if (data['status'] !== 'OK') {
                //alert(processingFailed[selLang] + data['status']);
                ret.result = false;
            }
        })
        // Ajaxリクエスト失敗時の処理
        .fail(function(data) {
            //alert(processingFailed[selLang] + data['status']);
            ret.result = false;
        })
        .always(function(data) {
            deferred.resolve();           
        });
        
        ret.deferred = deferred;

        return ret;
    }
    function updateSupplier(id,supplierid) {
        var ret = new Object();
        processing();
        var deferred = new $.Deferred();
        ret.result = true;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'Order/updateSupplier',
            type: 'GET',
            datatype: 'json',
            data : {'id' : id, 'supplierid' : supplierid}
        })
        // Ajaxリクエスト成功時の処理
        .done(function(data) {
            if (data['status'] !== 'OK') {
                //alert(processingFailed[selLang] + data['status']);
                ret.result = false;
            }
        })
        // Ajaxリクエスト失敗時の処理
        .fail(function(data) {
            //alert(processingFailed[selLang] + data['status']);
            ret.result = false;
        })
        .always(function(data) {
            deferred.resolve();           
        });
        
        ret.deferred = deferred;

        return ret;
    }



})
$(window).on("load", function(){
    loadingStart();
    $("#table-orderFixed > tbody > tr").each(function() {
        var target = $(this).find(".inpOrderRequestNumber");
        var minnum = target.attr('min');
        var maxnum = target.attr('max');
        target.attr('title',betweenNumber[selLang].replace('{0}',minnum).replace('{1}',maxnum));
    });
});

/*発注依頼に予算IDを付与する*/
function updateOrderRequestGiveBudget(budgetid,orderrequestid){
    var ret = new Object();
    processing();
    var deferred = new $.Deferred();
    ret.result = true;
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
            //alert(processingFailed[selLang] + data['status']);
            ret.result = false;
        }
    })
    // Ajaxリクエスト失敗時の処理
    .fail(function(data) {
        //alert(processingFailed[selLang] + data.message);
        ret.result = false;
    })
    .always(function(data) {
        deferred.resolve();           
    });
    
    ret.deferred = deferred;

    return ret;
}
