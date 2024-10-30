<?php require_once 'include/db_conn.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/landing.css" />
    <title>BagoTours | kapitanbato.</title>
    <style>
        .modal {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5); /* Dark overlay */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease-in-out;
    z-index: 1000;
}

.modal.active {
    opacity: 1;
    pointer-events: auto;
}

.modal-content {
    background: rgba(255, 255, 255, 0.15); /* Frosted glass effect */
    backdrop-filter: blur(12px); /* Blurs background */
    border-radius: 12px;
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25); /* Enhanced shadow */
    overflow: hidden;
    width: 100%;
    max-width: 400px;
    position: relative;
    transform: scale(0.9);
    transition: transform 0.3s ease-in-out;
    padding: 30px;
    color: white;
}

.modal-content.show {
    transform: scale(1);
}

.modal-content .close-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255, 255, 255, 0.4);
    border: none;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    font-size: 18px;
    color: #fff;
    transition: background 0.2s ease-in-out;
}

.modal-content .close-btn:hover {
    background: rgba(255, 255, 255, 0.8);
}

.error-message {
    color: red;
    text-align: right;
    font-size: 12px;
    margin-top: -10px;
    margin-bottom: 15px;
}

.form-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100%;
}

.form-container h2 {
    font-size: 1.6rem;
    margin-bottom: 20px;
    color: #fff;
    text-align: center;
}

.form-container label {
    align-self: flex-start;
    margin-bottom: 5px;
    font-size: 14px;
    color: #fff;
}

.form-container input {
    padding: 12px;
    border: 1px solid rgba(255, 255, 255, 0.4);
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    width: 100%;
    margin-bottom: 15px;
    transition: border-color 0.3s ease;
}

.form-container input:focus {
    border-color: #007BFF;
    outline: none;
}

.form-container #forgot-password {
    margin-top: 10px;
    text-align: center;
    color: #00BFFF;
    text-decoration: none;
    font-size: 14px;
}

.form-container #forgot-password:hover {
    color: #87CEFA;
}

.form-container p {
    font-size: 14px;
    text-align: center;
    margin-top: 10px;
    color: #fff;
}

.form-container a {
    color: #87CEFA;
    text-decoration: none;
    font-weight: 500;
}

.form-container a:hover {
    color: #fff;
    text-decoration: underline;
}

.form-container button {
    padding: 12px;
    background-color: #007BFF;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    width: 100%;
    font-size: 16px;
    font-weight: 600;
    transition: background-color 0.3s ease;
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

/* Additional styles for small screen devices */
@media (max-width: 600px) {
    .modal-content {
        padding: 20px;
    }
    .form-container h2 {
        font-size: 1.4rem;
    }
    .form-container input {
        padding: 10px;
    }
    .form-container button {
        padding: 10px;
        font-size: 14px;
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
                    <li class="link"><a href="home">Home</a></li>
                    <li class="link"><a href="#about">About Us</a></li>
                    <li class="link"><a href="#gallery">Gallery</a></li>
                    <li class="link"><a href="#contact">Contact</a></li>
                    <li class="link"><button id="open-modal" class="btn" onclick="window.location.href='home';"> Discover More</button></li>
                </ul>
            </div>
        </nav>
        <div class="section__container header__container">
            <h1>The new way to plan your next adventure</h1>
            <h4>Explore the beautiful Bago City</h4>
            <button id="open-modal" class="btn" onclick="window.location.href='home';">
                Discover More <i class="ri-arrow-right-line"></i>
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
            </ul>
        </div>
        <div class="footer__bar">
            Copyright © 2024 kapitanbato. All rights reserved.
        </div>
    </section>

    <!-- Modal Structure -->
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/scrollreveal"></script>


</body>

</html>