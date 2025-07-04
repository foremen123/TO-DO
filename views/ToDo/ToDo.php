<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Заметки</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 2rem;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 800px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 0.75rem;
            text-align: left;
        }
        textarea {
            width: 100%;
        }
    </style>
</head>
<body>
<h2>Ваши Заметки: <?= htmlspecialchars($username ?? '') ?></h2>

<form method="post" action="/createNote">
    <label for="note">
        Добавление заметки:<br>
        <textarea rows="4" name="note" id="note"></textarea><br>
        <button type="submit">Создать</button>
    </label>
</form>

<h3>Список заметок:</h3>
<table>
    <thead>
    <tr>
        <th>Заметка</th>
        <th>Дата</th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($notes)): ?>
        <?php foreach ($notes as $note): ?>
            <tr>
                <td><?= htmlspecialchars($note['note']) ?></td>
                <td><?= htmlspecialchars($note['date']) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="2">Заметок пока нет</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
</body>
</html>