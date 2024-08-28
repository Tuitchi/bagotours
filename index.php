<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/index.css" />
    <script src="https://unpkg.com/scrollreveal"></script>
    <title>BagoTours | kapitanbato.</title>
    <style>
        /* Existing styles... */
        .modal {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease-in-out;
        }

        .modal.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.2); /* Semi-transparent white background */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 400px;
            position: relative;
            transform: scale(0.8);
            transition: transform 0.3s ease-in-out;
            backdrop-filter: blur(10px); /* Glass blur effect */
            -webkit-backdrop-filter: blur(10px); /* For Safari */
            color: white; /* White text color to ensure readability */
        }

        .modal-content.show {
            transform: scale(1);
        }

        .modal-content .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.6); /* Semi-transparent background */
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 18px;
            color: #fff;
        }

        .modal-content .close-btn:hover {
            background: rgba(255, 255, 255, 0.8);
        }

        .form-container {
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .form-container input {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-container #forgot-password{
          margin-top: 10px;
          text-decoration: underline;
        }
        .form-container a{
          text-decoration: none;
          color: skyblue;
        }
        .form-container a:hover{
          color: whitesmoke;
          text-decoration: underline;
        }
        .form-container p{
          margin-top: 10px;
          font-size: 16px;
          color: white;
          text-align: center;
        }

        .form-container button {
            padding: 10px;
            background-color: #007BFF;
            margin-top: 3px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #0056b3;
        }

        .hidden {
            display: none;
        }

        .form-container.slide-in {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(50%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <header id="home">
        <nav>
            <div class="nav__bar">
                <div class="nav__logo"><a href="#">BagoTours.</a></div>
                <ul class="nav__links">
                    <li class="link"><a href="index">Home</a></li>
                    <li class="link"><a href="#about">About Us</a></li>
                    <li class="link"><a href="#gallery">Gallery</a></li>
                    <li class="link"><a href="#contact">Contact</a></li>
                    <li class="link"><button id="open-modal" class="btn">Login</button></li>
                </ul>
            </div>
        </nav>
        <div class="section__container header__container">
            <h1>The new way to plan your next adventure</h1>
            <h4>Explore the beautiful Bago City</h4>
            <button id="open-modal" class="btn">
                Login <i class="ri-arrow-right-line"></i>
            </button>
        </div>
    </header>

    <section class="about" id="about">
        <div class="section__container about__container">
            <div class="about__content">
                <h2 class="section__header">About us</h2>
                <p class="section__subheader">
                    Our mission is to ignite the spirit of discovery in every traveler's
                    heart, offering meticulously crafted itineraries that blend
                    adrenaline-pumping activities with awe-inspiring landscapes. With a
                    team of seasoned globetrotters, we ensure that every expedition is
                    infused with excitement, grace our planet. Embark on a voyage of a
                    lifetime with us, as we redefine the art of exploration.
                </p>
                <br>
                <button class="btn">
                    Read More <i class="ri-arrow-right-line"></i>
                </button>
            </div>
            <div class="about__image">
                <img src="assets/about.png" alt="about" />
            </div>
        </div>
    </section>

    <section class="discover" id="discover">
        <div class="section__container discover__container">
            <h2 class="section__header">Discover the most engaging places</h2>
            <p class="section__subheader">
                Let's see the world with us with you and your family.
            </p>
            <div class="discover__grid">
                <div class="discover__card">
                    <div class="discover__image">
                        <img src="assets/discover-1.png" alt="discover" />
                    </div>
                    <div class="discover__card__content">
                        <h4>Tan Juan Statue</h4>
                        <p>
                        "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                        <button class="discover__btn">
                            Discover More <i class="ri-arrow-right-line"></i>
                        </button>
                    </div>
                </div>
                <div class="discover__card">
                    <div class="discover__image">
                        <img src="assets/discover-2.png" alt="discover" />
                    </div>
                    <div class="discover__card__content">
                        <h4>Kipot Twin Falls</h4>
                        <p>
                        "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                        <button class="discover__btn">
                            Discover More <i class="ri-arrow-right-line"></i>
                        </button>
                    </div>
                </div>
                <div class="discover__card">
                    <div class="discover__image">
                        <img src="assets/discover-3.png" alt="discover" />
                    </div>
                    <div class="discover__card__content">
                        <h4>Rafael Salas Drive</h4>
                        <p>
                        "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                        <button class="discover__btn">
                            Discover More <i class="ri-arrow-right-line"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="gallery" id="gallery">
        <div class="gallery__container">
            <h2 class="section__header">Gallery photos</h2>
            <p class="section__subheader">
                Explore the most beautiful places in the world.
            </p>
            <div class="gallery__grid">
                <div class="gallery__card">
                    <img src="assets/gallery-1.jpg" alt="gallery" />
                    <div class="gallery__content">
                        <h4>Northern Lights</h4>
                        <p>Norway</p>
                    </div>
                </div>
                <div class="gallery__card">
                    <img src="assets/gallery-2.jpg" alt="gallery" />
                    <div class="gallery__content">
                        <h4>Krabi</h4>
                        <p>Thailand</p>
                    </div>
                </div>
                <div class="gallery__card">
                    <img src="assets/gallery-3.jpg" alt="gallery" />
                    <div class="gallery__content">
                        <h4>Bali</h4>
                        <p>Indonesia</p>
                    </div>
                </div>
                <div class="gallery__card">
                    <img src="assets/gallery-4.jpg" alt="gallery" />
                    <div class="gallery__content">
                        <h4>Grand Canyon</h4>
                        <p>USA</p>
                    </div>
                </div>
                <div class="gallery__card">
                    <img src="assets/gallery-5.jpg" alt="gallery" />
                    <div class="gallery__content">
                        <h4>taj mahal</h4>
                        <p>India</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="footer">
      <div class="section__container footer__container">
        <h4>BagoTours.</h4>
        <div class="footer__socials">
          <span>
            <a href="#"><i class="ri-facebook-fill"></i></a>
          </span>
          <span>
            <a href="#"><i class="ri-instagram-fill"></i></a>
          </span>
          <span>
            <a href="#"><i class="ri-twitter-fill"></i></a>
          </span>
          <span>
            <a href="#"><i class="ri-linkedin-fill"></i></a>
          </span>
        </div>
        <p>
          Cheap Romantic Vacations. Many people feel that there is a limited
          amount of abundance, wealth, or chance to succeed in life.
        </p>
        <ul class="footer__nav">
          <li class="footer__link"><a href="#home">Home</a></li>
          <li class="footer__link"><a href="#about">About</a></li>
          <li class="footer__link"><a href="#discover">Discover</a></li>
          <!-- <li class="footer__link"><a href="#blog">Blog</a></li>
          <li class="footer__link"><a href="#journals">Journals</a></li>
          <li class="footer__link"><a href="#gallery">Gallery</a></li>
          <li class="footer__link"><a href="#contact">Contact</a></li> -->
        </ul>
      </div>
      <div class="footer__bar">
        Copyright © 2024 kapitanbato. All rights reserved.
      </div>  
    </section>

    <!-- Modal Structure -->
    <div id="modal" class="modal">
        <form class="modal-content">
            <button type="button" class="close-btn" id="close-modal">&times;</button>
            <div id="sign-in-form" class="form-container">
                <h2>Sign In</h2>
                <label for="Uname">Username or E-mail</label>
                <input name="Uname" type="email" placeholder="Email" required />
                <label for="pwd">Password</label>
                <input name="pwd" type="password" placeholder="Password" required />
                <button type="submit" class="btn">Sign in</button>
                <a href="#" id="forgot-password">Forgot Password?</a>
                <p>Need an Account? <a href="#" id="to-sign-up">Sign Up</a></p>
            </div>
            <div id="sign-up-form" class="form-container hidden">
                <h2>Sign Up</h2>
                <label for="Uname">Username</label>
                <input name="Uname" type="text" placeholder="Username" required />
                <label for="email">Email</label>
                <input name="email" type="email" placeholder="Email" required />
                <label for="pwd">Password</label>
                <input name="pwd" type="password" placeholder="Password" required />
                <label for="con-pwd">Confirm Password</label>
                <input name="con-pwd  " type="password" placeholder="Confirm-Password" required />
                <button type="button">Sign Up</button>
                <p>Already have an Account? <a href="#" id="to-sign-in">Sign In</a></p>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('modal');
            const signInForm = document.getElementById('sign-in-form');
            const signUpForm = document.getElementById('sign-up-form');
            const openModalButtons = document.querySelectorAll('#open-modal');
            const toSignUpButton = document.getElementById('to-sign-up');
            const toSignInButton = document.getElementById('to-sign-in');
            const closeModalButton = document.getElementById('close-modal');

            openModalButtons.forEach(button => {
                button.addEventListener('click', () => {
                    modal.classList.add('active');
                    signInForm.classList.add('slide-in');
                });
            });

            toSignUpButton.addEventListener('click', (event) => {
                event.preventDefault(); // Prevent default anchor behavior
                signInForm.classList.add('hidden');
                signUpForm.classList.remove('hidden');
                signUpForm.classList.add('slide-in');
                signInForm.classList.remove('slide-in');
            });

            toSignInButton.addEventListener('click', (event) => {
                event.preventDefault(); // Prevent default anchor behavior
                signUpForm.classList.add('hidden');
                signInForm.classList.remove('hidden');
                signInForm.classList.add('slide-in');
                signUpForm.classList.remove('slide-in');
            });

            closeModalButton.addEventListener('click', () => {
                modal.classList.remove('active');
            });

            // Optional: Close modal when clicking outside of the modal-content
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
