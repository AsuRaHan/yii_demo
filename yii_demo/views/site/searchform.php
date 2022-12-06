<form method="get" action="<?= \yii\helpers\Url::to(['site/search']); ?>" class="pull-right">
    <div class="input-group">
        <input type="text" name="query" class="form-control" placeholder="Поиск по каталогу" value="<?=$query?>">
        <div class="input-group-btn">
            <button class="btn btn-success" type="submit">
                Search
            </button>
        </div>
    </div>
</form>
