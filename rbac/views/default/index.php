<h1>Welcome to the RBAC Module</h1>
<?= \yii\bootstrap\Nav::widget([
    'items' => [
        ['label' => 'Assignment', 'url' => ['auth-assignment/index']],
        ['label' => 'Items', 'url' => ['auth-item/index']],
        ['label' => 'Item Children', 'url' => ['auth-item-child/index']],
        //['label' => 'Rules', 'url' => ['auth-rule/index']],
        ['label' => 'Permissions Table', 'url' => ['table']],
    ],
]) ?>
