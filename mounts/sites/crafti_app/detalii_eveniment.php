<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crafti";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conectare eșuată: " . $conn->connect_error);
}

$id_eveniment = isset($_GET['id_eveniment']) ? (int)$_GET['id_eveniment'] : 0;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_filiala_data_ora'])) {
    $id_filiala_data_ora = (int)$_POST['id_filiala_data_ora'];
    $copii = $_POST['copii'] ?? [];
    $parinte = $_POST['parinte'] ?? [];

    if (empty($parinte['prenume']) || empty($parinte['nr_telefon'])) {
        $message = 'Te rugăm să completezi datele părintelui.';
        // Remove the exit; statement here
    } else {
        $succes = true;
        $conn->begin_transaction();

        try {
            // Insert parent data
            $prenume_parinte = $conn->real_escape_string($parinte['prenume']);
            $nr_telefon_parinte = $conn->real_escape_string($parinte['nr_telefon']);
            $conn->query("INSERT INTO Parinte (prenume_parinte, nr_telefon_parinte) VALUES ('$prenume_parinte', '$nr_telefon_parinte')");
            $id_parinte = $conn->insert_id;

            // Get the number of available spots
            $sql_locuri_eveniment = "SELECT nr_loc_disponibile FROM Eveniment WHERE id_eveniment = $id_eveniment";
            $result_locuri_eveniment = $conn->query($sql_locuri_eveniment);
            $locuri_eveniment = $result_locuri_eveniment->fetch_assoc()['nr_loc_disponibile'];

            // Get the number of registered spots for this specific session
            $sql_nr = "SELECT COUNT(*) as total FROM Inscriere_Eveniment WHERE id_filiala_data_ora = $id_filiala_data_ora";
            $result_nr = $conn->query($sql_nr);
            $rand = $result_nr->fetch_assoc();
            $locuri_ocupate = $rand['total'];
            $locuri_ramase = $locuri_eveniment - $locuri_ocupate;

            // Check if spots are available
            if ($locuri_ramase <= 0) {
                throw new Exception("Nu mai sunt locuri disponibile pentru acest eveniment.");
            }

            // Process each child registration
            foreach ($copii as $copil) {
                if (empty($copil['prenume']) || empty($copil['an_nastere'])) {
                    continue; // Skip empty entries
                }

                $prenume = $conn->real_escape_string($copil['prenume']);
                $an = (int)$copil['an_nastere'];

                // Insert child data
                $stmt_copil = $conn->prepare("INSERT INTO Copil (prenume_copil, an_nastere_copil, id_parinte) VALUES (?, ?, ?)");
                $stmt_copil->bind_param("sii", $prenume, $an, $id_parinte);
                $stmt_copil->execute();
                $id_copil = $conn->insert_id;

                // Check if the child is already registered
                $verif_stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM Inscriere_Eveniment WHERE id_copil = ? AND id_filiala_data_ora = ?");
                $verif_stmt->bind_param("ii", $id_copil, $id_filiala_data_ora);
                $verif_stmt->execute();
                $result = $verif_stmt->get_result();
                $exista = $result->fetch_assoc()['cnt'];
                $verif_stmt->close();

                if ($exista > 0) {
                    throw new Exception("Copilul este deja înscris.");
                }

                // Register the child
                $stmt = $conn->prepare("INSERT INTO Inscriere_Eveniment (data_inscriere, id_filiala_data_ora, id_copil) VALUES (NOW(), ?, ?)");
                $stmt->bind_param("ii", $id_filiala_data_ora, $id_copil);
                $stmt->execute();
                $stmt->close();
            }

            // Commit transaction if everything succeeded
            $conn->commit();
            $message = 'Înscriere realizată cu succes!';
        } catch (Exception $e) {
            $conn->rollback();
            $message = 'Eroare la înscriere: ' . $e->getMessage();
        }
    }
}

$sql_eveniment = "SELECT * FROM Eveniment WHERE id_eveniment = $id_eveniment";
$result_eveniment = $conn->query($sql_eveniment);
$eveniment = $result_eveniment->fetch_assoc();

