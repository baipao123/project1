<style>
    img.img{
        max-width:60px;
    }
</style>

<table class="layui-table">
    <thead>
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>Logo</th>
        <th>封面</th>
        <th>简介</th>
        <th>审核文件</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php /* @var $records \common\models\CompanyRecord[] */ ?>
    <?php foreach ($records as $record): ?>
        <tr>
            <td><?= $record->id ?></td>
            <td><?= $record->name ?></td>
            <td class="img1"><img class="img" src="<?= $record->icon() ?>"/></td>
            <td class="img2"><img class="img" src="<?= $record->cover() ?>"/></td>
            <td><?= $record->description ?></td>
            <td class="img3">
                <?php foreach ($record->attaches as $attach): ?>
                    <img src="<?= $attach->cover() ?>" class="img">
                <?php endforeach; ?>
            </td>
            <td>
                <span class="layui-btn layui-btn-sm layui-btn-normal" onclick="layerConfirmUrl('/company/verify?id=<?=$record->id?>&type=1','确定通过？')">通过</span>
                <span class="layui-btn layui-btn-sm layui-btn-primary" onclick="layerConfirmUrl('/company/verify?id=<?=$record->id?>&type=2')">不通过</span>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php echo \layuiAdm\widgets\PagesWidget::widget(["pagination" => $pagination]); ?>

<script>
    $(".img").click(function (e) {
        var classTxt = $(this).parent().eq(0).attr("class");
        globalLayer.photos({
            photos: "." + classTxt
        })
    })
</script>