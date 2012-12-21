<!DOCTYPE html>

<html lang="pt-Br">
    <head>
        <meta charset="utf-8" />
        <title>
            Fórum
            -
            <?php echo $header->message ?>
            <?php if ($page > 1): ?>
            - Página <?php echo $page ?>
            <?php endif ?>
        </title>
    </head>
    <body>

<a href="/blacklist" style="float:right">blacklist</a>
<h1>
    <a href="/">Fórum</a>
    <em style="font:.6em/1.2em sans-serif;color:#555;display:block;"><?php echo $header->message ?></em>
</h1>

<hr />

<ul style="list-style:none;">
<?php foreach ($threads as $thread): ?>
    <li style="padding-left: <?php echo $thread['depth'] ?>em">
        <strong><?php echo $thread['slug'] ?></strong>
        <time><?php echo $thread['date_creation'] ?></time>
        &ndash;
        <?php echo $thread['message'] ?>
        <a href="/thread/<?php echo $thread_id ?>/<?php echo $page ?>/reply/<?php echo $thread['id_message'] ?>" class="reply">responder</a>
    </li>
<?php endforeach ?>
</ul>

<?php if ($pagination): ?>
    <p>[ <?php echo $pagination->displayPages() ?> ]</p>
<?php endif ?>


<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script>
$(function(){
    $('a.reply').click(function(){
            var _this = $(this)
        $('div.reply').remove();
        $('<div class="reply" />')
            .insertAfter(this)
            .html('carregando...')
            .load(this.href+' form', function(){
                $('div.reply textarea').focus();
                $('div.reply form').submit(function(){
                    var answer = $(this).find('textarea').val(),
                        action = this.action;
                    $('div.reply').css({
                        'border': '1px solid #DD9',
                        'background': '#FFE',
                        'padding': 10
                    })
                    $.post(action, { answer: answer }, function(data){
                        $('div.reply').html(data).delay(1000).fadeOut();
                        setInterval("window.location+=''", 5000)
                    });
                    return false;
                });
            });
        return false;
    });
});
</script>


    </body>
</html>
