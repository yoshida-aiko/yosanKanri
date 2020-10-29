<div id="modal-detail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
        <!--以下modal-dialogのCSSの部分で modal-lgやmodal-smを追加するとモーダルのサイズを変更することができる-->
        <div  class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="Modal">{{ __('screenwords.itemDetails') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('screenwords.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="modal-detail-table">
                        <tbody>
                            <tr>
                                <td>{{ __('screenwords.itemName') }}：</td><td id="detailProductName" colspan="3"></td>
                            </tr>
                            <tr>
                                <td>{{ __('screenwords.capacity') }}：</td><td id="detailAmount"></td><td>{{ __('screenwords.standard') }}：</td><td id="detailStandard"></td>
                            </tr>
                            <tr>
                                <td>{{ __('screenwords.catalogCode') }}：</td><td id="detailCatalogCode"></td><td>{{ __('screenwords.unitPrice') }}：</td><td id="detailUnitPrice"></td>
                            </tr>
                            <tr>
                                <td>{{ __('screenwords.maker') }}：</td><td id="detailMakerName"></td><td>{{ __('screenwords.prioritySupplier') }}：</td><td id="detailSupplierName"></td>
                            </tr>
                            <tr>
                                <td>{{ __('screenwords.remark') }}：</td><td id="detailRemark" colspan="3"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('screenwords.close') }}</button>
               </div>
            </div>
        </div>
    </div>