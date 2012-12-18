<!DOCTYPE html>

<html lang="pt-Br">
    <head>
        <meta charset="utf-8" />
        <title>
            Fórum
            <?php if ($page > 1): ?>
            - Página <?php echo $page ?>
            <?php endif ?>
        </title>
    </head>
    <body>

<form method="post" action="/new-thread">
    <textarea name="message" placeholder="Mensagem"></textarea>
    <button type="submit">Enviar</button>
</form>

<ul>
<?php foreach ($threads as $thread): ?>
    <li>
        <a href="#">
            <?php echo $thread->message ?>
        </a>
    </li>
<?php endforeach ?>
</ul>



    </body>
</html>
