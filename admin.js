document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    const captchaImg = document.getElementById('captcha-img');
    captchaImg.src = 'back/captcha.php?' + new Date().getTime();
    captchaImg.addEventListener('click', () => {
        captchaImg.src = 'back/captcha.php?' + new Date().getTime();
    });

    loginForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const formData = new FormData(loginForm);
        const data = {
            username: formData.get('username'),
            password: formData.get('password'),
            captcha: formData.get('captcha')
        };

        try {
            const response = await fetch('back/admin_login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log(result);

            if (result.status === 'success') {
                window.location.href = 'update.html';
            } else {
                alert(`登录失败：${result.message}`);
            }
        } catch (error) {
            console.error('登录请求失败：', error);
            alert(`登录请求失败：${error.message}`);
        } finally {
            captchaImg.src = 'back/captcha.php?' + new Date().getTime();
        }
    });
});
