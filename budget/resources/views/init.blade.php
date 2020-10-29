@extends('layouts.app')

@section('content')
    <script >
        jQuery (function ()
        {
            $("#btnHash").click(function() {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: 'Order/passwordHash',
                    type: 'GET',
                    datatype: 'json',
                    data : {'aa' : '1'}
                })
                .done(function(data) {
                    if (data['status'] == 'NG') {
                        alert(data['errorMsg']);
                    }
                })
                .fail(function(data) {
                    alert('データ更新に失敗しました' +  alert(data['errorMsg']));

                });
            });
        })
    </script>

    <input type="button" id="btnHash" class="btn btn-primary" value="Hash">
@endsection