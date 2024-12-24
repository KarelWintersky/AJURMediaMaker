<a href="/download.php?id={$uuid}">Скачать</a>
<hr>
{if $is_image}
<!-- так не работает, нужен не путь а URL -->
    <img src="/storage/{$uuid}.jpg" width="600" alt="{$uuid}">
{/if}


