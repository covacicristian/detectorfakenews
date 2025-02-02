        <?php
        include 'db.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO Users (username, email, password) VALUES ('$username', '$email', '$password')";

            if ($conn->query($sql) === TRUE) {
                echo "Înregistrare reușită!";
                header("Location: login.php");
            } else {
                echo "Eroare: " . $sql . "<br>" . $conn->error;
            }

            $conn->close();
        }
        ?>

        <<!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Pagina de Înregistrare</title>
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
            </style>
            <link rel="icon" type="image/x-icon" href="favicon.ico">
        </head>

        <body>
            <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
                <div class="card">
                    <h1 class="text-center mb-4"><i class="fa fa-shield-alt"></i> Înregistrare</h1>
                    <form action="register.php" method="post" class="w-100">
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" placeholder="Password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-custom btn-block w-100">Înregistrează-te</button>
                    </form>
                    <div class="text-center mt-4">
                        <a href="login.php" class="text-secondary">Ai deja un cont? Loghează-te</a>
                    </div>
                </div>
            </div>
        </body>

        </html>
