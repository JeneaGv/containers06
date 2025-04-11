<?php
session_start();

// Conectare la baza de date
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crafti";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conectare esuata: " . $conn->connect_error);
}

$eroare = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $parola = $_POST['parola'];

    $sql = "SELECT id_manager FROM Manager WHERE login = '$login' AND parola = '$parola'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['logged_in'] = true;
        $_SESSION['id_manager'] = $result->fetch_assoc()['id_manager'];
        header("Location: panou_manager.php");
        exit;
    } else {
        $eroare = "Autentificare eșuată! Verifică datele introduse.";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare Manager</title>
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(to right, #6dd5ed, #2193b0);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    form {
        background-color: #ffffff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 400px;
    }

    h1 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 30px;
    }

    label {
        display: block;
        margin-top: 15px;
        font-weight: 600;
        color: #34495e;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 12px;
        margin-top: 5px;
        border: 1px solid #bdc3c7;
        border-radius: 8px;
    }

    input[type="submit"] {
        margin-top: 25px;
        width: 100%;
        padding: 12px;
        background-color: #27ae60;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: #1e8449;
    }

    .eroare {
        margin-top: 20px;
        color: #e74c3c;
        text-align: center;
        font-weight: bold;
    }
</style>

</head>
<body>

    <form action="autentificare_manager.php" method="post">
        <h1>Autentificare Manager</h1>

        <label for="login">Login:</label>
        <input type="text" id="login" name="login" required>

        <label for="parola">Parola:</label>
        <input type="password" id="parola" name="parola" required>

        <input type="submit" value="Autentificare">

        <?php if (!empty($eroare)): ?>
            <div class="eroare"><?php echo $eroare; ?></div>
        <?php endif; ?>
    </form>

</body>
</html>