$sql_detalii = "
SELECT fdo.id_filiala_data_ora, fdo.data, fdo.ora, fc.adresa_filiala,
(SELECT COUNT(*) FROM Inscriere_Eveniment WHERE id_filiala_data_ora = fdo.id_filiala_data_ora) AS locuri_ocupate
FROM Filiala_Data_Ora fdo
JOIN Filiala_Crafti fc ON fdo.id_filiala = fc.id_filiala
WHERE fdo.id_eveniment = $id_eveniment
";
$rezultat_detalii = $conn->query($sql_detalii);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Detalii Eveniment</title>
    <style>
    body {
        font-family: 'Comic Sans MS', cursive, sans-serif;
        background: linear-gradient(to bottom right, #fff9c4, #b2ebf2);
        padding: 30px;
        margin: 0;
        color: #444;
    }

    .container {
        background: #ffffff;
        padding: 30px;
        border-radius: 20px;
        max-width: 900px;
        margin: auto;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    h2 {
        color: #ff6f61;
        font-size: 32px;
        margin-bottom: 10px;
    }

    h3 {
        color: #4db6ac;
        font-size: 24px;
        margin-top: 30px;
    }

    .bloc {
        margin-top: 20px;
        padding: 20px;
        border-radius: 15px;
        background: #f1f8e9;
        border: 1px solid #c5e1a5;
        transition: transform 0.2s ease;
    }

    .bloc:hover {
        transform: scale(1.01);
    }

    .btn {
        padding: 12px 24px;
        background-color: #4fc3f7;
        color: white;
        border: none;
        border-radius: 30px;
        font-size: 16px;
        margin-top: 15px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn:hover {
        background-color: #29b6f6;
        transform: scale(1.05);
    }

    input[type="text"],
    input[type="number"] {
        padding: 10px;
        width: 220px;
        margin: 8px 10px 8px 0;
        border-radius: 10px;
        border: 1px solid #ccc;
        font-size: 14px;
    }

    .copil {
        margin-bottom: 15px;
    }

    #mesaj {
        font-size: 20px;
        color: #00897b;
        margin-top: 20px;
        font-weight: bold;
    }

    .back-btn {
        background-color: #ffb74d;
    }

    .back-btn:hover {
        background-color: #ffa726;
    }

    .no-session {
        color: #c62828;
        font-size: 18px;
        font-weight: bold;
    }

    p {
        font-size: 16px;
        margin: 8px 0;
    }
</style>

</head>
<body>

<div class="container">
    <h2><?= htmlspecialchars($eveniment['denumire_eveniment']) ?></h2>
    <p><strong>Responsabil:</strong> <?= htmlspecialchars($eveniment['responsabil_eveniment']) ?></p>
    <p><strong>Cost:</strong> <?= htmlspecialchars($eveniment['cost_eveniment']) ?> lei</p>

    <?php if ($rezultat_detalii->num_rows > 0): ?>
        <?php
        $id_filiala_data_ora = 0;
        $locuri_ramase = 0;
        ?>

        <form method="post">
            <h3>Sesiuni disponibile:</h3>
            
            <?php
            while ($row = $rezultat_detalii->fetch_assoc()):
                $id_filiala_data_ora = $row['id_filiala_data_ora'];
                $sql_total = "SELECT nr_loc_disponibile FROM Eveniment WHERE id_eveniment = $id_eveniment";
                $res_total = $conn->query($sql_total);
                $nr_total = $res_total->fetch_assoc()['nr_loc_disponibile'];

                $locuri_libere = max(0, $nr_total - $row['locuri_ocupate']);
                $locuri_ramase = $locuri_libere;
            ?>
                <div class="bloc">
                    <p><strong>Data:</strong> <?= htmlspecialchars($row['data']) ?></p>
                    <p><strong>Ora:</strong> <?= substr($row['ora'], 0, 5) ?></p>
                    <p><strong>Adresă filială:</strong> <?= htmlspecialchars($row['adresa_filiala']) ?></p>
                    <p><strong>Locuri rămase:</strong> <?= $locuri_libere ?></p>
                </div>
            <?php endwhile; ?>

            <input type="hidden" name="id_filiala_data_ora" value="<?= $id_filiala_data_ora ?>">

            <?php if ($locuri_ramase > 0): ?>
                <h3>Date părinte:</h3>
                <div class="bloc">
                    Prenume Părinte: <input type="text" name="parinte[prenume]" required>
                    Nr. telefon Părinte: <input type="text" name="parinte[nr_telefon]" required>
                </div>

                <h3>Date copil:</h3>
                <div class="bloc">
                    <div id="copii-container">
                        <div class="copil">
                            Prenume: <input type="text" name="copii[0][prenume]" required>
                            An naștere: <input type="number" name="copii[0][an_nastere]" min="2005" max="2020" required>
                        </div>
                    </div>
                    <button type="button" onclick="adaugaCopil()" class="btn" style="background-color: #2196F3;">+ Adaugă copil</button>
                </div>

                <button type="submit" class="btn">Înregistrează</button>
            <?php else: ?>
                <p><strong>Nu mai sunt locuri disponibile pentru acest eveniment.</strong></p>
            <?php endif; ?>
        </form>
    <?php else: ?>
        <p>Nu sunt disponibile sesiuni pentru acest eveniment momentan.</p>
    <?php endif; ?>

    <?php if ($message): ?>
        <div id="mesaj"><?= htmlspecialchars($message) ?></div>
        <a href="vezi_evenimente.php"><button class="btn">Vezi Evenimente</button></a>
    <?php endif; ?>
</div>

<!-- Add this new div with the back button -->
<div style="text-align: center; margin-top: 20px;">
    <a href="vezi_evenimente.php"><button class="btn" style="background-color: #607D8B;">Înapoi la Evenimente</button></a>
</div>

<script>
let copilIndex = 1;
function adaugaCopil() {
    const container = document.getElementById('copii-container');
    const div = document.createElement('div');
    div.className = 'copil';
    div.innerHTML = `Prenume: <input type="text" name="copii[${copilIndex}][prenume]" required>
                     An naștere: <input type="number" name="copii[${copilIndex}][an_nastere]" min="2005" max="2020" required>`;
    container.appendChild(div);
    copilIndex++;
}
</script>

</body>
</html>

<?php $conn->close(); ?>