// Load Navbar
// Determine base path based on current location
const basePath = window.location.pathname.includes('/admin/') ? '../' : '';
fetch(basePath + "components/navbar.html")
    .then(res => {
        if (!res.ok) {
            throw new Error('Failed to load navbar');
        }
        return res.text();
    })
    .then(data => {
        const navbarEl = document.getElementById("navbar");
        if (navbarEl) {
            navbarEl.innerHTML = data;
            
            // Fix navbar links if we're in admin folder
            if (basePath) {
                const links = navbarEl.querySelectorAll('a[href]');
                links.forEach(link => {
                    const href = link.getAttribute('href');
                    // Only fix relative links (not absolute URLs or anchors)
                    if (href && !href.startsWith('http') && !href.startsWith('#') && !href.startsWith('/')) {
                        link.setAttribute('href', basePath + href);
                    }
                });
                // Fix logo image path
                const logoImg = navbarEl.querySelector('.logo');
                if (logoImg) {
                    const src = logoImg.getAttribute('src');
                    if (src && !src.startsWith('http') && !src.startsWith('/')) {
                        logoImg.setAttribute('src', basePath + src);
                    }
                }
            }
            
            // Set active link after navbar loads
            setActiveNavLink();
            // Initialize mobile menu
            initMobileMenu();
            
            // Load and execute navbar.js for auth status checking
            const navbarScript = document.createElement('script');
            navbarScript.src = basePath + 'assets/js/navbar.js';
            navbarScript.onload = function() {
                // Initialize navbar (which includes auth check)
                if (typeof initNavbar === 'function') {
                    initNavbar();
                } else if (typeof checkAuthStatus === 'function') {
                    // Fallback: just check auth if initNavbar not available
                    checkAuthStatus();
                }
            };
            document.head.appendChild(navbarScript);
        }
    })
    .catch(error => {
        console.error('Error loading navbar:', error);
        const navbarEl = document.getElementById("navbar");
        if (navbarEl) {
            navbarEl.innerHTML = '<nav class="navbar"><div class="navbar-container"><a href="' + basePath + 'index.html" class="navbar-brand">Navigation</a></div></nav>';
        }
    });

// Load Footer
fetch(basePath + "components/footer.html")
    .then(res => {
        if (!res.ok) {
            throw new Error('Failed to load footer');
        }
        return res.text();
    })
    .then(data => {
        const footerEl = document.getElementById("footer");
        if (footerEl) {
            footerEl.innerHTML = data;
        }
    })
    .catch(error => {
        console.error('Error loading footer:', error);
        const footerEl = document.getElementById("footer");
        if (footerEl) {
            footerEl.innerHTML = '<footer style="text-align: center; padding: 2rem; background: var(--bg-secondary);"><p>© 2025 Internal Education Worker Report System</p></footer>';
        }
    });

// Set active navigation link
function setActiveNavLink() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    const navLinks = document.querySelectorAll('.navbar a[href]');
    
    navLinks.forEach(link => {
        const linkPage = link.getAttribute('href').split('/').pop();
        
        if (linkPage === currentPage || 
            (currentPage === '' && linkPage === 'index.html') ||
            (currentPage === 'index.html' && linkPage === 'index.html')) {
            link.classList.add('active');
        }
    });
}

// Initialize mobile menu toggle
function initMobileMenu() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navbarNavWrapper = document.querySelector('.navbar-nav-wrapper');
    const navLinks = document.querySelectorAll('.navbar a[href]');
    
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