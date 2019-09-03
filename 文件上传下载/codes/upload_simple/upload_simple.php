<?php
/**
 * Created by PhpStorm.
 * User: JasonLee
 * Date: 2018/11/10
 * Time: 15:46
 */

var_dump($_FILES);

$tmpName = $_FILES['imooc_pic']['tmp_name'];

# rename($tmpName, './imooc.jpeg');

# move_uploaded_file($tmpName, './imooc2.jpeg');

if (is_uploaded_file($tmpName)) {
    move_uploaded_file($tmpName, './imooc3.jpeg');
}



















