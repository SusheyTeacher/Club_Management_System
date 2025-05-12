<style>
[id] {
  scroll-margin-top: 130px; /* adjust based on your navbar height */
}
</style>
<?php include('db.php'); ?>
<?php include('includes/header.php'); ?>



<!-- Hero Section -->
<section class="hero-section d-flex align-items-center text-center">
  <div class="container">
    <h1 class="display-4 fw-bold hero-heading">Welcome to the Club Management System</h1>
    <p class="lead hero-subtext">Discover events, join clubs, and connect with your school community</p>
    <a href="#events" class="btn btn-light btn-lg mt-3">Get Started</a>
  </div>
</section>



<!-- Event Search Section -->
<section class="container mt-5" id="events">
  <div class="row mb-4">
    <div class="col-md-8">
      <h2 class="mb-0">Latest Events</h2>
    </div>
    <div class="col-md-4">
      <form method="GET">
        <input type="text" name="search" class="form-control" placeholder="Search events or clubs..." />
      </form>
    </div>
  </div>

  <div class="row">
    <?php
    $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
    $sql = "SELECT e.*, c.name AS club_name FROM events e 
            JOIN clubs c ON e.club_id = c.id 
            WHERE e.name LIKE '%$search%' OR e.description LIKE '%$search%' OR c.name LIKE '%$search%'
            ORDER BY e.date DESC";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
      while ($event = mysqli_fetch_assoc($result)) {
        $eventId = $event['id'];
        $eventName = htmlspecialchars($event['name']);
        $eventDesc = htmlspecialchars(substr($event['description'], 0, 100)) . '...';
        $eventDate = $event['date'];
        $clubName = htmlspecialchars($event['club_name']);
        $eventImage = $event['image'];
      
        echo '
        <div class="col-md-6 mb-4">
          <div class="card h-100 shadow-sm">
            <div class="row g-0">
              <div class="col-md-4">
                <img src="upload/' . $eventImage . '" class="img-fluid h-100 w-100 rounded-start" style="object-fit: cover;" alt="event">
              </div>
              <div class="col-md-8">
                <div class="card-body">
                  <h5 class="card-title">' . $eventName . '</h5>
                  <p class="card-text">' . $eventDesc . '</p>
                  <p class="card-text"><strong>Date:</strong> ' . $eventDate . '</p>
                  <p class="card-text text-muted"><small>Organized by: ' . $clubName . '</small></p>';
      
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
          echo '<a href="login.php?redirect=index.php#events" class="btn btn-outline-primary btn-sm">Login to Apply as Student</a>';
        } else {
          $studentId = $_SESSION['user_id'];
          $checkApp = mysqli_query($conn, "SELECT id FROM event_applications WHERE student_id = $studentId AND event_id = $eventId");
          if (mysqli_num_rows($checkApp) > 0) {
            echo '<button class="btn btn-success btn-sm" disabled>✅ Applied</button>';
          } else {
            echo '<a href="apply.php?event_id=' . $eventId . '" class="btn btn-outline-success btn-sm">Apply</a>';
          }
        }
      
        echo '
                </div>
              </div>
            </div>
          </div>
        </div>';
      }
      
      
    } else {
      echo '<p class="text-muted text-center">No events found.</p>';
    }
    ?>
  </div>
</section>

<!-- Contact Section -->
<section id="contact" class="bg-light py-5">
  <div class="container text-center">
    <h3 class="mb-4">Contact Us</h3>
    <div class="row">
      <div class="col-md-4"><p>📧 info@school.edu</p></div>
      <div class="col-md-4"><p>📞 +975-2-123456</p></div>
      <div class="col-md-4"><p>📍 Thimphu, Bhutan</p></div>
    </div>
  </div>
</section>

<?php include('includes/footer.php'); ?>
