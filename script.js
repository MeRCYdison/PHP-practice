
document.addEventListener('DOMContentLoaded', () => {

    const logoutBtn = document.getElementById('logout');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            fetch('/back/logout.php', { method: 'POST' })
                .then(response => {
                    if (response.ok) {
                        window.location.href = 'login.html';
                    } else {
                        console.error('Logout failed!');
                    }
                });
        });
    } else {
        console.error('Logout button not found!');
    }

    const renews = document.getElementById('re_news');
    if (renews) {
        renews.addEventListener('click', () => {
            loadNews();
        });
    } else {
        console.error('refresh news button not found!');
    }

    const reimages = document.getElementById('re_images');
    if (reimages) {
        reimages.addEventListener('click', () => {
            loadNews();
        });
    } else {
        console.error('refresh images button not found!');
    }

    loadNews();
    loadImages();
    showSlides(slideIndex);

});

let slideIndex = 1;
let autoSlideTimeout;

function loadImages() {
    fetch('./back/images.php')
        .then(response => response.json())
        .then(data => {
            const images = data.images;
            const carouselContainer = document.getElementById('carousel-container');
            const indicators = carouselContainer.querySelector('.carousel-indicators');

            // 清空旧的内容
            carouselContainer.querySelectorAll('.carousel-item').forEach(item => item.remove());
            indicators.innerHTML = '';

            images.forEach((image, index) => {
                // 创建轮播图项
                const item = document.createElement('div');
                item.className = 'carousel-item';
                if (index === 0) item.style.display = 'block'; // 默认显示第一张

                const img = document.createElement('img');
                img.src = image;
                img.alt = `项目图片${index + 1}`;
                item.appendChild(img);
                carouselContainer.appendChild(item);

                // 创建指示器
                const dot = document.createElement('span');
                dot.className = 'dot';
                dot.onclick = () => currentSlide(index + 1);
                indicators.appendChild(dot);
            });

            slideIndex = 1; // 重置轮播图索引
            showSlides(slideIndex); // 更新轮播图显示
        })
        .catch(error => console.error('Error loading images:', error));
}


function showSlides(n) {
    let i;
    const slides = document.getElementsByClassName('carousel-item');
    const dots = document.getElementsByClassName('dot');
    if (n > slides.length) {
        slideIndex = 1;
    }
    if (n < 1) {
        slideIndex = slides.length;
    }
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = 'none';
    }
    for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(' active', '');
    }
    slides[slideIndex - 1].style.display = 'block';
    dots[slideIndex - 1].className += ' active';

    clearTimeout(autoSlideTimeout);
    autoSlideTimeout = setTimeout(() => plusSlides(1), 3000); // 每3秒切换一次图片
}

function plusSlides(n) {
    showSlides(slideIndex += n);
}

function currentSlide(n) {
    showSlides(slideIndex = n);
}

function updateIndicators(images) {  // 接收images参数
    const indicators = document.querySelector('.carousel-indicators');
    indicators.innerHTML = '';
    for (let i = 0; i < images.length; i++) {
        const dot = document.createElement('span');
        dot.className = 'dot';
        dot.onclick = () => currentSlide(i + 1);
        indicators.appendChild(dot);
    }
}

function loadNews() {
    fetch('./back/news.php') // 发送请求到 news.php
        .then(response => response.json()) // 解析返回的 JSON 数据
        .then(data => {
            const newsGrid = document.querySelector('.news-grid');
            newsGrid.innerHTML = ''; // 清空现有内容
            data.news.forEach(newsItem => { // 遍历返回的新闻数组
                const item = document.createElement('div');
                item.className = 'news-item';
                item.innerHTML = `
                    <img src="${newsItem.image}" alt="News Image">
                    <div class="news-content">
                        <h3>${newsItem.title}</h3>
                        <p>${newsItem.description}</p>
                    </div>
                `;
                newsGrid.appendChild(item);
            });
        })
        .catch(error => console.error('Error loading news:', error)); // 捕获并输出错误
}

