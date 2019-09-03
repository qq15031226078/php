<?php
/**
 * Created by PhpStorm.
 * User: JasonLee
 * Date: 2018/11/11
 * Time: 22:46
 */
require('UploadFile.php');

$upload = new UploadFile('imooc_pic');
$upload->setDestinationDir('./uploads');
$upload->setAllowMime(['image/jpeg', 'image/gif']);
$upload->setAllowExt(['gif', 'jpeg']);
$upload->setAllowSize(2*1024*1024);
if ($upload->upload()) {
    var_dump($upload->getFileName());
    var_dump($upload->getDestinationDir());
    var_dump($upload->getExtension());
    var_dump($upload->getFileSize());
} else {
    var_dump($upload->getErrors());
}