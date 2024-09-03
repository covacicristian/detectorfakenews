    <?php
    include 'db.php'; 
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obține titlul și ID-ul utilizatorului
        $title = isset($_POST['title']) ? $_POST['title'] : '';
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

        if (empty($title) || empty($user_id)) {
            echo json_encode(['error' => 'Titlul sau ID-ul utilizatorului lipsesc!']);
            exit();
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost:5000/detect");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['title' => $title]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        if ($response === false) {
            echo json_encode(['error' => 'Eroare la conectarea cu API-ul!']);
            curl_close($ch);
            exit();
        }
        curl_close($ch);

        error_log("API Response: " . $response);

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['error' => 'Răspunsul API-ului nu este un JSON valid!']);
            exit();
        }

        if ($data && isset($data['is_fake'], $data['message'])) {
            $is_fake = $data['is_fake'] ? 1 : 0;

            // Inserare în DetectionResults
            $stmt = $conn->prepare("INSERT INTO DetectionResults (is_fake, detected_at) VALUES (?, NOW())");
            if ($stmt === false) {
                echo json_encode(['error' => 'Eroare la pregătirea interogării DetectionResults: ' . $conn->error]);
                exit();
            }
            $stmt->bind_param("i", $is_fake);
            if (!$stmt->execute()) {
                echo json_encode(['error' => 'Eroare la executarea interogării DetectionResults: ' . $stmt->error]);
                exit();
            }
            $stmt->close();

            // Inserare în ActivityLogs
            $activity = $is_fake ? 'a verificat o știre falsă' : 'a verificat o știre adevărată';
            $stmt_log = $conn->prepare("INSERT INTO ActivityLogs (user_id, activity, timestamp) VALUES (?, ?, NOW())");
            if ($stmt_log === false) {
                echo json_encode(['error' => 'Eroare la pregătirea interogării ActivityLogs: ' . $conn->error]);
                exit();
            }
            $stmt_log->bind_param("is", $user_id, $activity);
            if (!$stmt_log->execute()) {
                echo json_encode(['error' => 'Eroare la executarea interogării ActivityLogs: ' . $stmt_log->error]);
                exit();
            }
            $stmt_log->close();

            echo json_encode([
                'is_fake' => $is_fake,
                'message' => $data['message']
            ]);
        } else {
            echo json_encode(['error' => 'Eroare la procesarea răspunsului!']);
        }

        $conn->close();
    }
    ?>




    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Detector Fake News</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
        <style>
                body {
                    background-color: #f4f6f9;
                    color: #333;
                    position: relative;
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
                .btn-custom {
                    background-color: #007bff;
                    color: #fff;
                }
                .btn-custom:hover {
                    background-color: #0056b3;
                }
                .alert-custom {
                    border-radius: 0;
                    background-color: #d4edda;
                    color: #155724;
                }
                .alert-custom .close {
                    color: #155724;
                }
                .slider-container {
                    margin: 20px 0;
                }
                .slider-item {
                    background-color: #fff;
                    border-radius: 8px;
                    padding: 20px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }
                .navbar-custom {
                    background-color: #333;
                }
                .navbar-brand img {
                    width: 40px;
                    height: 40px;
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
                .user-info {
                    background-color: #007bff;
                    color: #fff;
                    padding: 10px;
                    border-radius: 8px;
                    margin-bottom: 20px;
                    display: flex;
                    align-items: center;
                }
                .user-info .fas {
                    font-size: 1.5rem;
                    margin-right: 10px;
                }
                .user-info .username {
                    font-weight: bold;
                    text-transform: capitalize; 
                }
            </style>
    </head>
    <body>
        <!-- Navigatie -->
        <nav class="navbar navbar-expand-lg navbar-custom">
            <a class="navbar-brand" href="#"><img src="favicon.ico" alt="Logo"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="detect.php">Detectare</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="articole.html">Articole</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Butonul de logout -->
        <button class="btn logout-btn logout-btn-fixed" onclick="logout()">Logout</button>

        <!-- Caseta cu informații despre utilizator -->
        <div class="container">
            <?php
            $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
            $is_admin = isset($_SESSION['username']) && $_SESSION['username'] === 'admin';
            ?>
            <div class="user-info">
                <i class="fas fa-user"></i>
                <span class="username"><?php echo $is_admin ? 'Admin' : htmlspecialchars($username); ?></span>
            </div>

            <!-- Caseta de detectare-->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detectază Autenticitatea</h5>
                </div>
                <div class="card-body">
                    <form id="newsForm">
                        <div class="form-group">
                            <label for="newsTitle">Introdu numele știrii</label>
                            <input type="text" class="form-control" id="newsTitle" placeholder="Introdu aici un titlu" required>
                        </div>
                        <button type="submit" class="btn btn-custom">Verifică</button>
                    </form>
                    <div id="result" class="mt-4">
                        <!-- Rezultatul se va afisa aici -->
                    </div>
                </div>
            </div>

            <!-- Exemple de stiri -->
            <div class="slider-container">
                <h5 class="mb-3">Află știri din ultimul timp</h5>
                <div id="exampleSlider" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="slider-item">
                                <h6>O dronă a lovit un depozit de petrol din regiunea Rostov, Rusia</h6>
                                <p>Adevărat</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="slider-item">
                                <h6>Un nou proiect de lege adoptat în statul Missouri interzice oricui să dețină sau să utilizeze panouri solare, invocând preocupări legate de impactul acestora asupra rețelelor locale de energie.</h6>
                                <p>Fals</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="slider-item">
                                <h6>În Filipine, cursurile și activitățile guvernamentale din regiunea capitalei naționale (NCR) au fost suspendate din cauza ploilor abundente aduse de musonul sud-vestic, cunoscut local ca "Habagat".</h6>
                                <p>Adevărat</p>
                            </div>
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#exampleSlider" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#exampleSlider" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
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

            // Form Submission
            $('#newsForm').on('submit', function(event) {
        event.preventDefault();
        var title = $('#newsTitle').val().trim(); 

        if (!title) {
            $('#result').html('<div class="alert alert-danger" role="alert">Vă rugăm să introduceți un titlu de știre!</div>');
            return;
        }

        $.ajax({
            url: 'http://localhost:5000/detect',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ title: title }), // Trimite titlul știrii
            success: function(data) {
                console.log('Raw Response:', data);
                try {
                    if (data.error) {
                        $('#result').html('<div class="alert alert-danger" role="alert">' + data.error + '</div>');
                    } else {
                        $('#result').html('<div class="alert alert-success" role="alert">' +
                            '<i class="fas ' + (data.is_fake ? 'fa-exclamation-triangle feedback-icon' : 'fa-check-circle feedback-icon') + '"></i> ' +
                            '<span class="feedback">' + data.title + ' - ' + data.message + '</span></div>');
                    }
                } catch (e) {
                    $('#result').html('<div class="alert alert-danger" role="alert">Eroare la procesarea răspunsului!</div>');
                    console.error('Error parsing JSON:', e);
                }
            },
            error: function(xhr, status, error) {
                $('#result').html('<div class="alert alert-danger" role="alert">Eroare: ' + (xhr.responseJSON ? xhr.responseJSON.error : error) + '</div>');
                console.error('AJAX Error:', status, error, xhr.responseText);
            }
        });
    });



            $(document).ready(function() {
                $('.carousel').carousel();
            });
        </script>
    </body>
    </html>
