jQuery (function ()
{
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

})