<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заметки</title>
    <link rel="stylesheet" href="/CSS/ToDo.css">
</head>
<body>
<div class="notes-container" role="main">
    <h2 class="notes-title">Ваши Заметки: <?= htmlspecialchars($username ?? '') ?></h2>

    <div class="form-wrapper">
        <form method="post" action="/createNote" aria-label="Форма создания новой заметки">
            <label for="note">Добавление заметки:</label>
            <textarea name="note" id="note" rows="4" placeholder="Введите вашу заметку..." required></textarea>
            <button type="submit">Создать</button>
        </form>
    </div>

    <div class="notes-list" id="notesList">
        <h3>Список заметок:</h3>
        <?php if (!empty($notes)): ?>
            <?php foreach ($notes as $note): ?>
                <div class="note-item <?= $note['completed'] ? 'completed' : '' ?>" tabindex="0">
                    <p class="note-text <?= $note['completed'] ? 'done-text' : '' ?>">
                        <?= htmlspecialchars($note['note']) ?>
                    </p>
                    <small class="note-date"><?= htmlspecialchars($note['date']) ?></small>

                    <form method="post" action="/doneNote">
                    <input type="hidden" name="id" value="<?= $note['id'] ?>">
                    <button type="submit" class="done-button" title="<?= $note['completed'] ? 'Отменить выполнение' : 'Отметить как выполненную' ?>">
                        <?= $note['completed'] ? '↩️' : '✅' ?>
                    </button>
                    </form>

                    <?php if (!$note['completed']): ?>
                        <form method="get" action="/getEditId">
                            <input type="hidden" name="id" value="<?= $note['id'] ?>">
                            <button type="submit" class="edit-button">✏️</button>
                        </form>
                    <?php endif; ?>

                    <form method="post" action="/deleteNote">
                        <input type="hidden" name="id" value="<?= $note['id'] ?>">
                        <button type="submit" class="delete-button">🗑️</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-notes" aria-live="polite">Заметок пока нет</div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
