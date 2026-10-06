<?php

declare(strict_types=1);

$db = require dirname(__DIR__) . '/src/database.php';

if ($db->query('SELECT (SELECT COUNT(*) FROM categories) + (SELECT COUNT(*) FROM posts)')->fetchColumn() > 0) {
    fwrite(STDERR, "Seeding requires an empty database. Existing data was left untouched.\n");
    exit(1);
}

$categories = [
    ['Design', 'Thoughtful objects, useful ideas and spaces that feel right.'],
    ['Places', 'Slow journeys, local discoveries and a sense of somewhere.'],
    ['Everyday', 'Small rituals and a little more attention to ordinary life.'],
];

// Each entry contains an image, title, description and plain-text body.
$posts = [
    [
        'still-life', 'The quiet appeal of everyday objects',
        'A good jug, a well-worn bowl, a favourite cup. Why the things we keep matter.',
        "The jug on the kitchen table has a slightly uneven rim. It pours perfectly, but that small irregularity is the first thing you notice when you pick it up. Over time, it has become the reason to choose it over the others.\n\nUseful objects earn their place through repetition. Before buying something new, spend a week noticing what you already reach for. The answer often tells you more about good design than a showroom can.",
    ],
    [
        'interior', 'A room with space to breathe',
        'Making a home feel generous does not always mean adding more.',
        "Moving a chair away from the window changed the whole room. Light reached the floor again, and there was somewhere to stand with a cup of tea. Nothing had been bought; one thing had simply moved.\n\nTry starting with the paths you take through a room. Clear those first, then arrange the furniture around what you actually do there. A comfortable room makes its purpose easy to understand.",
    ],
    [
        'print', 'What a printed page can teach us',
        'Margins, rhythm and the pleasure of giving an idea enough room.',
        "A small exhibition catalogue can feel surprisingly spacious. A generous margin separates the image from the edge, a short caption sits close to what it describes, and the next page arrives without a struggle.\n\nThese decisions translate well to a screen. Keep related things together. Let headings describe what follows. Leave room between separate ideas. The reader should spend attention on the story, rather than on finding the next line.",
    ],
    [
        'still-life', 'Materials that get better with time',
        'Looking beyond a perfect finish to the way an object will age.',
        "The edge of an oak table becomes smoother where people rest their arms. A brass handle darkens around the places nobody touches. These changes are part of an object's working life, not necessarily signs that it needs replacing.\n\nWhen choosing a material, ask how it can be cleaned and repaired. A finish that allows a small repair can be more useful than one that looks flawless on the day it arrives.",
    ],
    [
        'interior', 'Reading a room through its light',
        'Before choosing a colour, spend a day watching the windows.',
        "A wall that looks warm at breakfast may look almost grey by late afternoon. The direction of a window, the buildings opposite and even the colour of the floor all change what we see.\n\nTape a large colour sample to two different walls and leave it there for several days. Look at it under the lamps you use in the evening. Choosing slowly is easier than repainting a room that only looked right at noon.",
    ],
    [
        'print', 'Less, but easier to use',
        'Simplicity starts with understanding what someone needs to do.',
        "Removing a label can make a control look cleaner and make its purpose harder to guess. A simpler appearance is not always a simpler experience. The useful question is whether someone can finish what they came to do.\n\nStart with a single task and follow every step. Keep the information needed for that task close to the action. Remove a choice only when it does not help. Clarity is something a person experiences, not something a layout declares.",
    ],
    [
        'canal', 'Amsterdam, one side street at a time',
        'A walk without a checklist, just beyond the busiest canals.',
        "Turn away from a busy crossing and the city changes scale. Bicycles lean against narrow façades, a delivery boat passes below street level, and a bench catches a patch of morning sun. The details become easier to see when there is no next stop to reach.\n\nChoose a short stretch of canal and walk both sides. The same houses look different across the water. Leave enough time to stop, and keep cycle lanes clear while you look around.",
    ],
    [
        'coast', 'A slower afternoon by the North Sea',
        'Wind, wide horizons and the simple pleasure of an unhurried walk.',
        "The beach offers very little to organise. There is a line of water, a line of dunes and a changing strip of sand between them. Walking into the wind makes the return journey feel like a small gift.\n\nTake a layer you can put on when you stop and check local conditions before setting out. Follow marked paths through the dunes. A short visit can be enough; there is no need to turn every outing into an expedition.",
    ],
    [
        'canal', 'The best route is not always the shortest',
        'Taking the long way home can make a familiar neighbourhood feel new.',
        "The direct route home follows three wide streets. The longer one passes a small garden, a bridge and a bakery with its door open. It adds ten minutes and changes the feeling of the whole afternoon.\n\nPick one familiar journey and alter a single section. You do not need an unfamiliar city to notice something new. Repeating the new route across a few weeks reveals changes a one-off visit would miss.",
    ],
    [
        'coffee', 'Finding a café worth staying in',
        'Beyond the menu: the small details that make a place welcoming.',
        "A good café has somewhere to put a coat, a table that does not wobble and enough light to read. These are modest details, but together they make staying feel easy. The most memorable room is not always the most photographed one.\n\nArrive outside the busiest hour and notice how the space works. Choose something from the menu, put the phone away for a moment, and let the neighbourhood come to you through the window.",
    ],
    [
        'coast', 'Packing less for a day away',
        'A smaller bag leaves more room for the journey itself.',
        "A day trip rarely needs a bag prepared for every possible version of the day. Water, a useful extra layer and a little food take care of most of the practical questions. The rest depends on where you are going.\n\nLay everything out before packing. Remove the items that duplicate another job, then check the forecast and your route. The aim is not the smallest possible bag; it is a bag you stop thinking about once you leave.",
    ],
    [
        'canal', 'Why we return to the same places',
        'Familiarity has its own kind of discovery.',
        "On a second visit, finding the way takes less effort. You notice a doorway instead of a street sign, or remember where the late sun reaches a terrace. A familiar place offers a different kind of attention.\n\nReturn at another time of day or in another season. Keep one part of the visit the same and let the rest change. Repetition can make a journey richer without making it longer.",
    ],
    [
        'coffee', 'A morning worth slowing down for',
        'One small ritual before the day begins asking for your attention.',
        "Boiling water creates a short pause that is easy to fill with a screen. Leave the phone where it is and use that minute to open a window or look outside. The tea will take the same amount of time either way.\n\nA morning ritual does not need an elaborate routine. Choose one thing you already do and give it your attention. Keep it small enough that it still fits on an ordinary, slightly hurried Tuesday.",
    ],
    [
        'still-life', 'The pleasure of fixing one small thing',
        'A loose handle and twenty minutes can change how a home feels.',
        "The cupboard handle had been loose for months. Tightening two screws took less time than making lunch, but opening the door felt different afterwards. A small irritation had stopped asking for attention.\n\nKeep a short list of these jobs and choose one at a time. Work within your skills and use the right tools. Finishing one manageable repair is often more satisfying than planning a whole weekend of improvements.",
    ],
    [
        'print', 'Keeping a notebook without a system',
        'A place for unfinished thoughts does not need to be perfectly organised.',
        "The first page does not need a title. Write the date, a sentence overheard on a train, or the name of a place you want to remember. A notebook starts working when there is something in it.\n\nLeave room for untidy entries and ideas that never become anything else. If finding things becomes difficult, number the pages and add a simple index at the back. Let the structure follow the use.",
    ],
    [
        'interior', 'Making a corner for reading',
        'Good light, a comfortable seat and a book within reach.',
        "A reading corner can be an existing chair moved nearer a lamp. Add a place for a cup and keep a book where you can see it. The invitation matters more than whether the furniture matches.\n\nSit there for ten minutes before rearranging anything else. Notice where your feet rest and whether the light falls on the page. Small adjustments made while using the space are more useful than decisions made from across the room.",
    ],
    [
        'coast', 'An hour outside, without a destination',
        'Leaving a little space in the day for whatever you happen to notice.',
        "A walk can be a gap between two tasks rather than another task to complete. Start from the door and choose a direction. When you are not trying to cover a particular distance, stopping becomes part of the walk.\n\nNotice one thing that has changed since you last passed: a tree, a shop window, the light on a wall. An ordinary route contains enough variation to reward a little attention.",
    ],
    [
        'coffee', 'A table for a few friends',
        'An easy meal leaves more time for the people around it.',
        "A pot in the middle of the table gives everyone something to share and the host somewhere to sit. The meal does not need several courses to feel generous. Enough food, water within reach and time to talk do most of the work.\n\nChoose a dish you have cooked before and prepare what you can in advance. Ask about dietary needs when you invite people. Once everyone arrives, let the conversation set the pace.",
    ],
];

