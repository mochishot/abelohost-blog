<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$title} — Fieldnotes</title>
    <meta name="description" content="Fieldnotes is an independent journal about design, places and the everyday.">
    <link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/assets/app.css">
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>
    <header class="site-header container">
        <a class="wordmark" href="/" aria-label="Fieldnotes home">fieldnotes<span>✳</span></a>
        <nav aria-label="Main navigation">
            <a href="/" {if $smarty.server.REQUEST_URI === '/'}aria-current="page"{/if}>Journal</a>
            {foreach $categories as $navCategory}
                <a href="/category/{$navCategory.id}" {if $activeCategory === $navCategory.id}aria-current="page"{/if}>{$navCategory.name}</a>
            {/foreach}
        </nav>
        <span class="header-note">A little curiosity goes a long way.</span>
    </header>
    <main id="main" class="container" tabindex="-1">{block name="content"}{/block}</main>
    <footer class="site-footer container">
        <a class="wordmark" href="/">fieldnotes<span>✳</span></a>
        <p>An independent journal. A fresh perspective.</p>
        <a href="#main">Back to top ↑</a>
    </footer>
</body>
</html>
