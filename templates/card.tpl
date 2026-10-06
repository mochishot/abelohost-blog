<article class="card">
    <a class="card-link" href="/article/{$post.id}{$articleQuery}" aria-label="{$post.title}">
        <div class="card-image">
            <img src="{$post.image}" alt="" width="960" height="640" loading="lazy">
        </div>
        <div class="meta">
            <time datetime="{$post.published_at|date_format:'%Y-%m-%d'}">{$post.published_at|date_format:'%d %b %Y'}</time>
            <span>{$post.views|number_format:0:'.':','} views</span>
        </div>
        <h3>{$post.title}</h3>
        <p>{$post.description}</p>
    </a>
</article>
