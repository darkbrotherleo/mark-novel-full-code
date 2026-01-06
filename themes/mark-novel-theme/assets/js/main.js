// JS cho menu điều hướng trên thiết bị di động
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-navigation');

    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('toggled');
            
            // Hiệu ứng đổi icon thành dấu X (Optional)
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !isExpanded);
        });
    }
});

// JS cho nút Black/White Mode
document.addEventListener('DOMContentLoaded', function() {
    // 1. Mobile Menu Toggle (Code cũ)
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-navigation');
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('toggled');
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !isExpanded);
        });
    }

    // 2. Theme Switcher (MỚI)
    const themeBtn = document.getElementById('theme-toggle');
    if (themeBtn) {
        themeBtn.addEventListener('click', function() {
            // Toggle class trên thẻ html
            document.documentElement.classList.toggle('theme-dark');
            
            // Lưu trạng thái vào bộ nhớ
            if (document.documentElement.classList.contains('theme-dark')) {
                localStorage.setItem('mark_theme', 'dark');
            } else {
                localStorage.setItem('mark_theme', 'light');
            }
        });
    }
});

/* USER PROFILE LAYOUT */
.user-profile-layout {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 30px;
    margin-top: 30px;
}

.user-sidebar {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 20px;
    height: fit-content;
}

.user-avatar-box {
    text-align: center;
    margin-bottom: 20px;
}
.user-avatar-box img {
    border-radius: 50%;
    margin: 0 auto 10px;
    border: 3px solid var(--primary);
}

.user-menu li a {
    display: block;
    padding: 10px;
    border-bottom: 1px dashed var(--border);
    color: var(--text-main);
}
.user-menu li a:hover, .user-menu li a.active {
    background: var(--bg-body);
    color: var(--primary);
    font-weight: bold;
}

.user-content {
    background: var(--bg-card);
    padding: 30px;
    border-radius: 8px;
    border: 1px solid var(--border);
}

.profile-form label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}
.profile-form input[type="text"], 
.profile-form input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--border);
    margin-bottom: 15px;
}

/* AUTH FORMS (Đăng ký/Đăng nhập) */
.mark-auth-form {
    max-width: 400px;
    margin: 40px auto;
    padding: 30px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 8px;
    box-shadow: var(--shadow);
}
.mark-auth-form input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
}
.mark-auth-form button {
    width: 100%;
    padding: 10px;
    background: var(--primary);
    color: white;
    border: none;
    cursor: pointer;
}

@media (max-width: 768px) {
    .user-profile-layout {
        grid-template-columns: 1fr;
    }
}

