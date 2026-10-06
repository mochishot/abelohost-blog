{extends file="layout.tpl"}
{block name="content"}
    <div class="page-intro">
        <a class="breadcrumb" href="/">Journal /</a>
        <p class="eyebrow">A different point of view</p>
        <h1>{$category.name}<span class="accent">.</span></h1>
        <p class="lead">{$category.description}</p>
    </div>
    <div class="toolbar">
        <p>{$total} {if $total === 1}article{else}articles{/if}</p>
        <form method="get" action="/category/{$category.id}">
            <label for="sort">Sort by</label>
            <select name="sort" id="sort">
                <option value="date" {if $sort === 'date'}selected{/if}>Latest published</option>
                <option value="views" {if $sort === 'views'}selected{/if}>Most viewed</option>
            </select>
            <button type="submit">Apply</button>
        </form>
    </div>
    <div class="card-grid category-grid">
        {foreach $posts as $post}
            {include file="card.tpl" post=$post}
        {foreachelse}
            <p class="empty">No stories here yet. There is more to discover in the journal.</p>
        {/foreach}
    </div>
    {if $pages > 1}
        <nav class="pagination" aria-label="Article pages">
            {if $page > 1}
                <a href="/category/{$category.id}?sort={$sort}&amp;page={$page - 1}" rel="prev">← Previous</a>
            {else}
                <span>← Previous</span>
            {/if}
            <div class="page-links">
                {if $pageStart > 1}
                    <a href="/category/{$category.id}?sort={$sort}&amp;page=1" aria-label="Page 1">1</a>
                    {if $pageStart > 2}<span aria-hidden="true">…</span>{/if}
                {/if}
                {for $number=$pageStart to $pageEnd}
                    {if $number === $page}
                        <span aria-current="page" aria-label="Page {$number}">{$number}</span>
                    {else}
                        <a href="/category/{$category.id}?sort={$sort}&amp;page={$number}" aria-label="Page {$number}">{$number}</a>
                    {/if}
                {/for}
                {if $pageEnd < $pages}
                    {if $pageEnd < $pages - 1}<span aria-hidden="true">…</span>{/if}
                    <a href="/category/{$category.id}?sort={$sort}&amp;page={$pages}" aria-label="Page {$pages}">{$pages}</a>
                {/if}
            </div>
            {if $page < $pages}
                <a href="/category/{$category.id}?sort={$sort}&amp;page={$page + 1}" rel="next">Next →</a>
            {else}
                <span>Next →</span>
            {/if}
        </nav>
    {/if}
{/block}
