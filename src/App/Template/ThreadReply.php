<!DOCTYPE html>

<html lang="pt-Br">
    <head>
        <meta charset="utf-8" />
        <title>
            Fórum
            -
            <?php echo $header->message ?>
        </title>
    </head>
    <body>

<a href="/blacklist" style="float:right">blacklist</a>
<h1>
    <a href="/">Fórum</a>
    <em style="font:.6em/1.2em sans-serif;color:#555;display:block;"><?php echo $header->message ?></em>
</h1>

<hr />

<?php if ($reply_to->id_message != $data->thread_id): ?>
<p><?php echo $reply_to->message ?></p>
<?php endif ?>

<form action="/thread/<?php echo $data->thread_id ?>/<?php echo $data->previous_page ?>/reply/<?php echo $data->reply_to ?>" method="post">
    <textarea placeholder="Resposta:" name="answer"></textarea>
    <button type="submit">Responder</button>
</form>


    </body>
</html>

