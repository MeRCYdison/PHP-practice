<?php
header('Content-Type: application/json');

// 模拟从数据库获取的轮播图数据
$carouselImages = [
    ['url' => 'image1.jpg'],
    ['url' => 'image2.jpg'],
    ['url' => 'image3.jpg']
    // 可以添加更多图片路径
];

echo json_encode($carouselImages);
?>
