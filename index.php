<?php
// login.php
session_start(); // 开启会话

// 假设这是正确的密码
$correct_password = "123456";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 从表单获取输入的密码
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // 验证密码
    if ($password == $correct_password) {
        // 密码正确，将用户标记为已登录，并重定向到受保护的页面
        $_SESSION['logged_in'] = true;
        header('Location: clouddisk.php');
        exit();
    } else {
        // 密码错误，显示错误消息
        $error = "密码错误，请重试。";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="password"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            background-color: #4169E1;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0000FF;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<body style="padding-top: 20px; background: url(http://tc.rf.gd/background.jpg) no-repeat center center fixed; background-size: cover;">
    <div class="login-container">
        <h2>登录页面</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="index.php" method="post">
            <input type="password" name="password" placeholder="Enter password" required>
            <button type="submit">登录</button>
        </form>
    </div>
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