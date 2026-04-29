<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Étudiants</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            padding: 30px;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
            font-size: 28px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .table-header {
            background-color: #2c3e50;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header span {
            font-size: 14px;
            opacity: 0.8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background-color: #3498db;
            color: white;
            padding: 14px 16px;
            text-align: left;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid #ecf0f1;
            transition: background 0.2s;
        }

        tbody tr:hover {
            background-color: #eaf4fc;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:nth-child(even):hover {
            background-color: #eaf4fc;
        }

        td {
            padding: 12px 16px;
            color: #34495e;
            font-size: 14px;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-B  { background-color: #d5f5e3; color: #1e8449; }
        .badge-AB { background-color: #d6eaf8; color: #1a5276; }
        .badge-P  { background-color: #fef9e7; color: #b7950b; }
        .badge-Aj { background-color: #fdecea; color: #c0392b; }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #95a5a6;
            font-style: italic;
        }
    </style>
</head>
<body>

    <h1>📋 Liste des Étudiants</h1>

    <div class="container">
        <div class="table-header">
            <strong>Tableau des étudiants</strong>
            <span><?= count($etudiants) ?> étudiant(s) trouvé(s)</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Nom</th>
                    <th>ID Option</th>
                    <th>ID Semestre</th>
                    <th>ID Matière</th>
                    <th>Note</th>
                    <th>Crédit</th>
                    <th>Résultat</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($etudiants)) : ?>
                    <?php foreach ($etudiants as $etudiant) : ?>
                        <tr>
                            <td><?= esc($etudiant['id']) ?></td>
                            <td><?= esc($etudiant['nom']) ?></td>
                            <td><?= esc($etudiant['id_option']) ?></td>
                            <td><?= esc($etudiant['id_semestre']) ?></td>
                            <td><?= esc($etudiant['id_matiere']) ?></td>
                            <td><?= esc($etudiant['note']) ?> / 20</td>
                            <td><?= esc($etudiant['credit']) ?></td>
                            <td>
                                <span class="badge badge-<?= esc($etudiant['resultat']) ?>">
                                    <?= esc($etudiant['resultat']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="8" class="no-data">Aucun étudiant trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>