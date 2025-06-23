<?php
session_start();

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $data['username'];
    $password = $data['password'];
    $captcha = $data['captcha'];

    if ($captcha == $_SESSION['authcode']) {
        $host = 'localhost';
        $dbname = 'normaluser';
        $dbusername = 'root';
        $dbpassword = '***';

        $conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

        if ($conn->connect_error) {
            die(json_encode(['status' => 'error', 'message' => '数据库连接失败']));
        }

        $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['loggedIn'] = true;
            setcookie('loggedIn', true, time() + 3600, '/');
            echo json_encode(['status' => 'success', 'message' => '登录成功']);
        } else {
            echo json_encode(['status' => 'error', 'message' => '用户名或密码错误']);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => '验证码错误']);
    }

    $new_captcha_code = '';
    for ($i = 0; $i < 4; $i++) {
        $new_captcha_code .= rand(0, 9);
    }
    $_SESSION['authcode'] = $new_captcha_code;
}
?>

