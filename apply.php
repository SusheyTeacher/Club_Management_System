<?php
session_start();
include('db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
  header("Location: login.php?redirect=index.php#events");
  exit;
}

$studentId = $_SESSION['user_id'];
$eventId = (int)$_GET['event_id'];

// Prevent duplicate applications
$exists = mysqli_query($conn, "SELECT * FROM event_applications WHERE student_id = $studentId AND event_id = $eventId");
if (mysqli_num_rows($exists) === 0) {
    mysqli_query($conn, "INSERT INTO event_applications (student_id, event_id, status) VALUES ($studentId, $eventId, 'Pending')");
}

// Redirect back to events section
header("Location: index.php#events");
exit;
?>
