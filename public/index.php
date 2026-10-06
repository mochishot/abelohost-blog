<?php

declare(strict_types=1);

use App\BlogRepository;
use Smarty\Smarty;

require dirname(__DIR__) . '/vendor/autoload.php';

date_default_timezone_set('UTC');
header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'self'; img-src 'self'; style-src 'self'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'");
header('Cache-Control: no-store');

$view = new Smarty();
$view->setTemplateDir(dirname(__DIR__) . '/templates');
$view->setCompileDir(dirname(__DIR__) . '/var/smarty');
$view->setEscapeHtml(true);
$view->assign(['categories' => [], 'activeCategory' => null, 'articleQuery' => '']);

set_exception_handler(static function (Throwable $exception) use ($view): void {
    error_log((string) $exception);
    http_response_code(500);
    $view->assign(['title' => 'Something went wrong', 'message' => 'Please try again later.', 'status' => 500]);
    $view->display('error.tpl');
});

$error = static function (int $status, string $message) use ($view): never {
    http_response_code($status);
    $view->assign(['title' => $status === 404 ? 'Page not found' : 'Invalid request', 'message' => $message, 'status' => $status]);
    $view->display('error.tpl');
    exit;
};

$method = $_SERVER['REQUEST_METHOD'];
if (!in_array($method, ['GET', 'HEAD'], true)) {
    header('Allow: GET, HEAD');
    $error(405, 'This page only accepts GET and HEAD requests.');
}

$blog = new BlogRepository(require dirname(__DIR__) . '/src/database.php');
$categories = $blog->categories();
$view->assign('categories', $categories);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/') {
    $groups = [];
    foreach ($blog->homePosts() as $post) {
        $groups[$post['category_id']][] = $post;
    }
    $view->assign(['title' => 'Ideas for a more considered everyday', 'groups' => $groups]);
    $view->display('home.tpl');
} elseif (preg_match('~^/category/([1-9][0-9]*)$~D', $path, $matches)) {
    $category = null;
    foreach ($categories as $item) {
        if ((string) $item['id'] === $matches[1]) {
            $category = $item;
            break;
        }
    }
    if ($category === null) {
        $error(404, 'We could not find that category.');
    }
    $sort = $_GET['sort'] ?? 'date';
    $page = filter_var($_GET['page'] ?? '1', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!in_array($sort, ['date', 'views'], true) || $page === false) {
        $error(400, 'Choose a valid sort order and a positive page number.');
    }
    $total = $blog->categoryPostCount($category['id']);
    $perPage = 6;
    $pages = max(1, (int) ceil($total / $perPage));
    if ($page > $pages) {
        $error(404, 'There are no articles on this page.');
    }
    $view->assign([
        'title' => $category['name'],
        'category' => $category,
        'activeCategory' => $category['id'],
        'posts' => $blog->categoryPosts($category['id'], $sort, $perPage, ($page - 1) * $perPage),
        'sort' => $sort,
        'page' => $page,
        'pages' => $pages,
        'total' => $total,
        'articleQuery' => '?' . http_build_query(['category' => $category['id'], 'sort' => $sort, 'page' => $page]),
        'pageStart' => max(1, $page - 1),
        'pageEnd' => min($pages, $page + 1),
    ]);
    $view->display('category.tpl');
} elseif (preg_match('~^/article/([1-9][0-9]*)$~D', $path, $matches)) {
    $id = filter_var($matches[1], FILTER_VALIDATE_INT);
    $post = $id === false ? false : $blog->post($id);
    if ($post === false) {
        $error(404, 'We could not find that article.');
    }
    $postCategories = $blog->postCategories($id);
    $backUrl = '/';
    $backLabel = 'Journal';
    if (isset($_GET['category'])) {
        $categoryId = filter_var($_GET['category'], FILTER_VALIDATE_INT);
        $page = filter_var($_GET['page'] ?? '1', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $sort = $_GET['sort'] ?? 'date';
        $categoryNames = array_column($postCategories, 'name', 'id');
        if ($categoryId === false || !isset($categoryNames[$categoryId]) || $page === false || !in_array($sort, ['date', 'views'], true)) {
            $error(400, 'The article category or return page is invalid.');
        }
        $backUrl = '/category/' . $categoryId . '?' . http_build_query(['sort' => $sort, 'page' => $page]);
        $backLabel = $categoryNames[$categoryId];
        $view->assign('activeCategory', $categoryId);
    }
    if ($method === 'GET') {
        $blog->incrementViews($id);
        $post['views']++;
    }
    $view->assign([
        'title' => $post['title'],
        'post' => $post,
        'paragraphs' => explode("\n\n", $post['body']),
        'postCategories' => $postCategories,
        'backUrl' => $backUrl,
        'backLabel' => $backLabel,
        'related' => $blog->relatedPosts($id),
    ]);
    $view->display('article.tpl');
} else {
    $error(404, 'That page may have moved, or the address may be incorrect.');
}
