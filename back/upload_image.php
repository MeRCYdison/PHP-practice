<?php
$host = 'localhost';
$dbname = 'profile';
$username = 'root';
$password = '***';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}
$conn->set_charset("utf8mb4");

// 检查当前轮播图图片数量是否达到上限
$result = $conn->query("SELECT COUNT(*) AS count FROM images");
$row = $result->fetch_assoc();
if ($row['count'] >= 3) {
    echo json_encode(["error" => "最多只能上传3张轮播图"]);
    $conn->close();
    exit;
}

// 获取上传的图片
$image = $_FILES['image'] ?? null;
if (!$image) {
    echo json_encode(["error" => "未选择图片"]);
    $conn->close();
    exit;
}

// 确定新图片的ID
$newId = $row['count'] + 1;
$imagePath = "../profile/image$newId.png";

// 移动上传的文件
if (move_uploaded_file($image['tmp_name'], $imagePath)) {
    // 插入数据库
    $relativePath = "profile/image$newId.png";
    $stmt = $conn->prepare("INSERT INTO images (id, url) VALUES (?, ?)");
    $stmt->bind_param("is", $newId, $relativePath);
    $stmt->execute();

    echo json_encode(["message" => "图片上传成功"]);
} else {
    echo json_encode(["error" => "图片上传失败"]);
}

$conn->close();
?>
