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
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mentor') {
  header("Location: ../login.php");
  exit;
}

include('../db.php');
include('../includes/header.php');


$mentor_id = $_SESSION['user_id'];
$name = $_SESSION['name'];
$msg = '';

// Handle event creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_event'])) {
  $nameEvent = mysqli_real_escape_string($conn, $_POST['name']);
  $description = mysqli_real_escape_string($conn, $_POST['description']);
  $date = $_POST['date'];
  $club_id = (int)$_POST['club_id'];
  $image = '';

  if (!empty($_FILES['image']['name'])) {
    $image = basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], '../upload/' . $image);
  }

  $sql = "INSERT INTO events (name, description, date, image, club_id, created_by) 
          VALUES ('$nameEvent', '$description', '$date', '$image', $club_id, $mentor_id)";
  $msg = mysqli_query($conn, $sql)
    ? "<div class='alert alert-success text-center'>Event created successfully.</div>"
    : "<div class='alert alert-danger text-center'>Event creation failed.</div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_event'])) {
  $event_id = (int)$_POST['event_id'];
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $description = mysqli_real_escape_string($conn, $_POST['description']);
  $date = $_POST['date'];
  $club_id = (int)$_POST['club_id'];
  $image = '';

  // Optional image upload
  if (!empty($_FILES['image']['name'])) {
    $image = basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], '../upload/' . $image);
    $sql = "UPDATE events SET name='$name', description='$description', date='$date', image='$image', club_id=$club_id 
            WHERE id=$event_id AND created_by=$mentor_id";
  } else {
    $sql = "UPDATE events SET name='$name', description='$description', date='$date', club_id=$club_id 
            WHERE id=$event_id AND created_by=$mentor_id";
  }

  $msg = mysqli_query($conn, $sql)
  ? "<div id='msgBox' class='alert alert-success text-center'>Event updated successfully.</div>"
  : "<div id='msgBox' class='alert alert-danger text-center'>Failed to update event.</div>";
}


// Handle delete event
if (isset($_GET['delete_event'])) {
  $eid = (int)$_GET['delete_event'];
  mysqli_query($conn, "DELETE FROM events WHERE id=$eid AND created_by=$mentor_id");
  mysqli_query($conn, "DELETE FROM event_applications WHERE event_id=$eid");
  $msg = "<div class='alert alert-warning text-center'>Event deleted.</div>";
}

// Accept/Reject applications
if (isset($_GET['action'], $_GET['app_id'])) {
  $status = $_GET['action'] === 'accept' ? 'Accepted' : 'Rejected';
  $app_id = (int)$_GET['app_id'];
  mysqli_query($conn, "UPDATE event_applications SET status='$status' WHERE id=$app_id");
}
?>

