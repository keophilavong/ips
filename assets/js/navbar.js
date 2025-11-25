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
                    // Check if we're on an admin page
                    const isAdminPage = window.location.pathname.includes('/admin/');
                    
                    // User is logged in - show logout
                    loginBtn.textContent = 'LOGOUT';
                    loginBtn.href = '#';
                    loginBtn.onclick = function(e) {
                        e.preventDefault();
                        logout();
                    };
                    
                    // Show user info only on admin pages for admins, or for regular users on regular pages
                    if (data.name && userInfo && userName) {
                        if (data.type === 'admin' && !isAdminPage) {
                            // Admin on regular pages - don't show admin name, keep it clean
                            userInfo.style.display = 'none';
                        } else {
                            // Show user info for regular users or admins on admin pages
                            userName.textContent = data.name;
                            userInfo.style.display = 'inline-block';
                        }
                    }
                    
                    // Add dashboard link based on user type
                    addDashboardLink(data.type, basePath);
                } else {
                    // User is not logged in - show login
                    loginBtn.textContent = 'LOGIN';
                    loginBtn.href = basePath + 'login.html';
                    loginBtn.onclick = null;
                    
                    if (userInfo) {
                        userInfo.style.display = 'none';
                    }
                    
                    // Remove dashboard link if exists
                    removeDashboardLink();
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
            removeDashboardLink();
        });
}

// Add dashboard link to navbar
function addDashboardLink(userType, basePath) {
    // Remove existing dashboard link if any
    removeDashboardLink();
    
    // Find the navbar nav top section
    const navTop = document.querySelector('.navbar-nav-top');
    if (!navTop) return;
    
    // Check if dashboard link already exists
    if (document.getElementById('dashboardNavLink')) return;
    
    // Check if we're on an admin page
    const isAdminPage = window.location.pathname.includes('/admin/');
    
    // Create dashboard link
    const dashboardLi = document.createElement('li');
    const dashboardLink = document.createElement('a');
    dashboardLink.id = 'dashboardNavLink';
    
    // Show appropriate dashboard link based on user type and page location
    if (userType === 'admin') {
        // Admin users: only show admin dashboard link on admin pages
        // On regular pages, don't show any dashboard link (keep regular navigation)
        if (isAdminPage) {
            dashboardLink.href = basePath + 'admin/dashboard.html';
            dashboardLink.innerHTML = '<span style="font-size: 1.2em; margin-right: 0.3em;">⚙</span> Dashboard';
        } else {
            // Admin on regular pages - don't show admin link, keep it clean
            return;
        }
    } else {
        // Regular users: show staff dashboard link (only on regular pages)
        if (!isAdminPage) {
            dashboardLink.href = basePath + 'dashboard.html';
            dashboardLink.innerHTML = '<span style="font-size: 1.2em; margin-right: 0.3em;">📊</span> Dashboard';
        } else {
            // Regular users shouldn't be on admin pages, but just in case
            return;
        }
    }
    
    dashboardLi.appendChild(dashboardLink);
    
    // Insert at the beginning of the nav
    navTop.insertBefore(dashboardLi, navTop.firstChild);
}

// Remove dashboard link from navbar
function removeDashboardLink() {
    const dashboardLink = document.getElementById('dashboardNavLink');
    if (dashboardLink && dashboardLink.parentElement) {
        dashboardLink.parentElement.remove();
    }
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

