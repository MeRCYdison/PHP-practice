<?php
// 数据库配置信息
$host = 'localhost';   // 数据库主机
$dbname = 'news';   // 数据库名
$username = 'root';    // 数据库用户名
$password = '***'; // 数据库密码

// 创建数据库连接
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4"); // 设置字符集为 utf8mb4

// 查询新闻数据
$sql = "SELECT image, title, description FROM news"; // 假设新闻表名为 `news`
$result = $conn->query($sql);

$news = array();
if ($result->num_rows > 0) {
    // 将每条新闻添加到数组中
    while ($row = $result->fetch_assoc()) {
        $news[] = array(
            "image" => $row["image"],
            "title" => $row["title"],
            "description" => $row["description"]
        );
    }
}

// 关闭数据库连接
$conn->close();

// 将新闻数据以 JSON 格式返回给前端
header('Content-Type: application/json');
echo json_encode(array("news" => $news));
?>
