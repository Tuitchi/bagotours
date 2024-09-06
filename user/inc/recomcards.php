<style>

.containercard {
  display: flex;
  gap: 3px;
  justify-content: space-around;
  
}


  .popcard-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 15px;
      margin-top: 20px;
    }

    .popcard {
      background-color: #333;
      border-radius: 10px;
      padding: 15px;
      text-align: center;
      width: 200px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    }

    .popcard img {
      width: 100%;
      border-radius: 10px;
    }

    .popcard h3 {
      margin-top: 10px;
      font-size: 18px;
    }

    .popcard p {
      margin-top: 5px;
      font-size: 14px;
    }

    .popcard .rating {
      display: inline-block;
      margin-top: 10px;
      font-size: 12px;
    }

    .popcard .rating .star {
      color: #ffc107;
    }

    .popcard .button {
      background-color: #ffc107;
      color: #222;
      padding: 8px 15px;
      border: none;
      border-radius: 5px;
      margin-top: 10px;
      cursor: pointer;
    }

    .popcard .button:hover {
      background-color: #e0a800;
    }

    .popcard-button-group {
      display: flex;
      justify-content: center;
      margin-top: 20px;
    }

    .popcard-button-group button {
      background-color: #ffc107;
      color: #222;
      padding: 8px 15px;
      border: none;
      border-radius: 5px;
      margin-left: 10px;
      cursor: pointer;
    }

    .popcard-button-group button:hover {
      background-color: #e0a800;
    }
</style>
<h1 id="title">Popular This Week</h1>

<div class="containercard">
  <!-- Weekly popcards -->
  <div class="popcard weekly">
    <img src="gallery-3.jpg" alt="Popular">
    <h3>NILO BOTAY</h3>
    <p>Type</p>
    <div class="rating">
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      9.4
    </div>
    <button class="button">Visit</button>   
  </div>
  <div class="popcard weekly">
    <img src="gallery-1.jpg" alt="Popular">
    <h3>Cristain RESORT</h3>
    <p>Type</p>
    <div class="rating">
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      9.8
    </div>
    <button class="button">Visit</button>
  </div>
  <div class="popcard weekly">
    <img src="gallery-4.jpg" alt="Popular">
    <h3>cREz Spa resort</h3>
    <p>Type</p>
    <div class="rating">
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      9.7
    </div>
    <button class="button">Visit</button>
  </div>
  <div class="popcard weekly">
    <img src="gallery-1.jpg" alt="Popular">
    <h3>Walter white lab</h3>
    <p>Type</p>
    <div class="rating">
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      9.8
    </div>
    <button class="button">Visit</button>
  </div>
  <div class="popcard weekly">
    <img src="gallery-3.jpg" alt="Popular">
    <h3>Ma-ao resort</h3>
    <p>Type</p>
    <div class="rating">
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      9.9
    </div>
    <button class="button">Visit</button>
  </div>

  <!-- Monthly popcards -->
  <div class="popcard monthly" style="display: none;">
    <img src="gallery-5.jpg" alt="Popular">
    <h3>Monthly Card 1</h3>
    <p>Type</p>
    <div class="rating">
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      9.2
    </div>
    <button class="button">Visit</button>
  </div>
  <div class="popcard monthly" style="display: none;">
    <img src="gallery-6.jpg" alt="Popular">
    <h3>Monthly Card 2</h3>
    <p>Type</p>
    <div class="rating">
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      9.6
    </div>
    <button class="button">Visit</button>
  </div>
  <div class="popcard monthly" style="display: none;">
    <img src="gallery-7.jpg" alt="Popular">
    <h3>Monthly Card 3</h3>
    <p>Type</p>
    <div class="rating">
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      9.5
    </div>
    <button class="button">Visit</button>
  </div>
  <div class="popcard monthly" style="display: none;">
    <img src="gallery-8.jpg" alt="Popular">
    <h3>Monthly Card 4</h3>
    <p>Type</p>
    <div class="rating">
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      9.7
    </div>
    <button class="button">Visit</button>
  </div>
  <div class="popcard monthly" style="display: none;">
    <img src="gallery-9.jpg" alt="Popular">
    <h3>Monthly Card 5</h3>
    <p>Type</p>
    <div class="rating">
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      <span class="star">★</span>
      9.8
    </div>
    <button class="button">Visit</button>
  </div>
</div>

<div class="button-group">
  <button id="weekly-btn">Weekly</button>
  <button id="monthly-btn">Monthly</button>
</div>
<script>
document.getElementById('weekly-btn').addEventListener('click', function() {
  document.querySelectorAll('.popcard.weekly').forEach(card => card.style.display = 'block');
  document.querySelectorAll('.popcard.monthly').forEach(card => card.style.display = 'none');
  document.getElementById('title').textContent = 'Popular This Week';
});

document.getElementById('monthly-btn').addEventListener('click', function() {
  document.querySelectorAll('.popcard.monthly').forEach(card => card.style.display = 'block');
  document.querySelectorAll('.popcard.weekly').forEach(card => card.style.display = 'none');
  document.getElementById('title').textContent = 'Popular This Month';
});
</script>