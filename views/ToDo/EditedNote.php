<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/CSS/EditedNote.css">
    <title>Изменение вашей заметки</title>
</head>
<body>
<div class="edit-note-container">
    <h1>Что-бы вы хотели изменить</h1>

    <form action="/editNote" method="post">
        <input type="hidden" name="id" value="<?=$note['id'] ?? null?>">

        <label for="editedNote">Текст заметки</label>
        <textarea
                name="editedNote"
                id="editedNote"
                placeholder="Введите текст вашей заметки..."
                required
        ><?=htmlspecialchars($note['note']) ?? ''?></textarea>

        <div class="buttons-row">
            <button type="submit" name="button">Изменить</button>
            <a href="ToDo.php" class="btn-back">Обратно</a>
        </div>
    </form>
</div>
</body>
</html>