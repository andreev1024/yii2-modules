##Barcode module

This module provides a barcode in Yii2 web app.

##Configuration

Add module to backend or common config file

```
'modules' => [
    'barcode' => [
        'class' => 'andreev1024\barcode\Module',
    ],
]
```

## How to use:

In your view you can use barcode widget

```
<?= Barcode::widget([
    'text' =>'1111-1111-1111',
    'size' => 50,
    'codeType' => 'codabar',    //  code128, code39, code25, codabar
    'orientation' => 'horizontal', //   horizontal|vertical
]);
```