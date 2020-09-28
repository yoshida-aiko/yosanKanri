<h6 class="h6-title" >お気に入り</h6>
        <div id="wrapperFavoriteList">
        <!--<input type="button" value="&#xf07b;" id="btnFolderAdd" name="btnFolderAdd" class="fa btn-folderadd-icon" title="フォルダ作成" >-->
        <label for="chkShared" ><input type="checkbox" id="chkShared" name="chkShared[]" value="共用">共用</label>
        <input type="radio" name="tabFavorite" id="tabFavoriteReagent" value="1" >
            <label class="tabLabel" for="tabFavoriteReagent">試薬</label>
            <div class="tabContent">
                <div id="favoriteTreeReagent" class="sectionOrderRequest">
                <ul>
                </ul>
                </div>
            </div>

            <input type="radio" name="tabFavorite" id="tabFavoriteArticle" value="2" >
            <label class="tabLabel" for="tabFavoriteArticle">物品</label>
            <div class="tabContent">
                <div id="favoriteTreeArticle" class="sectionOrderRequest">
                    <ul>
                    </ul>
                </div>
            </div>
        </div>
        
        <input type="hidden" id="hidFavoriteTreeReagent" value={{$jsonFavoriteTreeReagent}} >
        <input type="hidden" id="hidFavoriteTreeArticle" value={{$jsonFavoriteTreeArticle}} >

        <!--<div id="modal-folderadd" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
        <div  class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="Modal">フォルダの作成</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route($url, 0) }}">   
                <div class="modal-body">
                    <section>
                        @csrf
                        @method('PUT')
                        <div class="form-group" style="margin-top:10px;">
                            <label for="RegistDate" style="margin-left:5px !important;">フォルダ名</label>
                            <input type="text" id="FolderName" name="FolderName" value="{{old('FolderName')}}" required="required">
                        </div>
                        {{-- エラーメッセージ --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            </div>
                            <?php
                            $favoriteFolder->FolderName = old('FolderName');
                            $favoriteFolder->UseAuth = old('UserAuth');
                            ?>
                        @endif
                    </section>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_folder_save" name="submit_folder_save" class="btn btn-primary" value="保存" />
                    <input type="button" id="btnFolderClear" class="btn btn-secondary" value="クリア">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
               </div>
               <input type="hidden" value="" id="tabSelectFolder" name="tabSelectFolder" value="{{$itemClass}}">
               </form>
            </div>
        </div>
        </div>-->
