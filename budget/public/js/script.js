jQuery (function ()
{
    $(document).keydown(function (event) {
        // クリックされたキーのコード
        var keyCode = event.keyCode;
        // Ctrlキーがクリックされたか (true or false)
        var ctrlClick = event.ctrlKey;
        // Altキーがクリックされたか (true or false)
        var altClick = event.altKey;
        // キーイベントが発生した対象のオブジェクト
        var obj = event.target;

        // バックスペースキーを制御する
        if (keyCode == 8) {
            // テキストボックス／テキストエリアを制御する
            if ((obj.tagName.toUpperCase() == "INPUT" 
                && (obj.type.toUpperCase() != "BUTTON" && obj.type.toUpperCase() != "SUBMIT"))
                || obj.tagName.toUpperCase() == "TEXTAREA") {
                // 入力できる場合は制御しない
                if (!obj.readOnly && !obj.disabled) {
                    return true;
                }
            }
            return false;
        }
    });
    
    if (sessionStorage.getItem('selectedNavMenu') != null){
        var target = sessionStorage.getItem('selectedNavMenu');
        $("#myNavbar > li.selected").removeClass("selected");
        $("#myNavbar > li." + target).addClass("selected");
    }
    else{
        $("#myNavbar > li.selected").removeClass("selected");
        $("#myNavbar > li.nav-home").addClass("selected");
    }
    $("#myNavbar > li").click(function() {
        var classNm = $(this).attr("class");
        
        sessionStorage.setItem('selectedNavMenu',classNm);
        /*$("#myNavbar > li.selected").removeClass("selected");
        $(this).addClass("selected");*/
    });

    /*共用クリック時*/
    $("#chkShared").click(function() {
        sessionStorage.setItem('IsFavoriteSharedChecked',$(this).is(':checked'));
        location.reload();
    });

    /*tableのclick行を選択状態にする */
    $(".table-fixed > tbody > tr").click(function() {
        $(".table-fixed > tbody > tr").removeClass("table-fixed-selectRow");
        $(".table-fixed > tbody > tr").removeClass("table-fixed-nonselect");
        $(this).addClass("table-fixed-selectRow");
    });

    $.fn.budgetAccordion = function() {
        this.on('click', function(event) {
            event.preventDefault();
            if($(this).next('ul').length){
                if ($(this).next('ul').css('display')=='none') {
                    $(this).children('span').eq(0).removeClass('fa-caret-right').addClass('fa-caret-down');
                    $(this).children('span').eq(1).removeClass('fa-folder').addClass('fa-folder-open');
                }
                else {
                    $(this).children('span').eq(0).removeClass('fa-caret-down').addClass('fa-caret-right');
                    $(this).children('span').eq(1).removeClass('fa-folder-open').addClass('fa-folder');
                }
                $(this).next('ul').slideToggle();
            }
        });
    };

    $.fn.favoriteTreeCreate = function(url,id,isToCartDisabled=true) {

        var isShared = false;
        if (sessionStorage.getItem('IsFavoriteSharedChecked')!==null){
            isShared = sessionStorage.getItem('IsFavoriteSharedChecked')=='true'? true : false;
        }
        var deferred = new $.Deferred();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url+'/getData_Favorite',
            type: 'GET',
            datatype: 'json',
            data: {
                'isFavoriteSharedChecked' : isShared
            }
        })
        // Ajaxリクエスト成功時の処理
        .done(function(data) {
            if (data['status'] == 'OK') {
                var id_Reagent = id + 'Reagent';
                var id_Article = id + 'Article';
                jsTreeCreate_sub($.parseJSON(data['jsonFavoriteTreeReagent']),url,id_Reagent,isToCartDisabled);
                jsTreeCreate_sub($.parseJSON(data['jsonFavoriteTreeArticle']),url,id_Article,isToCartDisabled);
            }
            else {
                alert(processingFailed[selLang] + data['status']);
            }
        })
        // Ajaxリクエスト失敗時の処理
        .fail(function(data) {
            alert(processingFailed[selLang] + data['status']);
        })
        .always(function(data) {
            $.unblockUI();
            deferred.resolve();
        });

        return deferred;
    };

    /*ヘッダ部検索*/
    $("input[name=txtHeaderProductSearch]").keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){//enter
           $(this).parent("div").parent("form").submit();
        }
    });
    $("#frmHeaderProductSearch").submit(function() {
        if ($("input[name=txtHeaderProductSearch]").val()==""){
            return false;
        }
        else{
            $(this).submit();
        }
    });


    function jsTreeCreate_sub(jsondata,url,id,isToCartDisabled) {

        $('#' + id).jstree({
            "core":{
                "data":jsondata,
                "check_callback":function(operation,node,node_parent,node_position,more){
                    if (operation=="move_node"){
                        if(node.icon == "jstree-folder" && node_parent.id != "#"){
                            return false;
                        }
                    }
                }
            },
            "state" : { "key": "favoriteTree"},
            "plugins":["contextmenu", "dnd", "state"],
            "contextmenu":{
                "items": function($node) {
                    var tree = $('#' + id).jstree(true);
                    var isNodata = $node.original.key<=-100 ? true : false;
                    return {
                        "Delete": {
                            "separator_before": false,
                            "separator_after": false,
                            "label": "削除",
                            "icon": "contextmenu_deleteicon",
                            "_disabled" : isNodata,
                            "action": function (obj) {
                                if (confirm('削除しますか？')){
                                    deletekey = $node.original.key;
                                    var deferred = deleteTreeAjax(url,deletekey,tree,$node);
                                    deferred.done(function(){
                                        $.unblockUI();
                                    });
                                }
                            }
                        },
                        "createFolder":{
                            "separator_before": true,
                            "separator_after": false,
                            "label": "新規フォルダ作成",
                            "icon": "contextmenu_createfoldericon",
                            "action": function(data){
                               var inst = $.jstree.reference(data.reference),
                                    obj = inst.get_node(data.reference);
                                    inst.create_node('#', { text:'New Folder', 'icon':'jstree-folder' }
                                    , "last", 
                                    function(new_node){
                                        try{
                                            inst.edit(new_node, new_node.text, function(data){
                                                var itemclass = $('#' + id).parent('div').parent('div').children('input[name=tabFavorite]:checked').val();
                                                var deferred = createFolderTreeAjax(url,data.text,itemclass);
                                                deferred.done(function(){
                                                    $.unblockUI();
                                                    location.reload();
                                                });
                                             });
                                            
                                        }catch(ex){
                                            setTimeout(function(){ inst.edit(new_node); },0);
                                        }
                                    });
                            }
                        },
                        "MoveToCart": {
                            "separator_before": true,
                            "separator_after": false,
                            "label": "リストに追加",
                            "icon": "contextmenu_movetocarticon",
                            "_disabled": isToCartDisabled || isNodata,
                            "action": function (obj) {
                                itemkey = $node.original.key;
                                var deferred = moveToCartTreeAjax(url,itemkey);
                                deferred.done(function(){
                                    $.unblockUI();
                                });                            
                            }
                        },
                    }
                }
            }
        }).on('move_node.jstree', function(e, data){
            /*移動したデータのFavoriteテーブルid*/
            movenodeKey = data.node.original.key;
            parentKey = '-1';
            if (data.parent !== '#') {
                parentKey = data.instance.get_node(data.node.parent).original.key;        
            }    
            var deferred = moveTreeAjax(url,movenodeKey,parentKey);
            deferred.done(function(){
                $.unblockUI();
            });         
        }).bind("loaded.jstree", function (event, data) {
            $("#wrapperFavoriteList a").tooltip({
                content : function() {
                    return $(this).attr('title');
                }
            });
    
        }).bind("open_node.jstree", function (event, data) { 
            $("#wrapperFavoriteList ul.jstree-children a").tooltip({
                content : function() {
                    return $(this).attr('title');
                }
            });
        });
        
    
    }
    
    
    function createFolderTreeAjax(url,foldername,itemclass){
        processing();
        var deferred = new $.Deferred();
        var isShared = false;
        if (sessionStorage.getItem('IsFavoriteSharedChecked')!==null){
            isShared = sessionStorage.getItem('IsFavoriteSharedChecked')=='true'? true : false;
        }
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url + '/createFavoriteFolder',
            type: 'GET',
            datatype: 'json',
            data : {'FolderName' : foldername, 'ItemClass' : itemclass, 'IsShared' : isShared}
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
    
    function moveToCartTreeAjax(url,itemkey){
        processing();
        var deferred = new $.Deferred();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url + '/moveToCart',
            type: 'GET',
            datatype: 'json',
            data : {'update_id' : itemkey}
        })
        // Ajaxリクエスト成功時の処理
        .done(function(data) {
            if (data['status'] !== 'OK') {
                if (data['status']=='404') {
                    alert(otheruserprocessing[selLang]);
                    location.reload();
                }
                else{
                    alert(processingFailed[selLang] + data['status']);
                }
            }
            else{
                location.reload();
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
    //'url + '/' + deletekey + ''
    //data : {'update_id' : -1, 'parent_id' : -1, 'delete_id' : deletekey, 'order_number' : -1}
    function deleteTreeAjax(url,deletekey,tree,$node){
        processing();
        var deferred = new $.Deferred();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url + '/updateFavorite',
            type: 'GET',
            datatype: 'json',
            data : {'update_id' : -1, 'parent_id' : -1, 'delete_id' : deletekey}
        })
        // Ajaxリクエスト成功時の処理
        .done(function(data) {
            if (data['status'] !== 'OK') {
                if (data['status']=='404') {
                    alert(otheruserprocessing[selLang]);
                    location.reload();
                }
                else{
                    alert(processingFailed[selLang] + data['status']);
                }
            }
            else{
                tree.delete_node($node);
            }
        })
        // Ajaxリクエスト失敗時の処理
        .fail(function(data) {
            if (data['status']=='404') {
                alert(otheruserprocessing[selLang]);
                location.reload();
            }
            else {
                alert(processingFailed[selLang] + data['status']);
            }
        })
        .always(function(data) {
            deferred.resolve();           
        });
        
        return deferred;;
    }
    
    function moveTreeAjax(url,movenodeKey,parentKey){
        processing();
        var deferred = new $.Deferred();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url + '/updateFavorite',
            type: 'GET',
            datatype: 'json',
            data : {'update_id' : movenodeKey, 'parent_id' : parentKey, 'delete_id' : -1}
        })
        // Ajaxリクエスト成功時の処理
        .done(function(data) {
            if (data['status'] !== 'OK') {
                if (data['status']=='404') {
                    alert(otheruserprocessing[selLang]);
                    location.reload();
                }
                else{
                    alert(processingFailed[selLang] + data['status']);
                }
            }
        })
        // Ajaxリクエスト失敗時の処理
        .fail(function(data) {
            if (data['status']=='404') {
                alert(otheruserprocessing[selLang]);
                location.reload();
            }
            else {
                alert(processingFailed[selLang] + data['status']);
            }
        })
        .always(function(data) {
            deferred.resolve();           
        });
        
        return deferred;
    
    }
    
    /*フォルダ作成ボタン*/
    $("#btnFolderAdd").click(function() {
        $("#modal-folderadd").modal('show');
    });
    
    /*フォルダクリアボタン*/
    $("#btnFolderClear").click(function() {
        $("#FolderName").val("");
    });

    
    
})

