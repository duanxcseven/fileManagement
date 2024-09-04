<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}?> 

<?php
if (isset($_GET['path'])) {
    $path = $_GET['path'];

    // 获取当前目录路径
    $current_dir = dirname($path);

    // 如果是文件，删除文件
    if (is_file($path)) {
        unlink($path);
        $message = "success&msg=" . urlencode("文件删除成功： " . basename($path));
    }
    // 如果是目录，递归删除目录
    elseif (is_dir($path)) {
        function deleteDir($dirPath) {
            if (!is_dir($dirPath)) {
                return false;
            }
            $items = scandir($dirPath);
            foreach ($items as $item) {
                if ($item == '.' || $item == '..') {
                    continue;
                }
                $itemPath = $dirPath . '/' . $item;
                if (is_dir($itemPath)) {
                    deleteDir($itemPath);
                } else {
                    unlink($itemPath);
                }
            }
            rmdir($dirPath);
            return true;
        }

        deleteDir($path);
        $message = "success&msg=" . urlencode("目录删除成功： " . basename($path));
    } else {
        $message = "error&msg=" . urlencode("无法删除： " . basename($path));
    }
} else {
    $message = "error&msg=" . urlencode("路径无效");
}

// 检查是否有current_dir，带上current_dir重定向回去
$redirect_url = './';
if ($current_dir !== __DIR__) {
    $redirect_url .= 'clouddisk.php?dir=' . urlencode($current_dir) . '&status=' . $message;
} else {
    $redirect_url .= 'clouddisk.php?status=' . $message;
}

header('Location: ' . $redirect_url);
exit;
?>
