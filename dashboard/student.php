<style>
html, body {
  height: 100%;
  margin: 0;
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
}
</style>
<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
  header("Location: ../login.php");
  exit;
}

include('../db.php');
include('../includes/header.php');

$student_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Handle Apply
if (isset($_GET['apply'])) {
  $event_id = (int)$_GET['apply'];
  $check = mysqli_query($conn, "SELECT * FROM event_applications WHERE event_id=$event_id AND student_id=$student_id");
  if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "INSERT INTO event_applications (event_id, student_id) VALUES ($event_id, $student_id)");
    echo "<div class='alert alert-success text-center'>Applied successfully.</div>";
  }
}
?>

<main class="container my-5">
  <h2 class="text-center mb-4">Student Dashboard – Welcome, <?php echo htmlspecialchars($name); ?></h2>

  <!-- Applications -->
  <h4 class="mb-3">Your Event Applications</h4>
  <?php
  $apps = mysqli_query($conn, "SELECT ea.*, e.name AS event_name, e.date, c.name AS club_name
                               FROM event_applications ea
                               JOIN events e ON ea.event_id = e.id
                               JOIN clubs c ON e.club_id = c.id
                               WHERE ea.student_id = $student_id
                               ORDER BY ea.applied_at DESC");

  if (mysqli_num_rows($apps) > 0) {
    echo "<ul class='list-group mb-4'>";
    while ($a = mysqli_fetch_assoc($apps)) {
      $badge = $a['status'] === 'Accepted' ? 'success' : ($a['status'] === 'Rejected' ? 'danger' : 'secondary');
      echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
              <div>
                <strong>{$a['event_name']}</strong><br>
                <small>{$a['club_name']} | {$a['date']}</small>
              </div>
              <span class='badge bg-{$badge}'>{$a['status']}</span>
            </li>";
    }
    echo "</ul>";
  } else {
    echo "<p class='text-muted'>No event applications yet.</p>";
  }
  ?>

  <!-- Available Events -->
  <h4 class="mb-3">Available Events</h4>
  <div class="row">
    <?php
    $all = mysqli_query($conn, "SELECT e.*, c.name AS club_name FROM events e 
                                JOIN clubs c ON e.club_id = c.id
                                ORDER BY e.date DESC");
    while ($e = mysqli_fetch_assoc($all)) {
      $event_id = $e['id'];
      $applied = mysqli_query($conn, "SELECT id FROM event_applications WHERE event_id=$event_id AND student_id=$student_id");
      $disabled = mysqli_num_rows($applied) > 0;

      echo "<div class='col-md-6 mb-4'>
              <div class='card h-100 shadow-sm'>
                <div class='card-body'>
                  <h5 class='card-title'>{$e['name']}</h5>
                  <p class='card-text'>{$e['description']}</p>
                  <p><strong>Date:</strong> {$e['date']}</p>
                  <p class='text-muted'><small>Organized by: {$e['club_name']}</small></p>";

      if ($disabled) {
        echo "<button class='btn btn-outline-secondary btn-sm' disabled>Already Applied</button>";
      } else {
        echo "<a href='?apply={$event_id}' class='btn btn-primary btn-sm'>Apply</a>";
      }

      echo "</div></div></div>";
    }
    ?>
  </div>
</div>
  </main>
<?php include('../includes/footer.php'); ?>
