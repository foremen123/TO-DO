<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/CSS/authorization.css">
    <title>Авторизация</title>
</head>
<body>
    <form action="/authorizationUser" method="post">
        <h1 class ='authorization'>Авторизация</h1>

        <?php if (!empty($error)): ?>
            <div class="error"><?=$error?>
                <br>
                <a href="/authorization">Зарегистрироваться</a>
            </div>
        <?php endif; ?>

        <label for="username">
            Имя пользователя:
            <input type="text" name="username" placeholder="Имя пользователя" id="username" required>
        </label>

        <label for="password">
            Ваш пароль:
            <input type="password" name="password" placeholder="Пароль пользователя" id="password" required>
        </label>

        <button type="submit">Тык</button>
    </form>
</body>
</html>