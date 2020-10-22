jQuery (function ()
{

    /*ウィンドウの高さを取得して、グリッドの高さを指定*/
    settingGridHeight();
 
    var startDate = $('#txtStartDate').datepicker().on("change", function() {
        endDate.datepicker("option","minDate", getDate(this));
    });

    var endDate = $("#txtEndDate").datepicker().on("change", function () {
        startDate.datepicker("option", "maxDate", getDate(this));
    });


    $(window).resize(function () {
        settingGridHeight();
    });
    /*高さ調整*/
    function settingGridHeight() {
        /*ウィンドウの高さを取得して、グリッドの高さを指定*/
        var h = window.innerHeight ? window.innerHeight : $(window).height();

        if ($(".table-purchaseFixed").length > 0) {
            $(".table-purchaseFixed tbody").css('height', h - 300 + 'px');
            var tblwidth = parseInt($(".table-purchaseFixed").css('width').replace('px',''));
            tblwidth = tblwidth - 940;
            $(".table-purchaseFixed thead th:nth-child(4)").css('width',tblwidth + 'px');
            $(".table-purchaseFixed tbody td:nth-child(4)").css('width',tblwidth + 'px');
        }
    }

    $("#btnCsv").click(function() {
        $(this).parent("div").parent("form").attr('action','Purchase/outputCSV');
        $(this).parent("div").parent("form").submit();
    });
    $("#btnSearch").click(function() {
        $(this).parent("div").parent("form").attr('action','Purchase');
        $(this).parent("div").parent("form").submit();
    });
    $("input[name=searchWord]").keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){//enter
            $(this).parent("div").parent("div").parent("form").attr('action','Purchase');
            $(this).parent("div").parent("div").parent("form").submit();
        }
    });

    $("input[name=btnOrderRequest]").click(function() {
        $("#txtOrderNumber").val($(this).next("input[name=hidDeliveryNumber]").val());
        $("#txtOrderRemark").val("");
        $("#hidInsertOrderId").val($(this).next().next("input[name=hidOrderId]").val());
        $("#modal-orderRequest").modal('show');
        $("#txtOrderNumber").focus();
    });
    $("#btnOrderRequest").click(function() {
        var message = "";
        $("#divError").css('display','none');
        $("#divError li").remove();
        if ($("#txtOrderNumber").val()==""){
            message += '<li>数量は必須です</li>';
        }
        else{
            var intnum = parseInt($("#txtOrderNumber").val());
            if (isNaN(intnum)){
                message += '<li>数量は「数字」のみ有効です</li>';
            }
            else if(intnum > 9999) {
                message += '<li>数量は9,999以下のみ有効です</li>';
            }         
        }        
        if ($("#txtOrderRemark").val()!=""){
            if($("#txtOrderRemark").val().length > 100){
                message += '<li>備考は100文字以下のみ有効です</li>';
            }
        }
        if (message != ""){
            $("#divError").css('display','block');
            $("#divError").append(message);
            return false;
        }
        var deferred = insertOrderRequest();
        deferred.done(function(){
            $.unblockUI();
        });
    });
    $("#btnClear").click(function() {
        $("#divError").css('display','none');
        $("#divError li").remove();
        $("#txtOrderNumber").val("");
        $("#txtOrderRemark").val("");
    });

    /*一覧の行をダブルクリック */
    $(".table-purchaseFixed-tr").dblclick(function() {
        $("#detailAmount").html($(this).children("td").eq(3).children(".hidAmountUnit").val());
        $("#detailProductName").html($(this).children("td").eq(3).children("p").eq(0).html());
        $("#detailStandard").html($(this).children("td").eq(3).children(".hidStandard").val());
        $("#detailCatalogCode").html($(this).children("td").eq(3).children(".hidCatalogCode").val());
        $("#detailMakerName").html($(this).children("td").eq(3).children(".hidMakerNameJp").val());
        $("#detailUnitPrice").html($(this).children("td").eq(4).html());
        $("#detailSupplierName").html($(this).children("td").eq(3).children(".hidSupplierNameJp").val());
        $("#detailRemark").html($(this).children("td").eq(3).children(".hidOrderRemark").val());
        $("#modal-detail").modal('show');
    });
    /*行のダブルクリックイベントをキャンセル*/
    $("input[name=btnOrderRequest]").dblclick(function(event) {
        event.stopPropagation();
    });

    function insertOrderRequest(){
        
        processing();
        var deferred = new $.Deferred();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'Purchase/insertOrderRequest',
            type: 'GET',
            datatype: 'json',
            data : {
                'OrderId' : $("#hidInsertOrderId").val(),
                'OrderNumber' : $("#txtOrderNumber").val(),
                'OrderRemark' : $("#txtOrderRemark").val()
            }
        })
        // Ajaxリクエスト成功時の処理
        .done(function(data) {
            if (data['status'] == 'NG') {
                alert(data['errorMsg']);
            }
            else if(data['status'] == 'OK') {
                $("#modal-orderRequest").modal('hide');
                alert('発注を依頼しました');
            }
        })
        // Ajaxリクエスト失敗時の処理
        .fail(function(data) {
            alert('データ更新に失敗しました' +  alert(data['errorMsg']));
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

