<?php
if (basename(__FILE__) == basename($_SERVER["PHP_SELF"])) {
    http_response_code(403);
    exit("Access denied.");
}

final class Database
{
    private static ?PDO $pdo = null;

    // Adjust DB name as needed
    private const DB_HOST = '172.19.0.3';
    private const DB_NAME = 'sipilik';
    private const DB_USER = 'admin';
    private const DB_PASS = 'P@ssw0rdP@ssw0rd!';

    private function __construct() {}
    private function __clone() {}
    public function __wakeup() { throw new Exception('Cannot unserialize singleton'); }

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $dsn = 'mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4';

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => true, // <- persistent connection
        ];

        try {
            self::$pdo = new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
        } catch (PDOException $e) {
            // Log detailed error, show generic message
            $logDir = __DIR__ . '/../logs';
            if (!is_dir($logDir)) { @mkdir($logDir, 0750, true); }
            error_log('[' . date('c') . '] DB connection failed: ' . $e->getMessage() . PHP_EOL, 3, $logDir . '/error.log');
            exit('Database connection error.');
        }

        return self::$pdo;
    }

    public static function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public static function begin(): void { self::pdo()->beginTransaction(); }
    public static function commit(): void { if (self::pdo()->inTransaction()) self::pdo()->commit(); }
    public static function rollback(): void { if (self::pdo()->inTransaction()) self::pdo()->rollBack(); }
}