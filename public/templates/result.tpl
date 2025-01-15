{if $error}
    {$error_message}
{else}

{/if}
<a href="/download.php?id={$uuid}&file={$dest_file}&ext={$dest_ext}">Скачать</a>
<hr>
{if $is_image}
    <img src="/storage/{$dest_file}" width="600" alt="{$uuid}">
{/if}
{if $is_video}
    <video style="max-width: 600px">
        <source src="{$domain}/storage/{$dest_file}" type="video/mp4">
    </video>
{/if}