function getDate(element) {
    var date;
    try {
        date = $.datepicker.parseDate("yy/mm/dd", element.value);
    } catch (error) {
        date = null;
    }
    return date;
}

function getToday(splitchar) {
    var today = new Date();
    return today.getFullYear() + splitchar + 
        ( "0"+( today.getMonth()+1 ) ).slice(-2) + splitchar +
        ( "0"+today.getDate() ).slice(-2);
}
function getAddMonth(splitchar,addmonth) {
    var today = new Date();
    today.setMonth(today.getMonth() + addmonth);
    today.setDate(today.getDate() -1);
    return today.getFullYear() + splitchar + 
        ( "0"+( today.getMonth()+1 ) ).slice(-2) + splitchar +
        ( "0"+today.getDate() ).slice(-2);
}

function loadingFinish() {
    $(".loading").fadeOut();
}
function loadingStart() {
    setTimeout('loadingFinish()',500);
}
function processing()
{
	$.blockUI({
		message: '<div class="loadingtext"><div class="ball-grid-pulse"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>loading...</div>',
	    fadeIn: 200,
	    fadeOut: 0,
	    overlayCSS:  {
	         backgroundColor: '#666666',
	         opacity: 0.6,
	         cursor: 'wait'
	    },
	    css: {
	        padding: '5px 5px 5px 15px',
	        margin: 0,
	        height: '60px',
	        width: '200px',
	        border: '1px solid #aaa',
            color: '#666666',
            fontSize: '16px',
			textAlign:'center',
			boxShadow: '10px 10px 10px 10px rgba(0,0,0,0.8)'
	    }
	});
}

