<?php
/**
 * Created by PhpStorm.
 * User: JasonLee
 * Date: 2018/11/12
 * Time: 00:07
 */
//echo '<a href="./uploads/5be822d84c42a.jpeg">下载图片</a>';

// download.php?file=5be822d84c42a.jpeg

// 接收get参数
if (!isset($_GET['file'])) {
    exit('需要传递文件名称');
}

if (empty($_GET['file'])) {
    exit('请传递文件名称');
}

// 获取远程文件地址
$file = './uploads/' . $_GET['file'];

if (!file_exists($file)) {
    exit('文件不存在');
}

if (!is_file($file)) {
    exit('文件不存在');
}

if (!is_readable($file)) {
    exit('文件不可读');
}

// 清空缓冲区
ob_clean();

// 打开文件 rb
$file_handle = fopen($file, 'rb');

if (!$file_handle) {
    exit('打开文件失败');
}

// 通知浏览器
header('Content-type: application/octet-stream; charset=utf-8');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($file));
header('Content-Disposition: attachment; filename="' . urlencode(basename($file)) . '"');

// 读取并输出文件
while(!feof($file_handle)) {
    echo fread($file_handle, 10240);
}

// 关闭文档流
fclose($file_handle);