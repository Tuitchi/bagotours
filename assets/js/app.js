document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal');
    const signInForm = document.getElementById('sign-in-form');
    const signUpForm = document.getElementById('sign-up-form');
    const openModalButtons = document.querySelectorAll('#open-modal');
    const toSignUpButton = document.getElementById('to-sign-up');
    const toSignInButton = document.getElementById('to-sign-in');
    const closeModalButton = document.getElementById('close-modal');

    function clearFormInputs(form) {
        form.reset();
    }

    const loginForm = document.getElementById('loginForm');
    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const submitButton = loginForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Logging in...';

        const formData = new FormData(loginForm);

        $.ajax({
            url: 'php/login.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                const data = JSON.parse(response);
                document.getElementById('username-error').textContent = '';
                document.getElementById('username').style.border = '1px solid #ddd';

                document.getElementById('password-error').textContent = '';
                document.getElementById('password').style.border = '1px solid #ddd';


                if (data.success) {
                    setTimeout(function() {
                        window.location.href = data.redirect;
                    }, 1500);
                } else {
                    if (data.errors.username) {
                        document.getElementById('username-error').textContent = data.errors.username;
                        document.getElementById('username').style.border = '1px solid red';

                    }
                    if (data.errors.password) {
                        document.getElementById('password-error').textContent = data.errors.password;
                        document.getElementById('password').style.border = '1px solid red';

                    }
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                submitButton.disabled = false;
                submitButton.textContent = 'Sign in';
            }
        });
    });
    const signupForm = document.getElementById('signupForm');
    signupForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(signupForm);

        $.ajax({
            url: 'php/register.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {},
            success: function(response) {
                let data = JSON.parse(response);
                document.getElementById('regName-error').textContent = '';
                document.getElementById('fname').style.border = '1px solid #ddd';
                document.getElementById('lname').style.border = '1px solid #ddd';
                document.getElementById('regUsername-error').textContent = '';
                document.getElementById('signup-username').style.border = '1px solid #ddd';
                document.getElementById('regEmail-error').textContent = '';
                document.getElementById('regHome-error').textContent = '';
                document.getElementById('email').style.border = '1px solid #ddd';
                document.getElementById('home-address').style.border = '1px solid #ddd';
                document.getElementById('regPassword-error').textContent = '';
                document.getElementById('pwd').style.border = '1px solid #ddd';
                document.getElementById('regconPass-error').textContent = '';
                document.getElementById('con-pwd').style.border = '1px solid #ddd';

                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    if (data.errors.name) {
                        document.getElementById('regName-error').textContent = data.errors.name;
                        document.getElementById('lname').style.border = '1px solid red';
                        document.getElementById('fname').style.border = '1px solid red';
                    }
                    if (data.errors.uname) {
                        document.getElementById('regUsername-error').textContent = data.errors.uname;
                        document.getElementById('signup-username').style.border = '1px solid red';
                    }
                    if (data.errors.email) {
                        document.getElementById('regEmail-error').textContent = data.errors.email;
                        document.getElementById('email').style.border = '1px solid red';
                    }
                    if (data.errors.home) {
                        document.getElementById('regHome-error').textContent = data.errors.home;
                        document.getElementById('home-address').style.border = '1px solid red';
                    }
                    if (data.errors.pwd) {
                        document.getElementById('regPassword-error').textContent = data.errors.pwd;
                        document.getElementById('pwd').style.border = '1px solid red';
                    }
                    if (data.errors.confirm_password) {
                        document.getElementById('regconPass-error').textContent = data.errors.confirm_password;
                        document.getElementById('con-pwd').style.border = '1px solid red';
                    }
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });

    openModalButtons.forEach(button => {
        button.addEventListener('click', () => {
            modal.classList.add('active');
            signInForm.classList.add('slide-in');
        });
    });

    toSignUpButton.addEventListener('click', (event) => {
        event.preventDefault();
        signInForm.classList.add('hidden');
        signUpForm.classList.remove('hidden');
        signUpForm.classList.add('slide-in');
        signInForm.classList.remove('slide-in');

        document.getElementById('username-error').textContent = '';
        document.getElementById('username').style.border = '1px solid #ddd';
        document.getElementById('password-error').textContent = '';
        document.getElementById('password').style.border = '1px solid #ddd';
    });

    toSignInButton.addEventListener('click', (event) => {
        event.preventDefault();
        signUpForm.classList.add('hidden');
        signInForm.classList.remove('hidden');
        signInForm.classList.add('slide-in');
        signUpForm.classList.remove('slide-in');


        document.getElementById('regName-error').textContent = '';
        document.getElementById('fname').style.border = '1px solid #ddd';
        document.getElementById('lname').style.border = '1px solid #ddd';
        document.getElementById('regUsername-error').textContent = '';
        document.getElementById('signup-username').style.border = '1px solid #ddd';
        document.getElementById('regEmail-error').textContent = '';
        document.getElementById('regHome-error').textContent = '';
        document.getElementById('email').style.border = '1px solid #ddd';
        document.getElementById('home-address').style.border = '1px solid #ddd';
        document.getElementById('regPassword-error').textContent = '';
        document.getElementById('pwd').style.border = '1px solid #ddd';
        document.getElementById('regconPass-error').textContent = '';
        document.getElementById('con-pwd').style.border = '1px solid #ddd';
    });

    closeModalButton.addEventListener('click', () => {
        modal.classList.remove('active');
        clearFormInputs(loginForm);
        clearFormInputs(signupForm);

        document.getElementById('regName-error').textContent = '';
        document.getElementById('fname').style.border = '1px solid #ddd';
        document.getElementById('lname').style.border = '1px solid #ddd';
        document.getElementById('regUsername-error').textContent = '';
        document.getElementById('signup-username').style.border = '1px solid #ddd';
        document.getElementById('regEmail-error').textContent = '';
        document.getElementById('regHome-error').textContent = '';
        document.getElementById('email').style.border = '1px solid #ddd';
        document.getElementById('home-address').style.border = '1px solid #ddd';
        document.getElementById('regPassword-error').textContent = '';
        document.getElementById('pwd').style.border = '1px solid #ddd';
        document.getElementById('regconPass-error').textContent = '';
        document.getElementById('con-pwd').style.border = '1px solid #ddd';
    });
});