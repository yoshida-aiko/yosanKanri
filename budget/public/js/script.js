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
            if ((obj.tagName.toUpperCase() == "INPUT" && obj.type.toUpperCase() == "TEXT")
                || obj.tagName.toUpperCase() == "TEXTAREA") {
                // 入力できる場合は制御しない
                if (!obj.readOnly && !obj.disabled) {
                    return true;
                }
            }
            return false;
        }
    });
    
    $(".table-fixed tr").click(function() {
        $(".table-fixed tr").removeClass("table-fixed-selectRow");
        $(".table-fixed tr").removeClass("table-fixed-nonselect");
        $(this).addClass("table-fixed-selectRow");
    });


})

function getToday(splitchar) {
    var today = new Date();
    return today.getFullYear() + splitchar + 
        ( "0"+( today.getMonth()+1 ) ).slice(-2) + splitchar +
        ( "0"+today.getDate() ).slice(-2);
}

function jsTreeCreate(jsondata,url,id) {

    $('#' + id).jstree({
        "core":{
            "data":jsondata,
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
                                    url: url + '/' + deletekey + '',
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
            url: url + '/' + movenodeKey + '',
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

}
