<h6 class="h6-title-favorite" >{{ __('screenwords.favorite') }}</h6>
<div id="wrapperFavoriteList">
<!--<input type="button" value="&#xf07b;" id="btnFolderAdd" name="btnFolderAdd" class="fa btn-folderadd-icon" title="フォルダ作成" >-->
<label for="chkShared" ><input type="checkbox" id="chkShared" name="chkShared[]" value="{{ __('screenwords.shared') }}">{{ __('screenwords.shared') }}</label>
<input type="radio" name="tabFavorite" id="tabFavoriteReagent" value="{{config('const.ItemClass.reagent')}}" >
    <label class="tabLabel" for="tabFavoriteReagent">{{ __('screenwords.reagent') }}</label>
    <div class="tabContent">
        <div id="favoriteTreeReagent" class="sectionOrderRequest">
        <ul>
        </ul>
        </div>
    </div>

    <input type="radio" name="tabFavorite" id="tabFavoriteArticle" value="{{config('const.ItemClass.article')}}" >
    <label class="tabLabel" for="tabFavoriteArticle">{{ __('screenwords.article') }}</label>
    <div class="tabContent">
        <div id="favoriteTreeArticle" class="sectionOrderRequest">
            <ul>
            </ul>
        </div>
    </div>
</div>
        

