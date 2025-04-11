<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    header("Location: autentificare_manager.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crafti";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conectare esuată: " . $conn->connect_error);
}

// Ștergerea unui eveniment
if (isset($_GET['sterge_eveniment'])) {
    $id_eveniment = $_GET['sterge_eveniment'];

    $sql_sterge_filiala_data_ora = "DELETE FROM Filiala_Data_Ora WHERE id_eveniment = $id_eveniment";
    if ($conn->query($sql_sterge_filiala_data_ora) === TRUE) {
        $sql_sterge_eveniment = "DELETE FROM Eveniment WHERE id_eveniment = $id_eveniment";
        if ($conn->query($sql_sterge_eveniment) === TRUE) {
            echo "<div class='success-msg'>Eveniment șters cu succes!</div>";
        } else {
            echo "<div class='error-msg'>Eroare la ștergerea evenimentului: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='error-msg'>Eroare la ștergerea datei și orei evenimentului: " . $conn->error . "</div>";
    }
}

$sql_evenimente = "SELECT * FROM Eveniment";
$result_evenimente = $conn->query($sql_evenimente);

$afiseaza_formular = isset($_POST['adauga_eveniment']);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Panou Manager</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 30px;
        background: linear-gradient(to right, #fceabb, #f8b500);
        margin: 0;
    }

    h1 {
        text-align: center;
        color: #333;
        font-size: 36px;
        margin-bottom: 30px;
    }

    h2 {
        color: #444;
        font-size: 28px;
        margin-bottom: 15px;
        text-align: center;
    }

    .btn {
        font-size: 16px;
        padding: 10px 20px;
        margin: 10px 5px;
        cursor: pointer;
        background-color: #00b894;
        color: white;
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        transition: all 0.25s ease;
    }

    .btn:hover {
        background-color: #019875;
        transform: translateY(-2px);
    }

    .btn-delete {
        background-color: #d63031;
    }

    .btn-delete:hover {
        background-color: #c0392b;
    }

    .eveniment-container {
        background: #fffdf7;
        border-radius: 20px;
        padding: 30px;
        margin-top: 30px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        overflow: hidden;
        border-radius: 12px;
    }

    th {
        background-color: #00b894;
        color: white;
        padding: 14px;
        font-size: 16px;
    }

    td {
        padding: 12px;
        text-align: center;
        background-color: #fff;
        border-bottom: 1px solid #eee;
    }

    tr:hover td {
        background-color: #f1f8f6;
    }

    .success-msg, .error-msg {
        padding: 15px;
        margin: 20px auto;
        max-width: 600px;
        border-radius: 12px;
        font-weight: bold;
        text-align: center;
        font-size: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .success-msg {
        background-color: #d4edda;
        color: #155724;
    }

    .error-msg {
        background-color: #f8d7da;
        color: #721c24;
    }

    form, a {
        display: inline-block;
        text-align: center;
    }

    .btn-container {
        text-align: center;
        margin-bottom: 30px;
    }

</style>

</head>
<body>

    <h1>Panou de administrare - Manager</h1>

    <div class="btn-container">
    <form action="adauga_eveniment.php" method="get">
        <input type="submit" value="Adaugă Eveniment" class="btn">
    </form>

    <form action="lista_copii.php" method="get">
        <input type="submit" value="Lista Copiilor Înscriși" class="btn">
    </form>

    <a href="logout.php"><button class="btn">Logout</button></a>
</div>


    <div class="eveniment-container">
        <h2>Evenimente existente</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Denumire Eveniment</th>
                    <th>Responsabil</th>
                    <th>Locuri Disponibile</th>
                    <th>Cost</th>
                    <th>Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_evenimente->num_rows > 0) {
                    while ($row = $result_evenimente->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['id_eveniment'] . "</td>
                                <td>" . $row['denumire_eveniment'] . "</td>
                                <td>" . $row['responsabil_eveniment'] . "</td>
                                <td>" . $row['nr_loc_disponibile'] . "</td>
                                <td>" . $row['cost_eveniment'] . "</td>
                                <td>
                                    <a href='modifica_eveniment.php?id_eveniment=" . $row['id_eveniment'] . "' class='btn'>Modifică</a>
                                    <a href='?sterge_eveniment=" . $row['id_eveniment'] . "' class='btn btn-delete'>Șterge</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Nu există evenimente.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>
