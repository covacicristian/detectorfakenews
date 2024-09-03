<?php
include 'db.php'; 
session_start();

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$users = [];
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $stmt = $conn->prepare("SELECT * FROM Users");
    $stmt->execute();
    $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Ștergere utilizator
if (isset($_GET['delete'])) {
    $userId = intval($_GET['delete']);
    if ($userId > 0) {
        $conn->autocommit(FALSE);
        try {
            

            // Șterge rândurile din tabelul 'activitylogs'
            $stmt = $conn->prepare("DELETE FROM activitylogs WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();

            // Șterge utilizatorul
            $stmt = $conn->prepare("DELETE FROM Users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            echo "Eroare: " . htmlspecialchars($e->getMessage());
        }
        $conn->autocommit(TRUE);
        header("Location: activity.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activitate Users</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            color: #333;
        }
        .navbar-brand img {
            height: 40px;
        }
        .container {
            margin-top: 20px;
        }
        .card {
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: #fff;
            border-bottom: none;
        }
        .navbar-custom {
            background-color: #333;
        }
        .nav-link {
            color: #fff !important;
        }
        .nav-link:hover {
            color: #f8f9fa !important;
        }
        .logout-btn {
                background-color: #dc3545;
                border: none;
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 0.25rem;
            }
            .logout-btn:hover {
                background-color: #c82333;
                cursor: pointer;
            }
            .logout-btn-fixed {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1030; 
            }
    </style>
</head>
<body>
    <!-- Navigatie -->
    <nav class="navbar navbar-expand-lg navbar-custom">
            <a class="navbar-brand" href="#"><img src="favicon.ico" alt="Logo"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="administrator.php">Adaugare Stire</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="activity.php">Activity Logs</a>
                    </li>
                </ul>
            </div>
        </nav>


        <!-- Butonul de logout -->
        <button class="btn logout-btn logout-btn-fixed" onclick="logout()">Logout</button>

    <!-- main -->
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Activity</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <a href="?delete=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Esti sigur ca vrei sa stergi utilizatorul?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
         function logout() {
                alert("Ați fost deconectat!");
                window.location.href = 'login.php';
            }
        </script>
</body>
</html>
