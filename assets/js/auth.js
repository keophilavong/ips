// Unified Login - Automatically detects admin or user
document.getElementById("loginForm")?.addEventListener("submit", function(e){
    e.preventDefault();
    
    const result = document.getElementById("result");
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.textContent = "Signing in...";
    result.innerHTML = "";
    result.className = "";

    let formData = new FormData();
    const identifier = document.getElementById("identifier").value;
    const password = document.getElementById("password").value;
    
    formData.append("identifier", identifier);
    formData.append("password", password);
    
    // Debug logging
    console.log("Login attempt:", { identifier, passwordLength: password.length });

    fetch("backend/login.php", {
        method: "POST",
        body: formData
    })
    .then(res => {
        console.log("Response status:", res.status);
        console.log("Response headers:", res.headers.get("content-type"));
        
        // Check if response is JSON
        const contentType = res.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            return res.json().then(data => {
                console.log("JSON Response:", data);
                return data;
            });
        } else {
            // If not JSON, try to parse as text first
            return res.text().then(text => {
                console.log("Text Response:", text);
                try {
                    const parsed = JSON.parse(text);
                    console.log("Parsed JSON:", parsed);
                    return parsed;
                } catch (e) {
                    console.log("Not JSON, trying fallback");
                    // If it's not JSON, it might be old format
                    if (text.trim() === "success") {
                        // Try to determine if admin by checking identifier
                        if (!identifier.includes("@")) {
                            return { status: "success", type: "admin" };
                        }
                        return { status: "success", type: "user" };
                    }
                    return { status: "error", message: text || "Login failed" };
                }
            });
        }
    })
    .then(data => {
        if (data.status === "success") {
            result.innerHTML = '<div class="alert alert-success">Login successful! Redirecting...</div>';
            setTimeout(() => {
                // Redirect based on user type
                if (data.type === "admin") {
                    window.location = "admin/dashboard.html";
                } else {
                    window.location = "dashboard.html";
                }
            }, 1000);
        } else {
            result.innerHTML = '<div class="alert alert-error">' + (data.message || 'Login failed. Please check your credentials and try again.') + '</div>';
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    })
    .catch(error => {
        console.error("Login error:", error);
        console.error("Error details:", {
            message: error.message,
            stack: error.stack
        });
        result.innerHTML = '<div class="alert alert-error">An error occurred. Check browser console (F12) for details.</div>';
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

// Register
document.getElementById("registerForm")?.addEventListener("submit", function(e){
    e.preventDefault();
    
    const result = document.getElementById("result");
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.textContent = "Creating account...";
    result.innerHTML = "";
    result.className = "";

    let form = new FormData();
    form.append("fullname", document.getElementById("fullname").value);
    form.append("email", document.getElementById("email").value);
    form.append("password", document.getElementById("password").value);

    fetch("backend/register.php", {
        method: "POST",
        body: form
    })
    .then(res => {
        const contentType = res.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            return res.json();
        } else {
            return res.text().then(text => {
                // Handle old format for backward compatibility
                if (text.trim() === "success") {
                    return { status: "success" };
                }
                return { status: "error", message: text || "Registration failed" };
            });
        }
    })
    .then(data => {
        if (data.status === "success") {
            result.innerHTML = '<div class="alert alert-success">Account created successfully! Redirecting to login...</div>';
            setTimeout(() => {
                window.location = "login.html";
            }, 2000);
        } else {
            result.innerHTML = '<div class="alert alert-error">' + (data.message || 'Error creating account. Email may already be in use.') + '</div>';
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    })
    .catch(error => {
        console.error("Registration error:", error);
        result.innerHTML = '<div class="alert alert-error">An error occurred. Please try again later.</div>';
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});
