<?php
/**
 * Created by PhpStorm.
 * User: JasonLee
 * Date: 2018/11/11
 * Time: 14:06
 */

// 接收$_FILES数组
$key = 'imooc_pic';
$mimeWhiteList = ['image/jpeg', 'image/png', 'image/gif'];
$extWhiteList = ['jpeg', 'jpg', 'png', 'gif'];
$allowSize = 2*1024*1024;
$destDir = './uploads';

$name = $_FILES[$key]['name']; // 源文件名称
$type = $_FILES[$key]['type']; // MIME 类型
$tmpName = $_FILES[$key]['tmp_name']; // 临时文件名称
$error = $_FILES[$key]['error']; // 错误信息
$size = $_FILES[$key]['size']; // 文件大小 字节

// 处理错误
// 0 - 无错误
if ($error > UPLOAD_ERR_OK) {
    switch($error) {
        // 1 - 文件大小超出了php.ini当中的upload_max_filesize的大小
        case UPLOAD_ERR_INI_SIZE:
            exit('文件大小超出了php.ini当中的upload_max_filesize的大小');
        // 2 - 超出表单当中的MAX_FILE_SIZE的大小
        case UPLOAD_ERR_FORM_SIZE:
            exit('超出表单当中的MAX_FILE_SIZE的大小');
        // 3 - 部分文件被上传
        case UPLOAD_ERR_PARTIAL:
            exit('部分文件被上传');
        // 4 - 没有文件被上传
        case UPLOAD_ERR_NO_FILE:
            exit('没有文件被上传');
        // 6 - 临时目录不存在
        case UPLOAD_ERR_NO_TMP_DIR:
            exit('临时目录不存在');
        // 7 - 磁盘写入失败
        case UPLOAD_ERR_CANT_WRITE:
            exit('磁盘写入失败');
        // 8 - 文件上传被PHP扩展阻止
        case UPLOAD_ERR_EXTENSION:
            exit('文件上传被PHP扩展阻止');
        default:
            exit('未知错误');
    }
}

// 限制文件的MIME
if (!in_array($type, $mimeWhiteList)) {
    exit('文件类型' . $type . '不被允许!');
}

// 限制文件的扩展名
$ext = pathinfo($name, PATHINFO_EXTENSION);
if (!in_array($ext, $extWhiteList)) {
    exit('文件扩展名' . $ext . '不被允许！');
}

// 限制文件大小
if ($size > $allowSize) {
    exit('文件大小 ' . $size . ' 超出限定大小 ' . $allowSize . ' !');
}

// 生成新的随机文件名称
// md5(rand());
$fileName = uniqid() . '.' . $ext;

// 移动临时文件到指定目录当中并重新命名文件名
if (!file_exists($destDir)) {
    mkdir($destDir, 0777, true);
}
if (is_uploaded_file($tmpName) && move_uploaded_file($tmpName, $destDir . '/' . $fileName)) {
    echo "恭喜，文件上传成功";
} else {
    echo "很抱歉，文件上传失败";
}