<main class="container my-5">
  <h2 class="text-center mb-4">Mentor Dashboard – Welcome, <?php echo htmlspecialchars($name); ?></h2>
  <?php if ($msg) echo $msg; ?>

  <div class="mb-4 text-end">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEventModal">➕ Create Event</button>
   </div>

  <div class="row">
  <!-- Events List -->
  <div class="col-md-6">
    <h4 class="mb-3">Your Events</h4>
    <?php
    $events = mysqli_query($conn, "SELECT e.*, c.name AS club_name FROM events e 
                                   JOIN clubs c ON e.club_id = c.id 
                                   WHERE created_by = $mentor_id ORDER BY date DESC");
    if (mysqli_num_rows($events) > 0) {
      while ($e = mysqli_fetch_assoc($events)) {
        echo "<div class='card mb-3 shadow-sm'>
                <div class='card-body'>
                  <h5 class='card-title'>{$e['name']} <span class='badge bg-secondary float-end'>{$e['date']}</span></h5>
                  <p class='card-text small'>{$e['description']}</p>
                  <p class='text-muted mb-2'>Club: {$e['club_name']}</p>
                  <a href='?view_apps={$e['id']}' class='btn btn-outline-primary btn-sm'>View Applications</a>
                  
                  <!-- Trigger Modal -->
<button class='btn btn-sm btn-secondary' data-bs-toggle='modal' data-bs-target='#editModal{$e['id']}'>Edit</button>

<!-- Edit Modal -->
<div class='modal fade' id='editModal{$e['id']}' tabindex='-1' aria-labelledby='editModalLabel{$e['id']}' aria-hidden='true'>
  <div class='modal-dialog modal-lg'>
    <form method='POST' enctype='multipart/form-data' class='modal-content'>
      <input type='hidden' name='edit_event' value='1'>
      <input type='hidden' name='event_id' value='{$e['id']}'>
      <div class='modal-header'>
        <h5 class='modal-title' id='editModalLabel{$e['id']}'>Edit Event</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
      </div>
      <div class='modal-body'>
        <div class='mb-3'><label class='form-label'>Event Name</label><input name='name' class='form-control' value='" . htmlspecialchars($e['name']) . "' required></div>
        <div class='mb-3'><label class='form-label'>Description</label><textarea name='description' class='form-control' rows='3' required>" . htmlspecialchars($e['description']) . "</textarea></div>
        <div class='mb-3'><label class='form-label'>Date</label><input type='date' name='date' class='form-control' value='{$e['date']}' required></div>
        <div class='mb-3'><label class='form-label'>Event Image</label><input type='file' name='image' class='form-control' accept='image/*'></div>
        <div class='mb-3'>
          <label class='form-label'>Organizing Club</label>
          <select name='club_id' class='form-control' required>";

          $clubList = mysqli_query($conn, "SELECT id, name FROM clubs");
          while ($club = mysqli_fetch_assoc($clubList)) {
            $selected = $club['id'] == $e['club_id'] ? 'selected' : '';
            echo "<option value='{$club['id']}' $selected>" . htmlspecialchars($club['name']) . "</option>";
          }

        echo "</select>
        </div>
      </div>
      <div class='modal-footer'>
        <button type='submit' class='btn btn-success'>Update Event</button>
        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
      </div>
    </form>
  </div>
</div>
<button class='btn btn-sm btn-danger' data-bs-toggle='modal' data-bs-target='#deleteModal{$e['id']}'>
  Delete
</button>


                 </div>
              </div>";
              echo '<div class="modal fade" id="deleteModal' . $e['id'] . '" tabindex="-1" aria-labelledby="deleteLabel' . $e['id'] . '" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header bg-danger text-white">
              <h5 class="modal-title" id="deleteLabel' . $e['id'] . '">Confirm Deletion</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              Are you sure you want to delete the event "<strong>' . htmlspecialchars($e['name']) . '</strong>"?
            </div>
            <div class="modal-footer">
              <a href="?delete_event=' . $e['id'] . '" class="btn btn-danger">Yes, Delete</a>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </div>
        </div>
      </div>';

              
              
      }
    } else {
      echo "<p class='text-muted'>You have not created any events yet.</p>";
    }
    ?>
  </div>

  <!-- Applications Display -->
  <div class="col-md-6">
    <h4 class="mb-3">Applications Received</h4>
    <?php
    if (isset($_GET['view_apps'])) {
      $eventId = (int)$_GET['view_apps'];
      $event = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM events WHERE id=$eventId AND created_by=$mentor_id"));
      if ($event) {
        echo "<h5 class='text-primary'>For Event: <strong>{$event['name']}</strong></h5>";

        $apps = mysqli_query($conn, "SELECT ea.id, ea.status, u.name FROM event_applications ea 
                                     JOIN users u ON ea.student_id = u.id 
                                     WHERE ea.event_id = $eventId");

        if (mysqli_num_rows($apps) > 0) {
          echo "<table class='table table-bordered table-sm mt-3'>
                  <thead class='table-light'>
                    <tr><th>Student</th><th>Status</th><th>Action</th></tr>
                  </thead><tbody>";
          while ($a = mysqli_fetch_assoc($apps)) {
            $badge = $a['status'] === 'Accepted' ? 'success' : ($a['status'] === 'Rejected' ? 'danger' : 'secondary');
            echo "<tr>
                    <td>{$a['name']}</td>
                    <td><span class='badge bg-{$badge}'>{$a['status']}</span></td>
                    <td>
                      <a href='?view_apps=$eventId&action=accept&app_id={$a['id']}' class='btn btn-sm btn-outline-success'>Accept</a>
                      <a href='?view_apps=$eventId&action=reject&app_id={$a['id']}' class='btn btn-sm btn-outline-warning'>Reject</a>
                    </td>
                  </tr>";
          }
          echo "</tbody></table>";
        } else {
          echo "<p class='text-muted'>No applications found for this event.</p>";
        }
      } else {
        echo "<p class='text-danger'>Invalid event selected.</p>";
      }
    } else {
      echo "<p class='text-muted'>Click 'View Applications' on the left to see student applications here.</p>";
    }
    ?>
  </div>
</div>


<!-- Create Event Modal -->
<div class="modal fade" id="createEventModal" tabindex="-1" aria-labelledby="createEventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" enctype="multipart/form-data" class="modal-content">
      <input type="hidden" name="create_event" value="1">
      <div class="modal-header">
        <h5 class="modal-title" id="createEventModalLabel">Create New Event</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3"><label class="form-label">Event Name</label><input name="name" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3" required></textarea></div>
        <div class="mb-3"><label class="form-label">Date</label><input type="date" name="date" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Event Image</label><input type="file" name="image" class="form-control" accept="image/*" required></div>
        <div class="mb-3">
          <label class="form-label">Organizing Club</label>
          <select name="club_id" class="form-control" required>
            <option value="">Select Club</option>
            <?php
            $clubs = mysqli_query($conn, "SELECT id, name FROM clubs");
            while ($club = mysqli_fetch_assoc($clubs)) {
              echo "<option value='{$club['id']}'>" . htmlspecialchars($club['name']) . "</option>";
            }
            ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Create Event</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
          </main>

<?php include('../includes/footer.php'); ?>

<!-- Bootstrap JS -->
<script>
  setTimeout(() => {
    const box = document.getElementById('msgBox');
    if (box) box.style.display = 'none';
  }, 2000); // 2000ms = 2 seconds
</script>
