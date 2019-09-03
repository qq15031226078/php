<?php
/**
 * Created by PhpStorm.
 * User: JasonLee
 * Date: 2018/11/11
 * Time: 14:54
 */

$errors = [];
$mimeWhiteList = ['image/jpeg', 'image/png', 'image/gif'];
$extWhiteList = ['jpeg', 'jpg', 'png', 'gif'];
$allowSize = 2*1024*1024;
$destDir = './uploads';

foreach($_FILES as $key => $val) {
    // name type tmp_name error size
    // 接收$_FILES
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
                $errors[$key] = '文件大小超出了php.ini当中的upload_max_filesize的大小';
                continue 2;
            // 2 - 超出表单当中的MAX_FILE_SIZE的大小
            case UPLOAD_ERR_FORM_SIZE:
                $errors[$key] = '超出表单当中的MAX_FILE_SIZE的大小';
                continue 2;
            // 3 - 部分文件被上传
            case UPLOAD_ERR_PARTIAL:
                $errors[$key] = '部分文件被上传';
                continue 2;
            // 4 - 没有文件被上传
            case UPLOAD_ERR_NO_FILE:
                $errors[$key] = '没有文件被上传';
                continue 2;
            // 6 - 临时目录不存在
            case UPLOAD_ERR_NO_TMP_DIR:
                $errors[$key] = '临时目录不存在';
                continue 2;
            // 7 - 磁盘写入失败
            case UPLOAD_ERR_CANT_WRITE:
                $errors[$key] = '磁盘写入失败';
                continue 2;
            // 8 - 文件上传被PHP扩展阻止
            case UPLOAD_ERR_EXTENSION:
                $errors[$key] = '文件上传被PHP扩展阻止';
                continue 2;
            default:
                $errors[$key] = '未知错误';
                continue 2;
        }
    }

    // 限制MIME
    if (!in_array($type, $mimeWhiteList)) {
        $errors[$key] = '文件类型' . $type . '不被允许!';
        continue;
    }

    // 限制扩展名
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    if (!in_array($ext, $extWhiteList)) {
        $errors[$key] = '文件扩展名' . $ext . '不被允许！';
        continue;
    }

    // 限制大小
    if ($size > $allowSize) {
        $errors[$key] = '文件大小 ' . $size . ' 超出限定大小 ' . $allowSize . ' !';
        continue;
    }

    // 生成随机文件名称
    $fileName = uniqid() . '.' . $ext;

    // 移动文件
    if (!file_exists($destDir)) {
        mkdir($destDir, 0777, true);
    }
    if (!is_uploaded_file($tmpName) || !move_uploaded_file($tmpName, $destDir . '/' . $fileName)) {
        $errors[$key] = "很抱歉，文件上传失败";
        continue;
    }
}

if (count($errors) > 0) {
    var_dump($errors);
} else {
    echo "文件全部上传成功";
}