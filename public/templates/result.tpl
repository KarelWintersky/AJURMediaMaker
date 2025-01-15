{if $error}
    {$error_message}
{else}

{/if}
<a href="/download.php?id={$uuid}&ext={$dest_ext}">Скачать</a>
<hr>
{if $dest_ext eq 'jpg'}
    <img src="/storage/{$uuid}.{$dest_ext}" width="600" alt="{$uuid}">
{/if}


