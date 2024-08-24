document.getElementById('sign-in-form').addEventListener('submit', function(e) {
    let isValid = true;

    const username = document.getElementById('signin-username');
    const password = document.getElementById('signin-password');

    // Clear previous errors
    document.getElementById('signin-username-error').textContent = '';
    document.getElementById('signin-password-error').textContent = '';

    // Username validation
    if (username.value.trim() === '') {
        document.getElementById('signin-username-error').textContent = 'Username is required.';
        isValid = false;
    }

    // Password validation
    if (password.value.trim() === '') {
        document.getElementById('signin-password-error').textContent = 'Password is required.';
        isValid = false;
    }

    if (!isValid) {
        e.preventDefault();
    }
});

document.getElementById('sign-up-form').addEventListener('submit', function(e) {
    let isValid = true;

    const username = document.getElementById('signup-username');
    const email = document.getElementById('signup-email');
    const password = document.getElementById('signup-password');
    const confirmPassword = document.getElementById('signup-confirm-password');

    // Clear previous errors
    document.getElementById('signup-username-error').textContent = '';
    document.getElementById('signup-email-error').textContent = '';
    document.getElementById('signup-password-error').textContent = '';
    document.getElementById('signup-confirm-password-error').textContent = '';

    // Username validation
    if (username.value.trim() === '') {
        document.getElementById('signup-username-error').textContent = 'Username is required.';
        isValid = false;
    }

    // Email validation
    if (email.value.trim() === '') {
        document.getElementById('signup-email-error').textContent = 'Email is required.';
        isValid = false;
    } else if (!email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        document.getElementById('signup-email-error').textContent = 'Enter a valid email address.';
        isValid = false;
    }

    // Password validation
    if (password.value.trim() === '') {
        document.getElementById('signup-password-error').textContent = 'Password is required.';
        isValid = false;
    }

    // Confirm password validation
    if (confirmPassword.value.trim() === '') {
        document.getElementById('signup-confirm-password-error').textContent = 'Confirm your password.';
        isValid = false;
    } else if (password.value !== confirmPassword.value) {
        document.getElementById('signup-confirm-password-error').textContent = 'Passwords do not match.';
        isValid = false;
    }

    if (!isValid) {
        e.preventDefault();
    }
});