<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}?> 

<?php
// 获取上传目录路径，如果没有传递则默认为当前目录
$upload_dir = isset($_POST['current_dir']) ? $_POST['current_dir'] : __DIR__;

// 检查上传目录是否存在
if (!is_dir($upload_dir)) {
    die("上传目录不存在: " . $upload_dir);
}

// 检查是否有文件上传
if (isset($_FILES['fileToUpload'])) {
    $file = $_FILES['fileToUpload'];

    // 获取上传文件的信息
    $file_name = basename($file['name']);
    $target_file = $upload_dir . '/' . $file_name;

    // 检查是否上传成功
    if ($file['error'] === UPLOAD_ERR_OK) {
        // 移动文件到上传目录
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // 上传成功后，重定向到干净的 URL
            header('Location: ./?dir=' . urlencode($upload_dir) . '&status=success');
            exit;
        } else {
            header('Location: ./?dir=' . urlencode($upload_dir) . '&status=fail');
            exit;
        }
    } else {
        header('Location: ./?dir=' . urlencode($upload_dir) . '&status=error&code=' . $file['error']);
        exit;
    }
}
