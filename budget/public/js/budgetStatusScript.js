jQuery (function ()
{
    $(".table-budgetFixed > tbody > tr").each(function(index,elem){
        if ($(elem).find("[name=hidBudgetId]").val()==$("[name=hidSelectedBudgetId]").val()){
            $(".table-budgetFixed-tr").removeClass("table-fixed-selectRow");
            $(elem).addClass("table-fixed-selectRow");
            return false;
        }
    });

    /*ウィンドウの高さを取得して、グリッドの高さを指定*/
    settingGridHeight();
 
    $(window).resize(function () {
        settingGridHeight();
    });
    /*高さ調整*/
    function settingGridHeight() {
        /*ウィンドウの高さを取得して、グリッドの高さを指定*/
        var h = window.innerHeight ? window.innerHeight : $(window).height();

        if ($(".table-budgetFixed").length > 0) {
            $(".table-budgetFixed tbody").css('height', (h - 320)/3*2 + 'px');
            
            settingGridWidth();
        }
        $(".table-budgetDetailFixed tbody").css('height', (h - 320)/3 + 'px');
    }

    function settingGridWidth() {
        var tblwidth = parseInt($(".table-budgetFixed").css('width').replace('px',''));
        tblwidth_1 = tblwidth - 740;
        $(".table-budgetFixed thead th:nth-child(2)").css('width',tblwidth_1 + 'px');
        $(".table-budgetFixed tbody td:nth-child(2)").css('width',tblwidth_1 + 'px');
        tblwidth_2 = tblwidth - 500;
        $(".table-budgetDetailFixed thead th:nth-child(2)").css('width',tblwidth_2 + 'px');
        $(".table-budgetDetailFixed tbody td:nth-child(2)").css('width',tblwidth_2 + 'px');
    }

    var startDate = $('#txtStartDate').datepicker().on("change", function() {
        endDate.datepicker("option","minDate", getDate(this));
    });

    var endDate = $("#txtEndDate").datepicker().on("change", function () {
        startDate.datepicker("option", "maxDate", getDate(this));
    });

    $("#txtExecDate").datepicker({
        maxDate: new Date(),
    }).datepicker('setDate',new Date());

    $(".lnkDetail").click(function() {
        var id = $(this).next('input').val();
        $("#hidSelectedBudgetId").val(id);
        var deferred = getDetail(id);
        deferred.done(function(){
            $.unblockUI();
        });
    });

    $("input[name=btnExec]").click(function() {
        $("#hidSelectedBudgetId").val("");
    });

    $("input[name=btnExec]").click(function() {
        $(this).parent("form").attr('action','BudgetStatus');
        $(this).parent("form").submit();
    });

    $("input[name=btnCSV]").click(function() {
        $(this).parent("form").attr('action','BudgetStatus/outputCSV');
        $(this).parent("form").submit();
    });

    /*その他の執行*/
    $("input[name=btnOtherExec]").click(function() {
        $("#hidSelectedBudgetId").val($(this).parent('td').next('td').children('[name=hidBudgetId]').val());
        $("#modal-oherExec").modal('show');
    });
    $("#btnClear").click(function() {
        $("#divError").css('display','none');
        $("#divError li").remove();
        $("#txtExecDate").val(getToday('/'));
        $("#txtExecRemark").val("");
        $("#txtExecPrice").val("");
    });
    /*残高調整実行*/
    $("#btnBalanceExec").click(function() {
        var message = "";
        $("#divError").css('display','none');
        $("#divError li").remove();
        if ($("#txtExecDate").val()==""){
            message += '<li>執行日は必須です</li>';
        }
        if ($("#txtExecRemark").val()==""){
            message += '<li>備考は必須です</li>';
        }
        else{
            if($("#txtExecRemark").val().length > 100){
                message += '<li>備考は100文字以下のみ有効です</li>';
            }
        }
        if ($("#txtExecPrice").val()==""){
            message += '<li>執行額は必須です</li>';
        }
        else{
            var floatprice = parseFloat($("#txtExecPrice").val());
            if (isNaN(floatprice)){
                message += '<li>執行額は「数字」のみ有効です</li>';
            }
            else if(floatprice > 99999999) {
                message += '<li>執行額は99,999,999以下のみ有効です</li>';
            }         
        }
        if (message != ""){
            $("#divError").css('display','block');
            $("#divError").append(message);
            return false;
        }

        var deferred = balanceExec();
        deferred.done(function(){
            $.unblockUI();
            $(".budgetStatusConditionForm").submit();
        });
    });


    function balanceExec(){
        processing();
        var deferred = new $.Deferred();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'BudgetStatus/balanceAdjustment',
            type: 'GET',
            datatype: 'json',
            data : {
                'BudgetId' : $("input[name=hidSelectedBudgetId]").val(),
                'ExecDate' : $("#txtExecDate").val(),
                'ExecRemark' : $("#txtExecRemark").val(),
                'ExecPrice' : $("#txtExecPrice").val()
            }
        })
        // Ajaxリクエスト成功時の処理
        .done(function(data) {
            if (data['status'] == 'NG') {
                alert(data['errorMsg']);
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


    function getDetail(id) {
        processing();
        var deferred = new $.Deferred();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'BudgetStatus/getDetail',
            type: 'GET',
            datatype: 'json',
            data : {
                'BudgetId' : id,
                'startDate' : $("#txtStartDate").val(),
                'endDate' : $("#txtEndDate").val()
            }
        })
        // Ajaxリクエスト成功時の処理
        .done(function(data) {
            if (data['status'] == 'NG') {
                alert(data['errorMsg']);
            }
            else if(data['status'] == 'OK') {
                var datas = data['datas'];
                $("#table-budgetDetailFixed-tbody tr").remove();
                var tbody = $("#table-budgetDetailFixed-tbody");
                for(var i=0;i<datas.length;i++){
                    var html = '<tr><td class="align-center">' + datas[i].ExecDate + '</td>';
                    html +=  '<td>' + datas[i].ItemNameJp + '</td>';
                    html +=  '<td class="align-right">' + datas[i].UnitPrice + '</td>';
                    html +=  '<td class="align-right">' + datas[i].ExecNumber + '</td>';
                    html +=  '<td class="align-right">' + datas[i].ExecPrice + '</td></tr>';
                    tbody.append(html);
                }
                settingGridWidth();
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