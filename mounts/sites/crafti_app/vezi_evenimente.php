<?php
// vezi_evenimente.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crafti";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conectare eșuată: " . $conn->connect_error);
}

$sql = "SELECT * FROM Eveniment";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Evenimente Crafti</title>
    <style>
        body {
            font-family: 'Comic Sans MS', cursive, sans-serif;
            background: linear-gradient(to bottom right, #fffde7, #e1f5fe);
            padding: 30px;
            margin: 0;
            color: #444;
        }

        h1 {
            text-align: center;
            color: #ff6f61;
            font-size: 36px;
            margin-bottom: 40px;
        }

        .card {
            background-color: #ffffff;
            border-radius: 20px;
            padding: 25px;
            margin: 20px auto;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .event-title {
            font-size: 28px;
            color: #ffb74d;
            margin-bottom: 15px;
        }

        .event-detail {
            font-size: 18px;
            color: #555;
            margin-bottom: 8px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4fc3f7;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            margin-top: 15px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn:hover {
            background-color: #29b6f6;
            transform: scale(1.05);
        }

        p {
            text-align: center;
            font-size: 20px;
            color: #777;
        }
    </style>
</head>
<body>

<h1>Evenimente disponibile</h1>

<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='card'>";
        echo "<div class='event-title'>" . htmlspecialchars($row["denumire_eveniment"]) . "</div>";
        echo "<div class='event-detail'>Responsabil: " . htmlspecialchars($row["responsabil_eveniment"]) . "</div>";
        echo "<div class='event-detail'>Cost: " . htmlspecialchars($row["cost_eveniment"]) . " lei</div>";
        echo "<a class='btn' href='detalii_eveniment.php?id_eveniment=" . $row["id_eveniment"] . "'>Mai multe detalii</a>";
        echo "</div>";
    }
} else {
    echo "<p>Nu sunt evenimente disponibile.</p>";
}
?>

</body>
</html>
