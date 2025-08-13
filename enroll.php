<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $userId = $_SESSION['user_id'];
    $courseId = (int)$_POST['course_id'];

    // Check if already enrolled to prevent duplicates
    $checkStmt = $conn->prepare("SELECT id FROM user_courses WHERE user_id = ? AND course_id = ?");
    $checkStmt->bind_param("ii", $userId, $courseId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows === 0) {
        // Not enrolled, so insert the record
        $stmt = $conn->prepare("INSERT INTO user_courses (user_id, course_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $courseId);
        $stmt->execute();
    }

    // Redirect back to the course page, where they will now see the course content
    header("Location: courses.php?id=" . $courseId);
    exit();
} else {
    // If accessed directly or without a course ID, redirect to the course library
    header("Location: courses.php");
    exit();
}
?>
