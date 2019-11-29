<?php
declare(strict_types=1);

use parallel\Channel;
use parallel\Runtime;

require __DIR__ . '/../bootstrap.php';

$pdo = ContainerFactory::create()->get(PDO::class);
$slowQuery = function () use ($pdo) {
    echo "STANDARD: Load 20 items\n";
    return $pdo->query('select * from salaries order by rand() limit 20')->fetchAll();
};

$threadQuery = function (Channel $channel = null) {
    $pdo = ContainerFactory::create()->get(PDO::class);
    echo "THREAD: Load 20 items\n";
    $channel->send(
        $pdo->query('select * from salaries order by rand() limit 20')->fetchAll()
    );
};

// === Single thread

$start = microtime(true);

$payloadSingle = [
    $slowQuery(), $slowQuery()
];

$finalTime = microtime(true) - $start;
echo "Single thread time: {$finalTime}.\n";

// ==== Parallel

$start = microtime(true);

$runtime = new Runtime(__DIR__ . '/../bootstrap.php');
$runtime2 = new Runtime(__DIR__ . '/../bootstrap.php');
$channel = new Channel();

$runtime->run($threadQuery, [$channel]);
$runtime2->run($threadQuery, [$channel]);

$payloadThread = [
    $channel->recv(),
    $channel->recv(),
];

$channel->close();

$finalTime = microtime(true) - $start;
echo "Parallel: {$finalTime}.\n";

echo "Loaded payloads are equal: " . ($payloadSingle == $payloadThread) . "\n";