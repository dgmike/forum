<!DOCTYPE html>

<html lang="pt-Br">
    <head>
        <meta charset="utf-8" />
        <title>
            Fórum
            -
            Black List
        </title>
    </head>
    <body>

<a href="/blacklist" style="float:right">blacklist</a>
<h1>
    <a href="/">Fórum</a>
</h1>

<hr />

<p>
    <a href="/blacklist/regenerate">Compilar registros</a>
    <?php if ($regenerated): ?>
    &bullet; Gerado com sucesso!
    <?php endif ?>
</p>

<hr />


<div style="width: 500px;float:left;">
    <h2>Blacklist</h2>
    <form action="/blacklist" method="post">
        <input type="text" name="word" maxlength="50" placeholder="Palavra" autocomplete="off" />
        <button type="submit">adicionar</button>
    </form>
    <ul>
        <?php foreach($words as $word): ?>
        <li>
            <?php echo $word['word'] ?>
            <form action="/blacklist/remove" method="post" style="display:inline">
                <input type="hidden" name="word" value="<?php echo $word['word'] ?>" />
                <button>remover</button>
            </form>
        </li>
        <?php endforeach ?>
    </ul>
</div>
<div style="width: 500px;float:left;">
    <h2>Micro Replacer</h2>
    <form action="/blacklist" method="post">
        <input type="text" name="letter_in" placeholder="Letra de entrada" maxlength="1"  autocomplete="off" />
        <input type="text" name="letter_out" placeholder="Letra de substituição" maxlength="1"  autocomplete="off" />
        <button type="submit">adicionar</button>
    </form>
    <ul>
        <?php foreach($letters as $letter): ?>
        <li>
            <?php echo $letter['letter_in'] ?>
            &rarr;
            <?php echo $letter['letter_out'] ?>
            <form action="/blacklist/remove" method="post" style="display:inline">
                <input type="hidden" name="letter_in" value="<?php echo $letter['letter_in'] ?>" />
                <input type="hidden" name="letter_out" value="<?php echo $letter['letter_out'] ?>" />
                <button>remover</button>
            </form>
        </li>
        <?php endforeach ?>
    </ul>
</div>


    </body>
</html>
