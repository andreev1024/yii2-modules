<?php

use yii\helpers\Html;

$currentPermissions = $model['permissions'];
$currentPermissions = reset($currentPermissions);
$currentPermissions = array_keys($currentPermissions);

?>

<table id="permissions-table" class="table table-bordered">
    <tr>
        <td>Permissions</td>
        <td colspan="<?= count($allRoles) ?>" style="text-align: center">allRoles</td>
    </tr>
    <tr>
        <td></td>
        <?php foreach ($allRoles as $role): ?>
            <td>
                <?= Html::checkbox('', null, [
                    'label' => $role->name,
                    'class' => 'checker',
                    'data' => [
                        'role' => $role->name,
                        'group' => $model['groupName']
                    ]
                ]
                ) ?>

            </td>
        <?php endforeach; ?>
    </tr>
    <?php foreach ($currentPermissions as $permission): ?>
    <tr>
        <td><?= $allPermissions[$permission]->name ?>
        <p class="permission-description"><?= $allPermissions[$permission]->description ?></p>
        </td>
        <?php foreach ($allRoles as $role): ?>
            <td style="background-color: <?= empty($model['permissions'][$role->name][$permission]) ? '#E6C2C1' : '#C1E6CD' ?>">
                <?= Html::checkbox(
                    $parentModel->formName() . '[permissions][' . $role->name . '][' . $permission . ']',
                    $model['permissions'][$role->name][$permission], [
                        'label' => null,
                        'uncheck' => 0,
                        'data' => [
                            'role' => $role->name,
                            'group' => $model['groupName']
                        ]
                    ]
                ) ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
</table>