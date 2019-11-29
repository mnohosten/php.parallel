<?php
declare(strict_types=1);

use parallel\Channel;
use parallel\Runtime;

$host = 'mysql';
$db   = 'employees';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->exec('SET GLOBAL query_cache_limit = 0');
    $pdo->exec('SET GLOBAL query_cache_size = 0');
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

class A {
    function foo() { echo "foo!"; }
}

$fastQuery = function(int $a)  {
    return $a;
//    return $pdo->query('select * from salaries limit 10')->fetchAll();
};
$slowQuery = function(PDO $pdo) {
    return $items = $pdo->query('select * from salaries order by rand() limit 10')->fetchAll();
};

$runtime = new Runtime();

$a = new A;
$future = $runtime->run($fastQuery, [12, $a]);
var_dump($future->value());

