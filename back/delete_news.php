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

    // 获取需要删除的新闻记录及其图片路径
    $sql = "SELECT id, image FROM news WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imagePath = "../" . $row['image']; // 构造文件路径

        // 删除文件
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // 删除数据库中的行
        $conn->query("DELETE FROM news WHERE id = $id");

        // 获取剩余新闻按ID排序
        $remainingNews = [];
        $result = $conn->query("SELECT id, image FROM news ORDER BY id ASC");
        while ($row = $result->fetch_assoc()) {
            $remainingNews[] = $row;
        }

        // 重构文件名和数据库路径
        $newId = 1;
        foreach ($remainingNews as $news) {
            $oldImagePath = "../" . $news['image'];
            $newImagePath = "../news/news$newId.png";

            // 如果文件路径需要调整，重命名文件
            if ($oldImagePath !== $newImagePath) {
                if (file_exists($oldImagePath)) {
                    rename($oldImagePath, $newImagePath);
                }
                // 更新数据库路径
                $relativePath = "news/news$newId.png";
                $conn->query("UPDATE news SET id = $newId, image = '$relativePath' WHERE id = " . $news['id']);
            } else {
                // 如果文件不需要重命名，仍需要更新 ID
                $conn->query("UPDATE news SET id = $newId WHERE id = " . $news['id']);
            }

            $newId++;
        }

        echo json_encode(["message" => "新闻删除成功并重构文件名"]);
    } else {
        echo json_encode(["error" => "新闻ID不存在"]);
    }
} else {
    echo json_encode(["error" => "未提供有效的新闻ID"]);
}

$conn->close();
?>
