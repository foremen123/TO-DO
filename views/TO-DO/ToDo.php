<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Заметки</title>
</head>
<body>
    <table>
        <tr id="Label">
            Ваши Заметки
        </tr>

        <tr>
            <td id="note_st">
                <form method="post" action="">
                    <label for="note">
                        Добавление заметок
                        <br>
                        <textarea rows="4" cols="50" name="note" id="note"></textarea>
                        <button type="submit">Создать</button>
                    </label>
                </form>
            </td>
        </tr>

        <tr>
            <td>Здесь будет логика показа всех заметок</td>
        </tr>

    </table>
</body>
</html>