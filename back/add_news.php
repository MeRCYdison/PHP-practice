<?php
$host = 'localhost';
$dbname = 'news';
$username = 'root';
$password = '***';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}
$conn->set_charset("utf8mb4");

// 检查当前新闻条数是否达到上限
$result = $conn->query("SELECT COUNT(*) AS count FROM news");
$row = $result->fetch_assoc();
if ($row['count'] >= 4) {
    echo json_encode(["error" => "最多只能添加4条新闻"]);
    $conn->close();
    exit;
}

// 获取上传数据
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$image = $_FILES['image'] ?? null;

if (empty($title) || empty($description) || !$image) {
    echo json_encode(["error" => "标题、描述和图片不能为空"]);
    $conn->close();
    exit;
}

// 确定新新闻的ID
$newId = $row['count'] + 1;
$imagePath = "../news/news$newId.png";

// 移动上传的文件
if (move_uploaded_file($image['tmp_name'], $imagePath)) {
    // 插入数据库
    $relativePath = "news/news$newId.png";
    $stmt = $conn->prepare("INSERT INTO news (id, title, description, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $newId, $title, $description, $relativePath);
    $stmt->execute();

    echo json_encode(["message" => "新闻添加成功"]);
} else {
    echo json_encode(["error" => "图片上传失败"]);
}

$conn->close();
?>
