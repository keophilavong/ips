// Initialize navbar functionality
function initNavbar() {
    // Get current page filename
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    
    // Find all navbar links
    const navLinks = document.querySelectorAll('.navbar a[href]');
    
    navLinks.forEach(link => {
        const linkPage = link.getAttribute('href').split('/').pop();
        
        // Check if this link matches current page
        if (linkPage === currentPage || 
            (currentPage === '' && linkPage === 'index.html') ||
            (currentPage === 'index.html' && linkPage === 'index.html')) {
            link.classList.add('active');
        }
    });
    
    // Check authentication status and update login/logout button
    checkAuthStatus();
    
    // Mobile menu toggle functionality
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navbarNavWrapper = document.querySelector('.navbar-nav-wrapper');
    
    if (mobileMenuToggle && navbarNavWrapper) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenuToggle.classList.toggle('active');
            navbarNavWrapper.classList.toggle('active');
        });
        
        // Close mobile menu when clicking on a link
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenuToggle.classList.remove('active');
                navbarNavWrapper.classList.remove('active');
            });
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.navbar')) {
                mobileMenuToggle.classList.remove('active');
                navbarNavWrapper.classList.remove('active');
            }
        });
    }
}

// Run on DOMContentLoaded or immediately if DOM is already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNavbar);
} else {
    // DOM is already loaded
    initNavbar();
}

// Check authentication status
function checkAuthStatus() {
    // Determine base path
    const basePath = window.location.pathname.includes('/admin/') ? '../' : '';
    
    fetch(basePath + 'backend/check-auth.php')
        .then(response => response.json())
        .then(data => {
            const loginBtn = document.getElementById('loginLogoutBtn');
            const userInfo = document.getElementById('userInfo');
            const userName = document.getElementById('userName');
            
            if (loginBtn) {
                if (data.logged_in) {
                    // User is logged in - show logout
                    loginBtn.textContent = 'LOGOUT';
                    loginBtn.href = '#';
                    loginBtn.onclick = function(e) {
                        e.preventDefault();
                        logout();
                    };
                    
                    // Show user info if available
                    if (data.name && userInfo && userName) {
                        userName.textContent = data.name;
                        userInfo.style.display = 'inline-block';
                    }
                } else {
                    // User is not logged in - show login
                    loginBtn.textContent = 'LOGIN';
                    loginBtn.href = basePath + 'login.html';
                    loginBtn.onclick = null;
                    
                    if (userInfo) {
                        userInfo.style.display = 'none';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error checking auth status:', error);
            // On error, default to login button
            const loginBtn = document.getElementById('loginLogoutBtn');
            if (loginBtn) {
                const basePath = window.location.pathname.includes('/admin/') ? '../' : '';
                loginBtn.textContent = 'LOGIN';
                loginBtn.href = basePath + 'login.html';
            }
        });
}

// Logout function
function logout() {
    const basePath = window.location.pathname.includes('/admin/') ? '../' : '';
    
    fetch(basePath + 'backend/logout.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to home page
            window.location.href = basePath + 'index.html';
        }
    })
    .catch(error => {
        console.error('Logout error:', error);
        // Force redirect even on error
        const basePath = window.location.pathname.includes('/admin/') ? '../' : '';
        window.location.href = basePath + 'index.html';
    });
}

