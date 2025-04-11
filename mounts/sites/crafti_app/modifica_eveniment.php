<?php 
session_start();

if (!isset($_SESSION['logged_in'])) {
    header("Location: autentificare_manager.php");
    exit;
}

// Conectare la baza de date
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crafti";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conectare esuata: " . $conn->connect_error);
}

$id_eveniment = $_GET['id_eveniment'] ?? null;

if (!$id_eveniment || !is_numeric($id_eveniment)) {
    echo "ID Eveniment nu a fost transmis sau este invalid.";
    exit;
}

$sql = "SELECT e.id_eveniment, e.denumire_eveniment, e.responsabil_eveniment, e.nr_loc_disponibile, e.cost_eveniment, fdo.data, fdo.ora, fdo.id_filiala
        FROM Eveniment e
        JOIN Filiala_Data_Ora fdo ON e.id_eveniment = fdo.id_eveniment
        WHERE e.id_eveniment = $id_eveniment";

$result = $conn->query($sql);

if ($result === false) {
    echo "Eroare SQL: " . $conn->error;
    exit;
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Evenimentul nu a fost găsit.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $denumire_eveniment = $_POST['denumire_eveniment'];
    $responsabil_eveniment = $_POST['responsabil_eveniment'];
    $nr_loc_disponibile = $_POST['nr_loc_disponibile'];
    $cost_eveniment = $_POST['cost_eveniment'];
    $data_eveniment = $_POST['data_eveniment'];
    $ora_eveniment = $_POST['ora_eveniment'];
    $id_filiala = $_POST['id_filiala'];

    $sql_update = "UPDATE Eveniment SET denumire_eveniment = '$denumire_eveniment', responsabil_eveniment = '$responsabil_eveniment', 
                   nr_loc_disponibile = $nr_loc_disponibile, cost_eveniment = $cost_eveniment 
                   WHERE id_eveniment = $id_eveniment";

    if ($conn->query($sql_update) === TRUE) {
        $sql_update_filiala = "UPDATE Filiala_Data_Ora SET id_filiala = $id_filiala, data = '$data_eveniment', ora = '$ora_eveniment' 
                               WHERE id_eveniment = $id_eveniment";

        if ($conn->query($sql_update_filiala) === TRUE) {
            header("Location: panou_manager.php");
            exit;
        } else {
            echo "Eroare la actualizarea datei și orei: " . $conn->error;
        }
    } else {
        echo "Eroare la actualizarea evenimentului: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifică Eveniment</title>
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f7f9fc;
        display: flex;
        justify-content: center;
        padding: 40px;
    }

    form {
        background-color: #ffffff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 700px;
    }

    h2 {
        text-align: center;
        color: #34495e;
        margin-bottom: 30px;
    }

    label {
        display: block;
        margin-top: 20px;
        font-weight: 600;
        color: #2c3e50;
    }

    input[type="text"],
    input[type="number"],
    input[type="date"],
    input[type="time"] {
        width: 100%;
        padding: 12px;
        margin-top: 8px;
        border: 1px solid #ccc;
        border-radius: 8px;
    }

    input[type="submit"] {
        margin-top: 30px;
        width: 100%;
        padding: 14px;
        background-color: #2980b9;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: #21618c;
    }

    @media (max-width: 600px) {
        form {
            padding: 25px;
        }
    }
</style>

</head>
<body>

    <form action="modifica_eveniment.php?id_eveniment=<?php echo $id_eveniment; ?>" method="post">
        <h2>Modifică Eveniment</h2>

        <label for="denumire_eveniment">Denumire Eveniment:</label>
        <input type="text" id="denumire_eveniment" name="denumire_eveniment" value="<?php echo $row['denumire_eveniment']; ?>" required>

        <label for="responsabil_eveniment">Responsabil Eveniment:</label>
        <input type="text" id="responsabil_eveniment" name="responsabil_eveniment" value="<?php echo $row['responsabil_eveniment']; ?>" required>

        <label for="nr_loc_disponibile">Număr Locuri Disponibile:</label>
        <input type="number" id="nr_loc_disponibile" name="nr_loc_disponibile" value="<?php echo $row['nr_loc_disponibile']; ?>" required>

        <label for="cost_eveniment">Cost Eveniment:</label>
        <input type="number" step="0.01" id="cost_eveniment" name="cost_eveniment" value="<?php echo $row['cost_eveniment']; ?>" required>

        <label for="data_eveniment">Data Eveniment:</label>
        <input type="date" id="data_eveniment" name="data_eveniment" value="<?php echo $row['data']; ?>" required>

        <label for="ora_eveniment">Ora Eveniment:</label>
        <input type="time" id="ora_eveniment" name="ora_eveniment" value="<?php echo $row['ora']; ?>" required>

        <label for="id_filiala">Filiala:</label>
        <input type="number" id="id_filiala" name="id_filiala" value="<?php echo $row['id_filiala']; ?>" required>

        <input type="submit" value="Salvează Modificările">
    </form>

</body>
</html>
