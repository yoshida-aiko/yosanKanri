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

        if ($(".table-deliveryFixed").length > 0) {
            $(".table-deliveryFixed tbody").css('height', h - 240 + 'px');
            var tblwidth = parseInt($(".table-deliveryFixed").css('width').replace('px',''));
            tblwidth = tblwidth - 1260;
            $(".table-deliveryFixed thead th:nth-child(3)").css('width',tblwidth + 'px');
            $(".table-deliveryFixed tbody td:nth-child(3)").css('width',tblwidth + 'px');
        }else{
            if ($(".divNoData").length){
                $(".divNoData").css('height', h - 190  + 'px');
            }
        }
    }

    /*全て選択or解除チェック*/
    $("input[name=chkTargetAll").click(function() {
        if($(this).is(':checked')) {
            $('input[name="chkTarget[]"]').prop('checked',true);
        } else {
            $('input[name="chkTarget[]"]').prop('checked',false);
        }
    });

    $('.inpDeliveryDate').datepicker();

    /*納品ボタンクリック*/
    $("#btnDelivery").click(function() {
        var arrayChecked = $("#table-deliveryFixed tr").children('td').children('input[type=checkbox]:checked');
        if (arrayChecked.length <= 0){
            alert('納品対象を選択してください');
        }
        else {
            if (confirm('納品処理を行いますか？' + '【対象：' + arrayChecked.length + '件】')) {
                var deferred = insertDelivery();
                deferred.done(function(){
                    $.unblockUI();
                    $('input[name="chkTarget[]"]').prop('checked',false);
                    location.reload();
                });
            }
        }
    });


    /*一覧の行をダブルクリック */
    $(".table-deliveryFixed-tr").dblclick(function() {
        $("#detailAmount").html($(this).children("td").eq(13).html());
        $("#detailProductName").html($(this).children("td").eq(19).html());
        $("#detailStandard").html($(this).children("td").eq(14).html());
        $("#detailCatalogCode").html($(this).children("td").eq(15).html());
        $("#detailMakerName").html($(this).children("td").eq(17).html());
        var floatPrice = 0;
        if (!isNaN($(this).children("td").eq(16).html())){
            floatPrice = parseFloat($(this).children("td").eq(16).html());
        }
        $("#detailUnitPrice").html(floatPrice.toLocaleString());
        $("#detailSupplierName").html($(this).children("td").eq(18).html());

        $("#modal-detail").modal('show');
    });
    /*行のダブルクリックイベントをキャンセル*/
    $(".tdOrderInputNumber input").dblclick(function(event) {
        event.stopPropagation();
    });
    $(".tdBudget select").dblclick(function(event) {
        event.stopPropagation();
    });
    $('.inpOrderInputNumber').on({
        "keypress":function(e) {
            if (e.which == 13) {
                if ($(this).val() !== "" && isFinite($(this).val())) {
                    var id=$(this).parent().parent().find('input[name=orderreqId]').val();
                    $(this).parent().children('.spnOrderInputNumber').html(Number($(this).val()).toLocaleString());
                    $(this).css('display','none');
                    $(this).parent().children('.spnOrderInputNumber').css('display','inline-block');
                    if ($(this).hasClass('inpDeliveryExpectedNumber')){
                        var price = Number($(this).parent().children('.hidUnitPrice').val());
                        var num = Number($(this).val());
                        var total = price * num;
                        $(this).parent().parent().children('.tdSummaryPrice').children('.inpSummaryPrice').val(total);
                        $(this).parent().parent().children('.tdSummaryPrice').children('.spnOrderInputNumber').html(total.toLocaleString());
                    }
                    /*var deferred = updateOrder(id,price,ordernum);
                    deferred.done(function(){
                        $.unblockUI();
                    });  */
                }
            }
        },
        "blur":function() {
            if ($(this).val() !== "" && isFinite($(this).val())) {
                var id=$(this).parent().parent().find('input[name=orderreqId]').val();
                $(this).parent().children('.spnOrderInputNumber').html(Number($(this).val()).toLocaleString());
                $(this).css('display','none');
                $(this).parent().children('.spnOrderInputNumber').css('display','inline-block');
                if ($(this).hasClass('inpDeliveryExpectedNumber')){
                    var price = Number($(this).parent().children('.hidUnitPrice').val());
                    var num = Number($(this).val());
                    var total = price * num;
                    $(this).parent().parent().children('.tdSummaryPrice').children('.inpSummaryPrice').val(total);
                    $(this).parent().parent().children('.tdSummaryPrice').children('.spnOrderInputNumber').html(total.toLocaleString());
                }
                /*var deferred = updateOrderInfo(id,-1,selval);
                deferred.done(function(){
                    $.unblockUI();
                });*/
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

    $('.selSelectBudget').on({
        "keypress":function(e) {
            if (e.which == 13) {
                var id=$(this).parent().parent().find('input[name=orderId]').val();
                var selval = $(this).val();
                var prevval = $(this).parent().children('.spnBudgetId').html();
                var seltext = $(this).children('option:selected').text();
                $(this).parent().children('.spnBudgetId').html(selval);
                $(this).parent().children('.spnSelectBudget').html(seltext);
                $(this).css('display','none');
                $(this).parent().children('.spnSelectBudget').css('display','inline-block');
                /*if (prevval != selval){
                    var deferred = updateOrderInfo(id,-1,selval);
                    deferred.done(function(){
                        $.unblockUI();
                    });
                }*/
            }
        },
        "blur":function() {
            var id=$(this).parent().parent().find('input[name=orderId]').val();
            var selval = $(this).val();
            var prevval = $(this).parent().children('.spnBudgetId').html();
            var seltext = $(this).children('option:selected').text();
            $(this).parent().children('.spnBudgetId').html(selval);
            $(this).parent().children('.spnSelectBudget').html(seltext);
            $(this).css('display','none');
            $(this).parent().children('.spnSelectBudget').css('display','inline-block');
            /*if (prevval != selval){
                var deferred = updateOrderInfo(id,-1,selval);
                deferred.done(function(){
                    $.unblockUI();
                });
            }*/
        }
    });    
    $(".tdBudget").click(function () {
        if ($(this).children('.spnSelectBudget').css('display')!='none') {
            $('.selSelectBudget').css('display','none');
            $('.spnSelectBudget').css('display','inline-block');
            $(this).children('.spnSelectBudget').css('display','none');
            $(this).children('.selSelectBudget').css('display','inline-block');
            $('.selSelectBudget').val($(this).children('.spnBudgetId').html());
            $(this).children('.selSelectBudget').focus();
        }
    });    

    $(".table-deliveryFixed-t > td:not(.tdBudget)").click(function() {
        $('.spnSelectBudget').css('display','inline-block');
    });



    function insertDelivery() {
        processing();

        var arrayOrderList = [];            
        $.each($("#table-deliveryFixed > tbody > tr"),function(index,tr){

            if ($(tr).find('[type=checkbox]').prop('checked')){
                var obj = 
                    $(tr).find('[name=orderId]').val() + '@' +
                    $(tr).find('[name=deliveryDate]').val() + '@' +
                    $(tr).find('.inpDeliveryExpectedNumber').val() + '@' +
                    $(tr).find('.inpSummaryPrice').val() + '@' +
                    $(tr).find('.spnBudgetId').html()
                ;
                arrayOrderList.push(obj);
            }
        });
console.log(arrayOrderList);
        var deferred = new $.Deferred();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'Delivery/insertDelivery',
            type: 'GET',
            datatype: 'json',
            data : {'arr' : arrayOrderList}
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
    processing();
    setTimeout('init()',1000);
});

function init() {
    $.unblockUI();
}

