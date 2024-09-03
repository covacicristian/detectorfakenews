<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: administrator.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = intval($_POST['category_id']);

    if (empty($title) || empty($content) || $category_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Date invalide']);
        exit();
    }

    // Inserare în baza de date
    $stmt = $conn->prepare("INSERT INTO News (title, content, category_id) VALUES (?, ?, ?)");
    if ($stmt === false) {
        echo json_encode(['status' => 'error', 'message' => 'Eroare la pregătirea interogării: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param("ssi", $title, $content, $category_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Știrea a fost adăugată cu succes']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Eroare la inserare: ' . $stmt->error]);
    }

    $stmt->close();
}
?>
