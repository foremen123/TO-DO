<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Отправка заметки</title>
    <link rel="stylesheet" href="/CSS/Email.css">
</head>
<body>
<div class="email-wrapper">
    <h1 class="email-title">Отправка заметки</h1>

    <form class="email-form" method="post" action="/mailer/queue">
        <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
        <label for="email">Ваша почта</label>
        <input type="email" name="email" id="email" placeholder="example@mail.com" required>

        <button type="submit">Отправить</button>
    </form>
</div>
</body>
</html>