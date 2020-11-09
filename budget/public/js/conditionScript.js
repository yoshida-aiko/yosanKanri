jQuery (function ()
{
    // ページ読み込み時　システム名(英名) 必須マーク
    var bilingual = $('input:radio[name="bilingual"]:checked').val();
    if (bilingual == '1') {
      $("#lblSystemNameEn").addClass('required');
    }else{
      $('#lblSystemNameEn').removeClass('required');
    }
    // バイリンガル　ラジオボタン選択イベント
    $(function() {
        $('[name="bilingual"]:radio').change( function() {
          if($('[id=use]').prop('checked')){
            $("#lblSystemNameEn").addClass('required');
            $('#SystemNameEn').prop('readonly', false);
          } else if ($('[id=notUse]').prop('checked')) {
            $('#lblSystemNameEn').removeClass('required');
            $('#SystemNameEn').prop('readonly', true);
          } 
        })
      });

/*     // メールアカウントとパスワードを使用するの選択（SMTPAuthFlag）必須マーク可視・不可視
    $('#SMTPAuthFlag').click(function() {
        if ( $(this).prop('checked')) {
            //チェックされている場合  
            $("#lblSMTPAccount").addClass('required');
            $("#lblSMTPPassword").addClass('required');
            $(this).val("1");
        } else {
            //チェックされていない場合
            $('#lblSMTPAccount').removeClass('required');
            $('#lblSMTPPassword').removeClass('required');
            $(this).val("0");
        }
    }); */

    //執行基準　モード判定
    var mode = $('input:hidden[name="mode"]').val();
    if (mode != 'new') {
      $("#ExecutionBasisArea").addClass('readonly');
      $(".readonly :radio:not(:checked)").attr("disabled", true); 
    }

    /*入力エリアでのEnterkeyでsubmitされるのを防ぐ*/
    $("input").keypress(function(event) {
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){//enter);
          event.preventDefault();
      }
  });
  
  // ２秒後に登録成功のブロックを非表示 
  $(".alert-success").fadeOut( 2000 );

})