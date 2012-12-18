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

<h1>
    <?php if ($page > 1): ?><a href="/"><?php endif ?>
        Fórum
    <?php if ($page > 1): ?></a><?php endif ?>
</h1>

<form method="post" action="/new-thread">
    <textarea name="message" placeholder="Criar nova mensagem"></textarea>
    <button type="submit">Enviar</button>
</form>

<hr />

<ul>
<?php foreach ($threads as $thread): ?>
    <li>
        <a href="/thread/<?php echo $thread['id_message'] ?>"
        ><?php echo $thread['message'] ?></a>
        &ndash;
        <time><?php echo $thread['date_creation'] ?></time>
    </li>
<?php endforeach ?>
</ul>

<?php if ($pagination): ?>
    <p>[ <?php echo $pagination->display_pages() ?> ]</p>
<?php endif ?>



    </body>
</html>
