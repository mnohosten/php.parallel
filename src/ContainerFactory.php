<?php
declare(strict_types=1);

class ContainerFactory
{

    public static function create(): \Psr\Container\ContainerInterface
    {
        $container = new \League\Container\Container();
        self::registerPdo($container);
        return $container;
    }

    /**
     * @param \League\Container\Container $container
     */
    private static function registerPdo(\League\Container\Container $container): void
    {
        $container->share(PDO::class, function () {
            $host = 'mysql';
            $db = 'employees';
            $user = 'root';
            $pass = 'root';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            try {
                $pdo = new PDO($dsn, $user, $pass, $options);
                $pdo->exec('SET GLOBAL query_cache_limit = 0');
                $pdo->exec('SET GLOBAL query_cache_size = 0');
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
            return $pdo;
        });
    }

}