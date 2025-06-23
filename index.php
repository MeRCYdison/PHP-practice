<?php
session_start();
if (!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
    header('Location: login.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>个人简历</title>
    <link rel="stylesheet" href="index.css">
    <script defer src="script.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+SC:wght@400;700&family=Roboto:wght@300;400;700&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="#personal-info">个人信息</a></li>
            <li><a href="#school-news">学校新闻</a></li>
            <li><a href="#project-images">项目图片</a></li>
            <li><a href="./admin.html">登录后台</a></li>
            <li><a id="logout">退出登录</a></li>
        </ul>
    </nav>
</header>
<div class="container">
    <section id="personal-info">
        <h1>个人信息</h1>
        <p><strong>姓名:</strong> jiashen</p>
        <p><strong>角色:</strong> 学生</p>
        <p><strong>职业方向:</strong> 网页</p>
        <p><strong>出生日期:</strong> 2024/08/14</p>
        <p><strong>大学:</strong> 中南大学</p>
        <p><strong>性别:</strong> 男</p>
        <p><strong>专业:</strong> 信息安全</p>
        <p><strong>位置:</strong> 中国</p>
        <p><strong>简介:</strong> hello123</p>
    </section>
    <section id="contact-info">
        <h1>联系方式</h1>
        <p><strong>电话:</strong> 123-456-7890</p>
        <p><strong>邮箱:</strong> example@example.com</p>
    </section>
</div>
<section id="school-news">
    <h2>学校新闻<i class="fas fa-sync-alt" id="re_news" style="cursor: pointer; margin-left: 10px;"></i></h2>
    <div class="news-grid"></div>
</section>
<section id="project-images">
    <h2>项目图片<i class="fas fa-sync-alt" id="re_images" style="cursor: pointer; margin-left: 10px;"></i></h2>
    <div class="carousel" id="carousel-container">
        <!-- 动态生成轮播图内容 -->
        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>
        <div class="carousel-indicators"></div>
    </div>
</section>



</body>
</html>
