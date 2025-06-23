<?php
// 数据库配置信息

$host = 'localhost';   // 数据库主机
$dbname = 'profile';    // 数据库名
$username = "root";    // 数据库用户名
$password = '***';        // 数据库密码
// 创建数据库连接
$conn = new mysqli($host,$username, $password, $dbname);

// 检查连接是否成功
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 查询图片 URL
$sql = "SELECT url FROM images";
$result = $conn->query($sql);

$images = array();
if ($result->num_rows > 0) {
    // 将每个 URL 添加到数组中
    while($row = $result->fetch_assoc()) {
        $images[] = $row["url"];
    }
}

// 关闭数据库连接
$conn->close();

// 将图片 URL 以 JSON 格式返回给前端
header('Content-Type: application/json');
echo json_encode(array("images" => $images));
?>
