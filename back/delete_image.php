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

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // 获取需要删除的图片记录及其文件路径
    $sql = "SELECT id, url FROM images WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imagePath = "../" . $row['url']; // 构造文件路径

        // 删除文件
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // 删除数据库中的行
        $conn->query("DELETE FROM images WHERE id = $id");

        // 获取剩余图片按ID排序
        $remainingImages = [];
        $result = $conn->query("SELECT id, url FROM images ORDER BY id ASC");
        while ($row = $result->fetch_assoc()) {
            $remainingImages[] = $row;
        }

        // 重构文件名和数据库路径
        $newId = 1;
        foreach ($remainingImages as $image) {
            $oldImagePath = "../" . $image['url'];
            $newImagePath = "../profile/image$newId.png";

            // 如果文件路径需要调整，重命名文件
            if ($oldImagePath !== $newImagePath) {
                if (file_exists($oldImagePath)) {
                    rename($oldImagePath, $newImagePath);
                }
                // 更新数据库路径
                $relativePath = "profile/image$newId.png";
                $conn->query("UPDATE images SET id = $newId, url = '$relativePath' WHERE id = " . $image['id']);
            } else {
                // 如果文件不需要重命名，仍需要更新 ID
                $conn->query("UPDATE images SET id = $newId WHERE id = " . $image['id']);
            }

            $newId++;
        }

        echo json_encode(["message" => "图片删除成功并重构文件名"]);
    } else {
        echo json_encode(["error" => "图片ID不存在"]);
    }
} else {
    echo json_encode(["error" => "未提供有效的图片ID"]);
}

$conn->close();
?>
