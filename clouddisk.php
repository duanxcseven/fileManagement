<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}?> 

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>简易文件系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <link rel="shortcut icon" href="http://tc.rf.gd/logo.png" type="image/x-icon"/>
    <!-- 核心 CSS 文件 -->
    <link rel="stylesheet" href="https://cdn.bootcss.com/mdui/0.4.3/css/mdui.min.css"/>
    <!-- 核心 JS 文件 -->
    <script src="https://cdn.bootcss.com/mdui/0.4.3/js/mdui.min.js"></script>
</head>
<style>
    .mdui-list-item-content {
        margin-left: 16px;
    }

    .button-group {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .mdui-dialog .mdui-textfield {
        margin: 15px 0;
    }

    .mdui-dialog .mdui-dialog-content {
        padding: 20px;
    }

    .delete-icon {
        color: red;
        cursor: pointer;
        font-size: 24px;
        transition: transform 0.3s;
    }

    .delete-icon:hover {
        transform: scale(1.2);
    }

    .file-action {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
<body style="padding-top: 20px; background: url(http://tc.rf.gd/background.jpg) no-repeat center center fixed; background-size: cover;">
    <div class="mdui-container mdui-shadow-5" style="background: rgba(255, 255, 255, 0.8);border-radius: 10px;">
        <div class="container" style="padding:15px">
           
            <!-- 显示提示消息 -->
            <?php if (isset($_GET['status'])): ?>
                <script>
                    mdui.snackbar({
                        message: "<?php echo htmlspecialchars(urldecode($_GET['msg'])); ?>",
                        timeout: 4000,
                        position: 'top'
                    });
                </script>
            <?php endif; ?>

            <div class="button-group">
                <!-- 文件上传按钮 -->
                <button class="mdui-btn mdui-btn-raised mdui-color-theme mdui-ripple" mdui-dialog="{target: '#upload-dialog'}">
                    <i class="mdui-icon material-icons">&#xe2c6;</i> 上传文件
                </button>

                <!-- 新建目录按钮 -->
                <button class="mdui-btn mdui-btn-raised mdui-color-theme mdui-ripple" mdui-dialog="{target: '#mkdir-dialog'}">
                    <i class="mdui-icon material-icons">&#xe2c7;</i> 新建目录
                </button>
            </div>

                <!-- 上传文件的对话框 -->
                <div class="mdui-dialog" id="upload-dialog">
                    <div class="mdui-dialog-title">上传文件</div>
                    <div class="mdui-dialog-content">
                        <form id="upload-form" enctype="multipart/form-data">
                            <div class="mdui-textfield mdui-textfield-floating-label">
                                <label class="mdui-textfield-label"></label>
                                <input type="file" name="fileToUpload" class="mdui-textfield-input"/>
                            </div>
                            <input type="hidden" name="current_dir" value="<?php echo isset($_REQUEST['dir']) ? $_REQUEST['dir'] : __DIR__; ?>" />

                            <!-- 显示系统上传限制 -->
                            <div class="mdui-typo">
                                <p>上传文件最大大小：<?php echo ini_get('upload_max_filesize'); ?></p>
                            </div>

                        </form>
                        <div class="mdui-progress">
                            <div class="mdui-progress-determinate" id="upload-progress" style="width: 0%;"></div>
                        </div>
                    </div>
                    <div class="mdui-dialog-actions">
                        <button class="mdui-btn mdui-ripple" mdui-dialog-close>取消</button>
                        <button class="mdui-btn mdui-color-theme mdui-ripple" onclick="uploadFile()">上传</button>
                    </div>
                </div>

                <!-- JavaScript 上传文件和显示进度条 -->
                <script>
                    function uploadFile() {
                        var form = document.getElementById('upload-form');
                        var formData = new FormData(form);
                        var xhr = new XMLHttpRequest();

                        xhr.open('POST', 'upload.php', true);

                        // 上传进度事件监听器
                        xhr.upload.onprogress = function (event) {
                            if (event.lengthComputable) {
                                var percentComplete = (event.loaded / event.total) * 100;
                                document.getElementById('upload-progress').style.width = percentComplete + '%';
                            }
                        };

                        // 上传完成后
                        xhr.onload = function () {
                            if (xhr.status === 200) {
                                mdui.snackbar({ message: '文件上传成功！', timeout: 2000 });
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                mdui.snackbar({ message: '文件上传失败。', timeout: 2000 });
                            }
                        };

                        // 发送文件数据
                        xhr.send(formData);
                    }
                </script>

            <!-- 新建目录弹窗 -->
            <div class="mdui-dialog" id="mkdir-dialog">
                <div class="mdui-dialog-title">新建目录</div>
                <div class="mdui-dialog-content">
                    <!-- 新建目录表单 -->
                    <form action="mkdir.php" method="post" style="display: flex; align-items: center; gap: 10px;">
                        <input type="hidden" name="current_dir" value="<?php echo isset($_GET['dir']) ? $_GET['dir'] : __DIR__; ?>" />
                        <input type="text" name="dirName" placeholder="输入新建目录名称" class="mdui-textfield-input" style="flex: 1;" />
                    </form>

                </div>
                <div class="mdui-dialog-actions">
                    <button class="mdui-btn mdui-ripple" mdui-dialog-close>取消</button>
                    <button class="mdui-btn mdui-color-theme mdui-ripple" onclick="document.querySelector('#mkdir-dialog form').submit();">创建</button>
                </div>
            </div>

            <!-- 删除确认弹窗 -->
            <div class="mdui-dialog" id="delete-dialog">
                <div class="mdui-dialog-title">确认删除</div>
                <div class="mdui-dialog-content">
                    <p id="delete-message">确定要删除这个文件/文件夹吗？</p>
                </div>
                <div class="mdui-dialog-actions">
                    <button class="mdui-btn mdui-ripple" mdui-dialog-close>取消</button>
                    <button class="mdui-btn mdui-color-red mdui-ripple" id="confirm-delete-btn">删除</button>
                </div>
            </div>

            <ul class="mdui-list">
                <li class='mdui-list-item mdui-ripple'>
                    <i class='mdui-list-item-avatar mdui-icon material-icons mdui-color-blue mdui-text-color-white'>home</i>
                    <div class='mdui-list-item-content'><a>根目录</a></div>
                </li>
<?php
error_reporting(0);
$dir = empty($_REQUEST["dir"]) ? __DIR__ : $_REQUEST["dir"];

function getfiles($path)
{
    $excludeFiles = ['delete.php', 'download.php', 'index.php', 'mkdir.php', 'upload.php','.htaccess','clouddisk.php','bing.php','logo.png','background.jpg'];
    $file = [];
    $tmp = [];

    foreach (scandir($path) as $afile) {
        if ($afile == '.' || $afile == '..') continue; // 跳过 . 和 .. 目录

        // 如果文件在排除列表中，跳过
        if (in_array($afile, $excludeFiles)) continue;

        if (is_dir($path . '/' . $afile)) {
            $tmp['type'] = 'dir';
        } else {
            $tmp['type'] = 'file';
        }
        $tmp['dirtext'] = $path . '/' . $afile;
        $tmp['filename'] = $afile;
        $tmp['dirtext2'] = str_replace(__DIR__ . '/', '', $path . '/' . $afile);
        $file[] = $tmp;
    }
    return $file;
}


$data = getfiles($dir);
foreach ($data as $item) {
    if ($item['type'] === 'file') {
        echo "<li class='mdui-list-item mdui-ripple'>
                <div class='file-action'>
                    <i class='mdui-list-item-avatar mdui-icon material-icons mdui-color-blue mdui-text-color-white'>insert_drive_file</i>
                    <div class='mdui-list-item-content'><a href='download.php?file=".$item['dirtext']."'>".$item['filename']."</a></div>
                    <i class='material-icons delete-icon' onclick=\"showDeleteDialog('".$item['dirtext']."', '文件');\">delete</i>
                </div>
              </li>";
    } else {
        echo "<li class='mdui-list-item mdui-ripple'>
                <div class='file-action'>
                    <i class='mdui-list-item-avatar mdui-icon material-icons mdui-color-blue mdui-text-color-white'>folder</i>
                    <div class='mdui-list-item-content'><a href='?dir=".$item['dirtext2']."'>".$item['filename']."</a></div>
                    <i class='material-icons delete-icon' onclick=\"showDeleteDialog('".$item['dirtext']."', '文件夹');\">delete</i>
                </div>
              </li>";
    }
}
?>
            </ul>
        </div>
    </div>

    <!-- 确认删除逻辑 -->
    <script>
        function showDeleteDialog(path, type) {
            var dialog = new mdui.Dialog('#delete-dialog');
            document.getElementById('delete-message').textContent = '确定要删除这个' + type + '吗？';
            document.getElementById('confirm-delete-btn').onclick = function() {
                window.location.href = 'delete.php?path=' + encodeURIComponent(path);
            };
            dialog.open();
        }
    </script>
    <script>
        window.addEventListener('load', function() {
            // 检查URL是否包含status参数
            if (window.location.search.indexOf('status=') !== -1) {
                // 显示提示信息3秒后清理URL
                setTimeout(function() {
                    var url = new URL(window.location);
                    url.searchParams.delete('status');
                    url.searchParams.delete('msg');
                    window.history.replaceState(null, null, url);
                }, 1000); // 3秒后清理URL
            }
        });
    </script>
</body>
</html>
