{extends file="layout.tpl"}
{block name="content"}
    <section class="hero">
        <div>
            <p class="eyebrow"><span class="dot"></span> The everyday, seen differently</p>
            <h1>Good stories.<br>Fresh <em>perspectives.</em></h1>
            <p class="hero-description">Notes on thoughtful design, interesting places and the small things that make a life.</p>
            <a class="button" href="#latest">Explore the journal <span aria-hidden="true">↗</span></a>
        </div>
        <div class="hero-art" aria-hidden="true">
            <img src="/assets/images/canal.svg" alt="" width="960" height="640">
            <span class="art-label">A different light on the everyday</span>
        </div>
    </section>
    <div id="latest" class="section-kicker"><span>The journal</span><span>Worth a closer look ↓</span></div>
    {foreach $categories as $category}
        {if isset($groups[$category.id])}
            <section class="category-section" aria-labelledby="category-{$category.id}">
                <div class="section-heading">
                    <div>
                        <h2 id="category-{$category.id}">{$category.name}</h2>
                        <p>{$category.description}</p>
                    </div>
                    <a class="text-link" href="/category/{$category.id}">All articles <span aria-hidden="true">↗</span></a>
                </div>
                <div class="card-grid">
                    {foreach $groups[$category.id] as $post}
                        {include file="card.tpl" post=$post}
                    {/foreach}
                </div>
            </section>
        {/if}
    {/foreach}
    {if !$groups}
        <p class="empty">Our first stories are on their way. Come back soon.</p>
    {/if}
{/block}
