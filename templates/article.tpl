{extends file="layout.tpl"}
{block name="content"}
    <article class="article">
        <div class="article-heading">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="/">Journal</a>
                {if $backUrl !== '/'}<span aria-hidden="true"> / </span><a href="{$backUrl}">{$backLabel}</a>{/if}
                <span aria-hidden="true"> / </span><span aria-current="page">Article</span>
            </nav>
            <div class="tags">
                {foreach $postCategories as $category}
                    <a href="/category/{$category.id}">{$category.name}</a>
                {/foreach}
            </div>
            <h1>{$post.title}</h1>
            <p class="lead">{$post.description}</p>
            <div class="meta">
                <time datetime="{$post.published_at|date_format:'%Y-%m-%d'}">{$post.published_at|date_format:'%d %b %Y'}</time>
                <span>{$post.views|number_format:0:'.':','} views</span>
            </div>
        </div>
        <img class="article-image" src="{$post.image}" alt="Illustration accompanying {$post.title}" width="960" height="640">
        <div class="article-body">
            {foreach $paragraphs as $paragraph}
                <p>{$paragraph}</p>
            {/foreach}
        </div>
        <a class="text-link article-back" href="{$backUrl}">← Back to {$backLabel}</a>
    </article>
    {if $related}
        <section class="category-section related" aria-labelledby="related-heading">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Keep exploring</p>
                    <h2 id="related-heading">A few more good reads</h2>
                </div>
            </div>
            <div class="card-grid">
                {foreach $related as $post}
                    {include file="card.tpl" post=$post}
                {/foreach}
            </div>
        </section>
    {/if}
{/block}
