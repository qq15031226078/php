<?php
/**
 * Created by PhpStorm.
 * User: JasonLee
 * Date: 2018/11/11
 * Time: 15:12
 */

$key = 'imooc_pic';
$mimeWhiteList = ['image/jpeg', 'image/png', 'image/gif'];
$extWhiteList = ['jpeg', 'jpg', 'png', 'gif'];
$allowSize = 2*1024*1024;
$destDir = './uploads';

// 接收和处理$_FILES
if (!empty($_FILES[$key])) {
    $files = [];
    foreach($_FILES[$key]['name'] as $k => $v) {
        $files[$k]['name'] = $v;
        $files[$k]['type'] = $_FILES[$key]['type'][$k];
        $files[$k]['tmp_name'] = $_FILES[$key]['tmp_name'][$k];
        $files[$k]['error'] = $_FILES[$key]['error'][$k];
        $files[$k]['size'] = $_FILES[$key]['size'][$k];
    }
}

$errors = [];
foreach($files as $file) {
    // name type error size
    $name = $file['name']; // 源文件名称
    $type = $file['type']; // MIME 类型
    $tmpName = $file['tmp_name']; // 临时文件名称
    $error = $file['error']; // 错误信息
    $size = $file['size']; // 文件大小 字节

    // 处理错误
    // 0 - 无错误
    if ($error > UPLOAD_ERR_OK) {
        switch($error) {
            // 1 - 文件大小超出了php.ini当中的upload_max_filesize的大小
            case UPLOAD_ERR_INI_SIZE:
                $errors[$key] = $name . '文件大小超出了php.ini当中的upload_max_filesize的大小';
                continue 2;
            // 2 - 超出表单当中的MAX_FILE_SIZE的大小
            case UPLOAD_ERR_FORM_SIZE:
                $errors[$key] =  $name . '超出表单当中的MAX_FILE_SIZE的大小';
                continue 2;
            // 3 - 部分文件被上传
            case UPLOAD_ERR_PARTIAL:
                $errors[$key] = $name . '部分文件被上传';
                continue 2;
            // 4 - 没有文件被上传
            case UPLOAD_ERR_NO_FILE:
                $errors[$key] = $name . '没有文件被上传';
                continue 2;
            // 6 - 临时目录不存在
            case UPLOAD_ERR_NO_TMP_DIR:
                $errors[$key] = $name . '临时目录不存在';
                continue 2;
            // 7 - 磁盘写入失败
            case UPLOAD_ERR_CANT_WRITE:
                $errors[$key] = $name . '磁盘写入失败';
                continue 2;
            // 8 - 文件上传被PHP扩展阻止
            case UPLOAD_ERR_EXTENSION:
                $errors[$key] = $name . '文件上传被PHP扩展阻止';
                continue 2;
            default:
                $errors[$key] = $name . '未知错误';
                continue 2;
        }
    }

    // 限制MIME类型
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

    // 限制文件大小
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
