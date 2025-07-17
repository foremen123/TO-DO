<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/CSS/ToDo.css">
    <title>Заметки</title>
</head>
<body>
<div class="notes-container" role="main">
    <div class="sort-wrapper">
        <form method="get" id="sortForm" aria-label="Сортировка заметок">
            <label for="sortSelect">Сортировка:</label>
            <select name="sort" id="sortSelect" onchange="document.getElementById('sortForm').submit();">
                <?php foreach ($sorts as $option): ?>
                    <option value="<?= $option->value ?>" <?= ($_GET['sort'] ?? '') === $option->value ? 'selected' : '' ?>>
                        <?= $option->label() ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
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

                    <div class="note-actions">
                        <form method="post" action="/doneNote">
                            <input type="hidden" name="id" value="<?= $note['id'] ?>">
                            <button type="submit" class="done-button" title="<?= $note['completed'] ? 'Отменить выполнение' : 'Отметить как выполненную' ?>">
                                <?= $note['completed'] ? '↩️' : '✅' ?>
                            </button>
                        </form>

                        <?php if (!$note['completed']): ?>
                            <form method="get" action="/getEditId">
                                <input type="hidden" name="id" value="<?= $note['id'] ?>">
                                <button type="submit" class="edit-button" title="Редактировать">✏️</button>
                            </form>
                        <?php endif; ?>

                            <form method="get" action="/email">
                                <input type="hidden" name="id" value="<?= $note['id'] ?? null ?>">
                                <button type="submit" class="sent-button" title="отправить на почту">📩</button>
                            </form>

                        <form method="post" action="/deleteNote">
                            <input type="hidden" name="id" value="<?= $note['id'] ?>">
                            <button type="submit" class="delete-button" title="Удалить">🗑️</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-notes" aria-live="polite">Заметок пока нет</div>
        <?php endif; ?>
        <div class="logout-container">
            <a href="/logOut" id="logOut">Выйти</a>
        </div>
    </div>
</div>
</body>
</html>
