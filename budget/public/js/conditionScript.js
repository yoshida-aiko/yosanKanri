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

    // メールアカウントとパスワードを使用するの選択（SMTPAuthFlag）必須マーク可視・不可視
    $('#SMTPAuthFlag').click(function() {
        if ( $(this).prop('checked')) {
            //チェックされている場合  
            $("#lblSMTPAccount").addClass('required');
            $("#lblSMTPPassword").addClass('required');
        } else {
            //チェックされていない場合
            $('#lblSMTPAccount').removeClass('required');
            $('#lblSMTPPassword').removeClass('required');
        }
    });
})