function setLogout() {
    sessionStorage.setItem('IsFavoriteSharedChecked',false);
    sessionStorage.removeItem('selectedNavMenu');
}

function onlyNumber(e){
    var ret = true;
    var st = String.fromCharCode(e.which);
    if ("0123456789".indexOf(st,0) < 0) {
        ret = false;
    }
    return ret;
}
var selLang = $("input[name=rdoLanguage]:checked").val()=="en" ? 1 : 0;

var requireQuantity = ['数量は必須です','The Quantity field is required.'];
var numericQuantity = ['数量は「数字」のみ有効です','The Quantity must be a number.'];
var maxAmountQuantity = ['数量は999以下のみ有効です','The Quantity may not be greater than 999.'];
var minAmountQuantity = ['数量は1以上のみ有効です','The Quantity must be at least 1.'];
var requireRemark = ['備考は必須です','The remark field is required.'];
var maxRemark = ['備考は100文字以下のみ有効です','The Remark may not be greater than 100 characters.'];
var requireExcutionDate = ['執行日は必須です','The Excution date field is required.'];
var requireExcutionAmount = ['執行額は必須です','The Excution amount field is required.'];
var numericExcutionAmount = ['執行額は「数字」のみ有効です','The Excution amount must be a number.'];
var maxAmountExcutionAmount = ['執行額は99,999,999以下のみ有効です','The Excution amount may not be greater than 99,999,999.'];
var requireItemClass = ['「試薬」か「物品」を選択して下さい','Please select [Reagent] or [Article].'];
var requireItemName = ['商品名は必須です','The Item name field is required.'];
var maxlengthItemName = ['商品名は50文字以下のみ有効です','The Item name may not be greater than 50 characters.'];
var requireMaker = ['メーカーは必須です','The Maker field is required.'];
var requirePrioritySupplier = ['優先する発注先は必須です','The Priority supllier field is required.'];
var requireUnitPrice = ['単価は必須です','The Unit price field is required.'];
var numericUnitPrice = ['単価は「数字」のみ有効です','The Unit price must be a number.'];
var maxAmountUnitPrice = ['単価は99,999,999以下のみ有効です','The Unit price may not be greater than 99,999,999.'];
var requireTitle= ['タイトルは必須です','The Title field is required.'];
var maxTitle = ['タイトルは50文字以下のみ有効です','The Title may not be greater than 50 characters.'];
var requireContents= ['内容は必須です','The Contents field is required.'];
var maxContents = ['内容は500文字以下のみ有効です','The Contents may not be greater than 500 characters.'];
var requireLimitDate= ['表示期限は必須です','The Limit date field is required.'];
var betweenNumber = ['{0} ～ {1} の数値が有効です','The Number must be between {0} and {1}.'];
var processingFailed = ['処理に失敗しました','Processing failed.'];
var otheruserprocessing = ['別ユーザーにより更新された可能性があります。操作をやり直してください','It may have been updated by another user. Please try again.'];

var pleaseSelect = ['納品対象を選択してください','Please select the delivery target.'];
var confirmRegist = ['納品処理を行いますか？【対象：{0}件】','Do you register?【target:{0} case(s)】'];
var pleaseSelect = ['登録対象を選択してください','Please select the items(s) registration.'];
var confirmRegist = ['登録しますか？【対象：{0}件】','Do you register?【target:{0} case(s)】'];
var confirmSave = ['登録しますか？','Do you register?'];
var confirmDelete = ['削除しますか？','Do you delete it?'];
var confirmReorder = ['同じ商品が既に発注依頼されています。登録しますか？','Already the item was order-requested. Do you register?'];

var beforeOriginalDate = ['現在入力されている日付({0})以前の日付を選択して下さい','Please specify before the original date ({0})']; 
var rangOfTheDate = ['日付の範囲を選択してください','Please specify the rang of the date']; 

