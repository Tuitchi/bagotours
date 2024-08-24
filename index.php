<?php
session_start();
if (isset ($_SESSION["user_id"])) {

}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="assets/css/index.css" />
    <script src="https://unpkg.com/scrollreveal"></script>
    <title>BagoTours | kapitanbato.</title>
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
            <li class="link"><a href="login">Login</a></li>
          </ul>
        </div>
      </nav>
      <div class="section__container header__container">
        <h1>The new way to plan your next adventure</h1>
        <h4>Explore the beautiful Bago City</h4>
        <a href="login.php">
          <button class="btn" >
            Login <i class="ri-arrow-right-line"></i>
            
          </button>
        </a>
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
                Discover the untamed beauty of Norway, a land where rugged
                mountains, and enchanting northern lights paint a surreal
                backdrop.
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
                From urban rock climbing to twilight cycling through royal
                parks, London beckons adventure enthusiasts to embrace
                opportunities.
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
                From scaling the iconic peaks of Mount Fuji to immersing in the
                serenity, Japan offers adventurers a captivating cultural
                treasures.
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
              <h4>Tokyo</h4>
              <p>Japan</p>
            </div>
          </div>
          <div class="gallery__card">
            <img src="assets/gallery-5.jpg" alt="gallery" />
            <div class="gallery__content">
              <h4>Taj Mahal</h4>
              <p>India</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="contact" id="contact">
      <div class="section__container contact__container">
        <div class="contact__col">
          <h4>Contact a travel researcher</h4>
          <p>We always aim to reply within 24 hours.</p>
        </div>
        <div class="contact__col">
          <div class="contact__card">
            <span><i class="ri-phone-line"></i></span>
            <h4>Call us</h4>
            <h5>+91 9876543210</h5>
            <p>We are online now</p>
          </div>
        </div>
        <div class="contact__col">
          <div class="contact__card">
            <span><i class="ri-mail-line"></i></span>
            <h4>Send us an enquiry</h4>
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
          <li class="footer__link"><a href="#gallery">Gallery</a></li>
          <li class="footer__link"><a href="#contact">Contact</a></li>
        </ul>
      </div>
      <div class="footer__bar">
        Copyright © 2024 kapitanbato. All rights reserved.
      </div>
    </section>

    <script src="assets/js/main.js"></script>
  </body>
</html>
