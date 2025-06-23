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

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // 获取当前新闻数据
    $result = $conn->query("SELECT * FROM news WHERE id = $id");
    if ($result->num_rows === 0) {
        echo json_encode(["error" => "新闻不存在"]);
        $conn->close();
        exit;
    }
    $row = $result->fetch_assoc();

    // 获取更新数据
    $title = $_POST['title'] ?? $row['title'];
    $description = $_POST['description'] ?? $row['description'];
    $image = $_FILES['image'] ?? null;

    // 处理图片更新
    $imagePath = $row['image']; // 默认保持原路径
    if ($image) {
        $newImagePath = "../news/news$id.png";
        if (move_uploaded_file($image['tmp_name'], $newImagePath)) {
            $imagePath = "news/news$id.png"; // 更新相对路径
        } else {
            echo json_encode(["error" => "图片更新失败"]);
            $conn->close();
            exit;
        }
    }

    // 更新数据库
    $stmt = $conn->prepare("UPDATE news SET title = ?, description = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $description, $imagePath, $id);
    $stmt->execute();

    echo json_encode(["message" => "新闻更新成功"]);
} else {
    echo json_encode(["error" => "未提供有效的新闻ID"]);
}

$conn->close();
?>
