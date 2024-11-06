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
         /* General Styling */
         :root {
            --primary-color: #0a0d14;
            --secondary-color: #f49e09;
            --white: #ffffff;
            --dark-bg: #1a1e28;
            --light-gray: #f4f4f4;
            --font-family: "Poppins", sans-serif;
        }

        /* body {
            font-family: var(--font-family);
            color: var(--white);
            background-color: var(--primary-color);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        } */

        .download-app {
            display: flex;
            flex-direction: row;
            align-items: center;
            padding: 2rem 1rem;
            background-color: var(--dark-bg);
            border-radius: 12px;
            max-width: 90%;
            margin: 0 auto;
            gap: 1rem;
        }

        .phone-mockup {
            margin-left: 20px;
            position: relative;
            width: 190px;
            height: 370px;
            background-color: #333;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .phone-mockup::before {
            content: "";
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 6px;
            background-color: var(--light-gray);
            border-radius: 3px;
        }

        .phone-mockup::after {
            content: "";
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 6px;
            background-color: var(--light-gray);
            border-radius: 3px;
        }

        .app-screenshot {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: opacity 0.3s ease;
        }

        .phone-mockup:hover .app-screenshot {
            opacity: 0.3; /* Makes the screenshot dim */
        }

        /* Logo Image on hover */
        .phone-mockup .logo {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80px; /* Adjust size as needed */
            height: 80px; /* Adjust size as needed */
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .phone-mockup:hover .logo {
            opacity: 1; /* Makes the logo visible on hover */
        }

        /* Blurred background effect */
        .phone-mockup:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(5px); /* Apply blur effect on hover */
        }

        .app-info {
            text-align: left;
            max-width: 60%;
        }

        .app-info h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 0.5rem;
        }

        .app-info p {
            font-size: 1rem;
            color: var(--light-gray);
            margin-bottom: 1.5rem;
        }

        .btn-download {
            padding: 0.8rem 1.2rem;
            border-radius: 8px;
            color: var(--white);
            font-size: 1rem;
            font-weight: 600;
            background-color: var(--secondary-color);
            border: none;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
        }

        .btn-download:hover {
            background-color: #e09108;
        }

        /* Media Query for Mobile Devices */
        @media (max-width: 480px) {
            .download-app {
                padding: 1.5rem;
                flex-direction: column;
                align-items: center;
            }

            .app-info {
                max-width: 100%;
                text-align: center;
            }

            .app-info h2 {
                font-size: 1.5rem;
            }

            .app-info p {
                font-size: 0.9rem;
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
                    <li class="link"><a href="#about">Download APK</a></li>
                    <li class="link"><a href="#contact">Abouts</a></li>
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
    <section>
        <div class="download-app">
            <div class="phone-mockup">
                <img src="assets/mock 1.png" alt="App Screenshot" class="app-screenshot">
                <img src="assets/LOGO1.png" alt="Logo" class="logo"> <!-- Replace with actual logo -->
            </div>
            <div class="app-info">
                <h2>Download Our App</h2>
                <p>Experience our features on the go! Get the app for quick access and smooth navigation.</p>
                <button class="btn-download" onclick="window.location.href='https://appstorelink.com';">Download Now</button>
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
    <!-- <script src="https://unpkg.com/scrollreveal"></script> -->


</body>

</html>