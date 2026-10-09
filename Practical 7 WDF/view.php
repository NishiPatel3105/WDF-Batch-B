<?php
$file = __DIR__ . "/students.csv";
$records = [];
if (is_file($file) && is_readable($file)) {
    $handle = fopen($file, "r");

    if ($handle !== false) {
        while (($row = fgetcsv($handle)) !== false) {
            $records[] = $row;
        }
        fclose($handle);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Students</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th,
        td {
            border: 1px solid lightsteelblue;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: midnightblue;
            color: white;
        }
        tr:nth-child(even) {
            background-color: aliceblue;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registered Students</h1>
        <?php if (count($records) <= 1): ?>
            <p>No student records found yet.</p>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <?php foreach ($records[0] as $heading): ?>
                                <th>
                                    <?= htmlspecialchars($heading, ENT_QUOTES, "UTF-8") ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php for ($i = 1; $i < count($records); $i++): ?>

                            <tr>
                                <?php foreach ($records[$i] as $value): ?>
                                    <td>
                                        <?= htmlspecialchars($value, ENT_QUOTES, "UTF-8") ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                        <?php endfor; ?>
                    </tbody>
                </table>

            </div>

        <?php endif; ?>

        <a class="view-link" href="register.html">
            Back to Registration
        </a>

    </div>

</body>
</html>