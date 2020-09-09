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
        console.log("cartid:" + cartid + " ordernumber:" + ordernumber);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'SearchPage/' + cartid + '',
            type: 'GET',
            datatype: 'json',
            data : {'update_id' : cartid, 'parent_id' : -1, 'delete_id' : -1, 'order_number' : ordernumber}
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
        });
    });

    var jdataReagent = $.parseJSON($("#hidFavoriteTreeReagent").val());

    $('#favoriteTreeReagent').jstree({
        "core":{
            "data":jdataReagent,
            "check_callback":function(operation,node,node_parent,node_position,more){
                if (operation=="move_node"){
                    if(node_parent.icon != "jstree-folder" && node_parent.id != "#"){
                        return false;
                    }
                }
            }
        },
        "plugins":["contextmenu", "dnd"],
        "contextmenu":{
            "items": function($node) {
                var tree = $('#favoriteTreeReagent').jstree(true);
                return {
                    "Delete": {
                        "separator_before": false,
                        "separator_after": false,
                        "label": "削除",
                        "icon": "contextmenu_deleteicon",
                        "action": function (obj) {
                            if (confirm('削除しますか？')){
                                deletekey = $node.original.key;
                                $.ajax({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    url: 'SearchPage/' + deletekey + '',
                                    type: 'GET',
                                    datatype: 'json',
                                    data : {'update_id' : -1, 'parent_id' : -1, 'delete_id' : deletekey, 'order_number' : -1}
                                })
                                // Ajaxリクエスト成功時の処理
                                .done(function(data) {
                                    if (data['status'] !== 'OK') {
                                        alert('データ削除に失敗しました');
                                    }
                                    else{
                                        tree.delete_node($node);
                                    }
                                })
                                // Ajaxリクエスト失敗時の処理
                                .fail(function(data) {
                                    alert('データ削除に失敗しました');
                                });
                            }
                        }
                    }
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
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'SearchPage/' + movenodeKey + '',
            type: 'GET',
            datatype: 'json',
            data : {'update_id' : movenodeKey, 'parent_id' : parentKey, 'delete_id' : -1, 'order_number' : -1}
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
        });
    });



    var jdataArticle = $.parseJSON($("#hidFavoriteTreeArticle").val());

    $('#favoriteTreeArticle').jstree({
        "core":{
            "data":jdataArticle,
            "check_callback":function(operation,node,node_parent,node_position,more){
                if (operation=="move_node"){
                    if(node_parent.icon != "jstree-folder" && node_parent.id != "#"){
                        return false;
                    }
                }
            }
        },
        "plugins":["contextmenu", "dnd"],
        "contextmenu":{
            "items": function($node) {
                var tree = $('#favoriteTreeArticle').jstree(true);
                return {
                    "Delete": {
                        "separator_before": false,
                        "separator_after": false,
                        "label": "削除",
                        "icon": "contextmenu_deleteicon",
                        "action": function (obj) {
                            if (confirm('削除しますか？')){
                                deletekey = $node.original.key;
                                $.ajax({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    url: 'SearchPage/' + deletekey + '',
                                    type: 'GET',
                                    datatype: 'json',
                                    data : {'update_id' : -1, 'parent_id' : -1, 'delete_id' : deletekey, 'order_number' : -1}
                                })
                                // Ajaxリクエスト成功時の処理
                                .done(function(data) {
                                    if (data['status'] !== 'OK') {
                                        alert('データ削除に失敗しました');
                                    }
                                    else{
                                        tree.delete_node($node);
                                    }
                                })
                                // Ajaxリクエスト失敗時の処理
                                .fail(function(data) {
                                    alert('データ削除に失敗しました');
                                });
                            }
                        }
                    }
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
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'SearchPage/' + movenodeKey + '',
            type: 'GET',
            datatype: 'json',
            data : {'update_id' : movenodeKey, 'parent_id' : parentKey, 'delete_id' : -1, 'order_number' : -1}
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
        });
    });


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

    /*フォルダ作成ボタン*/
    $("#btnFolderAdd").click(function() {
        $("#modal-folderadd").modal('show');
    });

    /*フォルダクリアボタン*/
    $("#btnFolderClear").click(function() {
        $("#FolderName").val("");
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

    $("#chkShared").click(function() {
        $(".searchConditionForm").submit();
    });

})