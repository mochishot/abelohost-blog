{extends file="layout.tpl"}
{block name="content"}
    <section class="error-page">
        <p class="eyebrow">{$status}</p><h1>{$title}<span class="accent">.</span></h1>
        <p class="lead">{$message}</p><a class="button" href="/">Back to the journal ↗</a>
    </section>
{/block}
