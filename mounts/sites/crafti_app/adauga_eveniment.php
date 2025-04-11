<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    header("Location: autentificare_manager.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Conectare la baza de date
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "crafti";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conectare esuata: " . $conn->connect_error);
    }

    // Preluare date din formular
    $id_filiala = $_POST['id_filiala'];
    $data_eveniment = $_POST['data_eveniment'];
    $ora_eveniment = $_POST['ora_eveniment'];
    $denumire_eveniment = $_POST['denumire_eveniment'];
    $responsabil_eveniment = $_POST['responsabil_eveniment'];
    $nr_loc_disponibile = $_POST['nr_loc_disponibile'];
    $cost_eveniment = $_POST['cost_eveniment'];

    // Verifică dacă id_filiala există în tabela Filiala_Crafti
    $sql_verifica_filiala = "SELECT 1 FROM Filiala_Crafti WHERE id_filiala = ?";
    $stmt = $conn->prepare($sql_verifica_filiala);
    $stmt->bind_param("i", $id_filiala);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Inserare în tabelul Eveniment
        $sql_adauga_eveniment = "INSERT INTO Eveniment (denumire_eveniment, responsabil_eveniment, nr_loc_disponibile, cost_eveniment) 
                                 VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_adauga_eveniment);
        $stmt->bind_param("ssis", $denumire_eveniment, $responsabil_eveniment, $nr_loc_disponibile, $cost_eveniment);

        if ($stmt->execute()) {
            $id_eveniment = $conn->insert_id;

            // Inserare în tabelul Filiala_Data_Ora
            $sql_adauga_filiala_data_ora = "INSERT INTO Filiala_Data_Ora (id_filiala, data, ora, id_eveniment) 
                                            VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_adauga_filiala_data_ora);
            $stmt->bind_param("issi", $id_filiala, $data_eveniment, $ora_eveniment, $id_eveniment);

            if ($stmt->execute()) {
                header("Location: panou_manager.php");
                exit;
            } else {
                echo "Eroare la adăugarea datei și orei: " . $conn->error;
            }
        } else {
            echo "Eroare la adăugarea evenimentului: " . $conn->error;
        }
    } else {
        echo "Filiala cu id-ul $id_filiala nu există în baza de date.";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaugă Eveniment</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(to right, #fceabb, #f8b500);
        margin: 0;
        padding: 40px;
    }

    h2 {
        text-align: center;
        font-size: 32px;
        color: #2d3436;
        margin-bottom: 30px;
    }

    form {
        background-color: #fffdf7;
        border-radius: 20px;
        padding: 40px;
        max-width: 600px;
        margin: auto;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    label {
        display: block;
        margin-top: 20px;
        font-weight: 600;
        font-size: 15px;
        color: #333;
    }

    input, select {
        width: 100%;
        padding: 12px;
        margin-top: 6px;
        border: 1px solid #ccc;
        border-radius: 12px;
        font-size: 15px;
        box-sizing: border-box;
        transition: border-color 0.2s ease;
    }

    input:focus, select:focus {
        border-color: #00b894;
        outline: none;
    }

    input[type="submit"] {
        background-color: #00b894;
        color: white;
        border: none;
        margin-top: 30px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        border-radius: 14px;
        padding: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: background-color 0.25s ease, transform 0.25s ease;
    }

    input[type="submit"]:hover {
        background-color: #019875;
        transform: translateY(-2px);
    }

    .btn-back {
        text-align: center;
        margin-top: 30px;
    }

    .btn-back a {
        text-decoration: none;
        background-color: #636e72;
        color: white;
        padding: 12px 24px;
        border-radius: 14px;
        font-weight: bold;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        transition: background-color 0.25s ease, transform 0.25s ease;
    }

    .btn-back a:hover {
        background-color: #2d3436;
        transform: translateY(-2px);
    }
</style>

</head>
<body>

    <h2 style="text-align: center;">Adăugare Eveniment</h2>
    <form action="adauga_eveniment.php" method="post">
        <label for="id_filiala">Filiala:</label>
        <select id="id_filiala" name="id_filiala" required>
            <option value="">-- Selectează filiala --</option>
            <option value="1">Filiala 1</option>
            <option value="2">Filiala 2</option>
            <option value="3">Filiala 3</option>
        </select>

        <label for="data_eveniment">Data Eveniment:</label>
        <input type="date" id="data_eveniment" name="data_eveniment" required>

        <label for="ora_eveniment">Ora Eveniment:</label>
        <input type="time" id="ora_eveniment" name="ora_eveniment" required>

        <label for="denumire_eveniment">Denumire Eveniment:</label>
        <input type="text" id="denumire_eveniment" name="denumire_eveniment" required>

        <label for="responsabil_eveniment">Responsabil Eveniment:</label>
        <input type="text" id="responsabil_eveniment" name="responsabil_eveniment" required>

        <label for="nr_loc_disponibile">Număr Locuri Disponibile:</label>
        <input type="number" id="nr_loc_disponibile" name="nr_loc_disponibile" required>

        <label for="cost_eveniment">Cost Eveniment:</label>
        <input type="number" step="0.01" id="cost_eveniment" name="cost_eveniment" required>

        <input type="submit" value="Adaugă Eveniment">
    </form>

    <div class="btn-back">
        <a href="panou_manager.php">Înapoi la Panou</a>
    </div>

</body>
</html>
