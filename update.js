document.addEventListener('DOMContentLoaded', () => {
    loadCarousel();
    loadNews();

    document.getElementById('upload-carousel-form').addEventListener('submit', uploadCarouselImage);
    document.getElementById('delete-carousel-form').addEventListener('submit', deleteCarouselImage);
    document.getElementById('upload-news-form').addEventListener('submit', addNews);
    document.getElementById('delete-news-form').addEventListener('submit', deleteNews);
    document.getElementById('update-news-form').addEventListener('submit', updateNews);
});

// 加载现有轮播图
function loadCarousel() {
    fetch(`./back/images.php?t=${new Date().getTime()}`)
        .then(response => response.json())
        .then(data => {
            const carouselPreview = document.getElementById('carousel-preview');
            carouselPreview.innerHTML = ''; // 清空之前的内容

            const images = data.images.slice(0, 3); // 限制最多显示3张图片
            images.forEach((image, index) => {
                const img = document.createElement('img');
                img.src = image;
                img.alt = `轮播图${index + 1}`;
                img.style = "width: 100px; margin: 5px; cursor: pointer;"; // 添加指针样式

                // 添加双击事件监听
                img.addEventListener('dblclick', () => openImageModal(image));

                carouselPreview.appendChild(img);
            });
        })
        .catch(error => console.error('Error loading carousel images:', error));
}

// 打开模态框显示图片
function openImageModal(imageSrc) {
    const modal = document.createElement('div');
    modal.style = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    `;

    const img = document.createElement('img');
    img.src = imageSrc;
    img.style = `
        max-width: 90%;
        max-height: 90%;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
    `;

    modal.addEventListener('click', () => {
        document.body.removeChild(modal);
    });

    modal.appendChild(img);
    document.body.appendChild(modal);
}

// 上传轮播图图片
function uploadCarouselImage(event) {
    event.preventDefault();
    const fileInput = document.getElementById('carousel-upload');
    if (fileInput.files.length === 0) {
        alert('请选择一张图片！');
        return;
    }
    const formData = new FormData();
    formData.append('image', fileInput.files[0]);
    fetch('./back/upload_image.php', {method: 'POST', body: formData})
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert(data.message);
                loadCarousel();

            } else if (data.error) {
                alert(data.error);
            }
        }).catch(error => console.error('Error uploading image:', error));
}

// 删除轮播图图片
function deleteCarouselImage(event) {
    event.preventDefault();
    const deleteId = document.getElementById('carousel-delete-id').value;
    if (!deleteId) {
        alert('请输入要删除的图片序号！');
        return;
    }

    // 从本地页面移除对应的图片
    const carouselPreview = document.getElementById('carousel-preview');
    const imgToDelete = carouselPreview.querySelectorAll('img')[deleteId - 1];
    if (imgToDelete) {
        carouselPreview.removeChild(imgToDelete);
    } else {
        alert('未找到对应的图片！');
        return;
    }

    fetch(`./back/delete_image.php?id=${deleteId}`, {
        method: 'GET'
    }).then(response => response.json())
        .then(data => {
            if (data.message) {
                alert(data.message);
            } else if (data.error) {
                alert(data.error);
            }
        }).catch(error => console.error('Error deleting image:', error));
}

// 加载现有新闻
function loadNews() {
    fetch('./back/news.php')
        .then(response => response.json())
        .then(data => {
            const newsPreview = document.getElementById('news-preview');
            newsPreview.innerHTML = ''; // 清空之前的内容

            const newsGrid = document.createElement('div');
            newsGrid.className = 'news-grid'; // 使用网格布局

            const newsItems = data.news.slice(0, 4); // 限制最多显示4条新闻
            newsItems.forEach((news, index) => {
                const newsDiv = document.createElement('div');
                newsDiv.className = 'news-item';

                newsDiv.innerHTML = `
                    <img src="${news.image}" alt="新闻图片">
                    <div>
                        <h3>${index + 1}. ${news.title}</h3>
                        <p>${news.description}</p>
                    </div>
                `;

                newsGrid.appendChild(newsDiv);
            });

            newsPreview.appendChild(newsGrid); // 添加网格布局
        })
        .catch(error => console.error('Error loading news:', error));
}

// 添加新闻
function addNews(event) {
    event.preventDefault();
    const title = document.getElementById('news-title').value;
    const description = document.getElementById('news-description').value;
    const fileInput = document.getElementById('news-image');
    const formData = new FormData();
    formData.append('title', title);
    formData.append('description', description);
    if (fileInput.files.length > 0) {
        formData.append('image', fileInput.files[0]);
    }
    fetch('./back/add_news.php', {
        method: 'POST', body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert(data.message);
                loadNews();
                } else if (data.error) {
                alert(data.error);
            }
        }).catch(error => console.error('Error adding news:', error));
    loadNews();
}

// 删除新闻
function deleteNews(event) {
    event.preventDefault();

    const deleteId = document.getElementById('news-delete-id').value;
    if (!deleteId) {
        alert('请输入要删除的新闻序号！');
        return;
    }

    // 从本地页面移除对应的新闻
    const newsPreview = document.getElementById('news-preview');
    const newsItems = newsPreview.querySelectorAll('.news-item');

    if (deleteId > 0 && deleteId <= newsItems.length) {
        // 移除对应的新闻项
        const newsToDelete = newsItems[deleteId - 1];
        newsToDelete.remove();

        // 重新更新所有新闻的序号
        const remainingNewsItems = newsPreview.querySelectorAll('.news-item');
        remainingNewsItems.forEach((newsItem, index) => {
            const titleElement = newsItem.querySelector('h3');
            if (titleElement) {
                // 更新标题中的序号
                const newTitle = titleElement.innerHTML.replace(/^\d+\.\s/, `${index + 1}. `);
                titleElement.innerHTML = newTitle;
            }
        });

        // 更新服务器上的新闻记录
        fetch(`./back/delete_news.php?id=${deleteId}`, { method: 'GET' })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => console.error('Error deleting news:', error));
    } else {
        alert('未找到对应的新闻条目！');
    }
}



// 更新新闻
function updateNews(event) {
    event.preventDefault();
    const updateId = document.getElementById('news-update-id').value;
    const title = document.getElementById('update-news-title').value;
    const description = document.getElementById('update-news-description').value;
    const fileInput = document.getElementById('update-news-image');
    const formData = new FormData();
    formData.append('title', title);
    formData.append('description', description);
    if (fileInput.files.length > 0) {
        formData.append('image', fileInput.files[0]);
    }
    fetch(`./back/update_news.php?id=${updateId}`, {
        method: 'POST',
        body: formData
    }).then(response => response.json()).then(data => {
        if (data.message) {
            alert(data.message);
            loadNews();
        } else if (data.error) {
            alert(data.error);
        }
    }).catch(error => console.error('Error updating news:', error));

    loadNews();
}
