<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}?> 

<?php
if (isset($_POST['dirName'])) {
    $dirName = $_POST['dirName'];

    // 获取当前目录
    $current_dir = isset($_POST['current_dir']) ? $_POST['current_dir'] : __DIR__;
    $new_dir_path = $current_dir . '/' . $dirName;

    if (!file_exists($new_dir_path)) {
        mkdir($new_dir_path, 0777, true);
        $message = "success&msg=" . urlencode("目录创建成功： " . $dirName);
    } else {
        $message = "error&msg=" . urlencode("目录已存在： " . $dirName);
    }
} else {
    $message = "error&msg=" . urlencode("请输入目录名称");
}

// 检查是否有dir参数，带上dir重定向回去
$redirect_url = './';
if (isset($_POST['current_dir']) && $_POST['current_dir'] !== __DIR__) {
    $redirect_url .= '/clouddisk.php?dir=' . urlencode($_POST['current_dir']) . '&status=' . $message;
} else {
    $redirect_url .= '/clouddisk.php?status=' . $message;
}

header('Location: ' . $redirect_url);
exit;
?>
