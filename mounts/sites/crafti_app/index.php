<?php
// Începem sesiunea pentru a putea redirecționa utilizatorii
session_start();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crafti - Pagina Principală</title>
    <style>
        body {
            font-family: 'Comic Sans MS', cursive, sans-serif;
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(to bottom right, #ffe4e1, #e0f7fa);
            color: #333;
        }

        h1 {
            color: #ff6f61;
            font-size: 36px;
            margin-bottom: 20px;
        }

        p {
            font-size: 20px;
            margin-bottom: 40px;
            color: #555;
        }

        .btn {
            font-size: 18px;
            padding: 12px 28px;
            margin: 15px;
            cursor: pointer;
            background-color: #ffb74d;
            color: #fff;
            border: none;
            border-radius: 25px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            background-color: #ffa726;
            transform: scale(1.05);
        }

        a {
            text-decoration: none;
        }
    </style>
</head>
<body>

    <h1>Bun venit la magazinul Crafti!</h1>
    <p>La Crafti, organizăm evenimente interesante pentru copii și părinți! Alege una dintre opțiunile de mai jos.</p>

    <a href="vezi_evenimente.php"><button class="btn">Părinte</button></a>
    <a href="autentificare_manager.php"><button class="btn">Manager</button></a>

</body>
</html>
