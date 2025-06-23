<?php
session_start();

// 设置 session 和 cookie 的生命周期为 60 秒
ini_set('session.cookie_lifetime', 60);
ini_set('session.gc_maxlifetime', 60);

if (isset($_SESSION['username']) || isset($_COOKIE['username'])) {
    // 用户已登录，可以访问index.html
    header("Location: index.php");
    exit();
} else {
    // 用户未登录，跳转到登录页面
    header("Location: login.html");
    exit();
}
?>
