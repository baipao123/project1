<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/9/4
 * Time: 下午10:50
 */
use common\models\School;
/* @var $data School[]*/
?>

<fieldset class="layui-elem-field">
    <legend>检索</legend>
    <div class="layui-field-box">
        <form class="layui-form" method="get">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">学校名称</label>
                    <div class="layui-input-inline" style="width: 100px;">
                        <input type="text" name="name" autocomplete="off" class="layui-input" value="<?= $name ?>">
                    </div>
                </div>
                <div class="layui-inline" style="width: 100px;">
                    <select name="cid" lay-verify="gender">
                        <option value="0">-- 城市 --</option>
                        <?php foreach ($cities as $city):?>
                            <option value="<?=$city['id']?>" <?= $city['id']==$cid ? "selected" : ""?>><?=$city['name']?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="layui-inline" style="width: 100px;">
                    <select name="status" lay-verify="gender">
                        <option value="-1">全部状态</option>
                        <option value="1" <?= $status == 1 ? "selected" : ""?>>显示</option>
                        <option value="0" <?= $status == 0 ? "selected" : ""?>>不显示</option>
                    </select>
                </div>

                <div class="layui-inline">
                    <button class="layui-btn layui-btn-normal login-btn" lay-submit>搜索</button>
                </div>
            </div>
        </form>
    </div>
</fieldset>


<button class="layui-btn layui-btn-danger" onclick="layerOpenIFrame('/school/info','添加学校')"><i class="layui-icon">&#xe654;</i>添加学校</button>


<table class="layui-table">
    <thead>
    <tr>
        <th>ID</th>
        <th>学校名称</th>
        <th>所属城市</th>
        <th>当前状态</th>
        <th>添加时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $school):?>
            <tr>
                <td><?=$school->id?></td>
                <td><?=$school->name?></td>
                <td><?=$school->city->name?></td>
                <td>
                    <?php if ($school->status == School::ON): ?>
                        <span class="layui-btn layui-btn-sm layui-btn-normal">显示</span>
                    <?php else: ?>
                        <span class="layui-btn layui-btn-sm layui-btn-danger">不显示</span>
                    <?php endif;?>
                </td>
                <td><?=date("Y-m-d H:i:s",$school->created_at)?></td>
                <td>
                     <span class="layui-btn layui-btn-sm layui-btn-normal"
                           onclick="layerOpenIFrame('/school/info?id=<?= $school->id ?>','学校信息')">编辑</span>
                    <?php if ($school->status == School::ON): ?>
                        <span class="layui-btn layui-btn-sm layui-btn-danger"
                              onclick="layerConfirmUrl('/school/toggle?id=<?= $school->id ?>&status=<?=School::OFF?>')">不显示</span>
                    <?php else: ?>
                        <span class="layui-btn layui-btn-sm layui-btn-normal"
                              onclick="layerConfirmUrl('/school/toggle?id=<?= $school->id ?>&status=<?=School::ON?>')">显示</span>
                        <span class="layui-btn layui-btn-sm layui-btn-primary"
                              onclick="layerConfirmUrl('/school/toggle?id=<?= $school->id ?>&status=<?=School::DEL?>','确定删除学校，删除后不可恢复')">删除</span>
                    <?php endif; ?>

                </td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>

<?php echo \layuiAdm\widgets\PagesWidget::widget(["pagination" => $pagination]); ?>
