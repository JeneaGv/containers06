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

// Obține detalii despre copii și părinți pentru fiecare eveniment
$sql = "
SELECT 
    e.id_eveniment, e.denumire_eveniment, e.nr_loc_disponibile,
    c.prenume_copil, p.prenume_parinte, p.nr_telefon_parinte,
    fdo.id_filiala_data_ora
FROM Eveniment e
LEFT JOIN Filiala_Data_Ora fdo ON e.id_eveniment = fdo.id_eveniment
LEFT JOIN Inscriere_Eveniment ie ON fdo.id_filiala_data_ora = ie.id_filiala_data_ora
LEFT JOIN Copil c ON ie.id_copil = c.id_copil
LEFT JOIN Parinte p ON c.id_parinte = p.id_parinte
ORDER BY e.id_eveniment
";

$result = $conn->query($sql);

$evenimente = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_eveniment = $row['id_eveniment'];

        if (!isset($evenimente[$id_eveniment])) {
            $evenimente[$id_eveniment] = [
                'denumire' => $row['denumire_eveniment'],
                'nr_total' => $row['nr_loc_disponibile'],
                'copii' => [],
            ];
        }

        if ($row['prenume_copil']) {
            $evenimente[$id_eveniment]['copii'][] = [
                'copil' => $row['prenume_copil'],
                'parinte' => $row['prenume_parinte'],
                'telefon' => $row['nr_telefon_parinte'],
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Evenimente & Copii</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(to right, #ffeaa7, #fab1a0);
        padding: 40px;
        margin: 0;
    }

    h1 {
        text-align: center;
        font-size: 36px;
        color: #2d3436;
        margin-bottom: 40px;
    }

    .eveniment {
        background: #fff;
        padding: 30px;
        margin-bottom: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease;
    }

    .eveniment:hover {
        transform: translateY(-5px);
    }

    h2 {
        color: #6c5ce7;
        font-size: 28px;
        margin-bottom: 10px;
    }

    p {
        font-size: 16px;
        color: #2d3436;
        margin: 6px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: #fefefe;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    th, td {
        padding: 14px 16px;
        text-align: center;
    }

    th {
        background-color: #74b9ff;
        color: white;
        font-weight: bold;
    }

    td {
        background-color: #ecf0f1;
        color: #2d3436;
    }

    tr:nth-child(even) td {
        background-color: #dfe6e9;
    }

    .no-data {
        text-align: center;
        font-size: 18px;
        color: #d63031;
        margin-top: 40px;
    }
</style>

</head>
<body>

<h1>Lista copiilor înscriși pe evenimente</h1>

<?php if (!empty($evenimente)): ?>
    <?php foreach ($evenimente as $id => $info): ?>
        <div class="eveniment">
            <h2><?= htmlspecialchars($info['denumire']) ?></h2>
            <p><strong>Locuri totale:</strong> <?= $info['nr_total'] ?></p>
            <p><strong>Copii înscriși:</strong> <?= count($info['copii']) ?></p>
            <p><strong>Locuri rămase:</strong> <?= $info['nr_total'] - count($info['copii']) ?></p>

            <?php if (count($info['copii']) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nume copil</th>
                            <th>Nume părinte</th>
                            <th>Telefon părinte</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($info['copii'] as $copil): ?>
                            <tr>
                                <td><?= htmlspecialchars($copil['copil']) ?></td>
                                <td><?= htmlspecialchars($copil['parinte']) ?></td>
                                <td><?= htmlspecialchars($copil['telefon']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nu sunt copii înscriși la acest eveniment.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Nu există date de afișat.</p>
<?php endif; ?>

</body>
</html>

<?php
$conn->close();
?>
