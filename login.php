        <?php
        include 'db.php'; 
        session_start();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = isset($_POST['username']) ? $_POST['username'] : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            if (empty($username) || empty($password)) {
                $_SESSION['error'] = "Nume de utilizator sau parola lipsesc!";
                header("Location: login.php");
                exit();
            }

            // Verificare specială pentru admin
            if ($username === 'admin' && $password === 'admin') {
                $_SESSION['user_id'] = 0; 
                $_SESSION['username'] = 'admin';
                header("Location: administrator.php
                "); 
                exit();
            }

            // Verificare pentru utilizatori 
            $stmt = $conn->prepare("SELECT * FROM Users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    header("Location: detect.php"); 
                    exit();
                } else {
                    $_SESSION['error'] = "Parolă incorectă!";
                    header("Location: login.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Nu există un utilizator cu acest nume!";
                header("Location: login.php");
                exit();
            }

            $stmt->close();
            $conn->close();
        }
        ?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Pagina de logare</title>
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                body {
                    background-image: url('https://images.unsplash.com/photo-1506773090264-ac0b07293a64');
                    background-size: cover;
                    background-position: center;
                    height: 100vh;
                    margin: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                .card {
                    width: 100%;
                    max-width: 400px;
                    height: auto;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    border: none;
                    border-radius: 10px;
                    background: rgba(255, 255, 255, 0.8);
                    backdrop-filter: blur(10px);
                    padding: 30px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                }

                .btn-custom {
                    background-color: #007bff;
                    color: #fff;
                    border-radius: 25px;
                }

                .btn-custom:hover {
                    background-color: #0056b3;
                }

                .fa {
                    color: #007bff;
                    margin-right: 10px;
                }

                .form-control {
                    border-radius: 25px;
                    padding: 15px;
                }

                h1 {
                    font-family: 'Arial Black', sans-serif;
                    font-weight: bold;
                    text-align: center;
                }

                .error-message {
                    color: #dc3545;
                    margin-bottom: 15px;
                }
            </style>
            <link rel="icon" type="image/x-icon" href="favicon.ico">
        </head>

        <body>
            <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
                <div class="card">
                    <h1 class="text-center mb-4"><i class="fa fa-shield-alt"></i> Detectare Fake News</h1>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="error-message">
                            <?php echo $_SESSION['error']; ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    <form action="login.php" method="post" class="w-100">
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" placeholder="Password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-custom btn-block w-100">Login</button>
                    </form>
                    <div class="text-center mt-4">
                        <a href="register.php" class="text-secondary">Creează un cont</a>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.min.js"></script>
        </body>

        </html>