$db->beginTransaction();
$insertCategory = $db->prepare('INSERT INTO categories (name, description) VALUES (?, ?)');
$categoryIds = [];
foreach ($categories as [$name, $description]) {
    $insertCategory->execute([$name, $description]);
    $categoryIds[] = (int) $db->lastInsertId();
}

$insertPost = $db->prepare('
    INSERT INTO posts (image, title, description, body, published_at, views) VALUES (?, ?, ?, ?, ?, ?)
');
$attachCategory = $db->prepare('INSERT INTO category_post (category_id, post_id) VALUES (?, ?)');
$publishedAt = new DateTimeImmutable('yesterday 12:00', new DateTimeZone('UTC'));
$secondaryCategories = [
    0 => 2, // Everyday objects → Everyday
    1 => 2, // Living spaces → Everyday
    3 => 2, // Ageing materials → Everyday
    6 => 0, // Amsterdam architecture → Design
    8 => 2, // Daily routes → Everyday
    9 => 0, // Café interiors → Design
    14 => 0, // Notebooks → Design
    15 => 0, // Reading corners → Design
    16 => 1, // Neighbourhood walks → Places
];

foreach ($posts as $index => [$image, $title, $description, $body]) {
    $insertPost->execute([
        '/assets/images/' . $image . '.svg', $title, $description, $body,
        $publishedAt->modify('-' . $index . ' days')->format('Y-m-d H:i:s'),
        (($index * 137) + 53) % 1800,
    ]);
    $postId = (int) $db->lastInsertId();
    $categoryIndex = intdiv($index, 6);
    $attachCategory->execute([$categoryIds[$categoryIndex], $postId]);
    if (isset($secondaryCategories[$index])) {
        $attachCategory->execute([$categoryIds[$secondaryCategories[$index]], $postId]);
    }
}

$db->commit();
echo "Created 3 categories, 18 articles and 27 category assignments.\n";
