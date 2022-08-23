<?php

declare(strict_types=1);

namespace WikiLogAnalysis;

use PDO;
use PDOStatement;

require_once __DIR__ . '/vendor/autoload.php';

class DB
{
    // private const LOGFILE = __DIR__ . '/log';
    private const COUNT_MAX_VIEWS_QUERY =
    'SELECT domain_code, page_title, count_views
      FROM pageviews
     ORDER BY count_views DESC
     LIMIT :number';

    private const COUNT_TOTAL_VIEWS_QUERY =
    'SELECT COUNT(count_views), domain_code
      FROM pageviews WHERE ';
    private PDO $pdo;
    private PDOStatement $stmt;
    public function __construct()
    {
        try {
            $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
            $dotenv->load();
            $dotenv->required(['DB_DSN', 'DB_USER', 'DB_PASSWORD']);
        } catch (\Exception $e) {
            echo 'Debug: ' . $e->getMessage() . PHP_EOL;
            exit;
        }

        try {
            $this->pdo = new PDO($_ENV['DB_DSN'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        } catch (\PDOException $e) {
            // error_log($e->getMessage() . PHP_EOL, 3, self::LOGFILE);
            echo 'Debug: ' . $e->getMessage() . PHP_EOL;
            exit;
        }

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
    private function prepareMaxViews(): void
    {
        $this->stmt = $this->pdo->prepare(self::COUNT_MAX_VIEWS_QUERY);
    }
    private function prepareCountViews(int $numberOfDomainCodes): void
    {
        $query = '';
        for ($i = 0; $i < $numberOfDomainCodes - 1; $i++) {
            $query .= 'domain_code = ? OR ';
        }
        $query .= 'domain_code = ? ';
        $query .= 'GROUP BY domain_code';

        $this->stmt = $this->pdo->prepare(self::COUNT_TOTAL_VIEWS_QUERY . $query);
    }
    public function execNumber(int $number): void
    {
        $this->prepareMaxViews();
        $this->stmt->bindValue(':number', $number, PDO::PARAM_INT);
        $this->stmt->execute();
    }
    public function execDomainCode(int $numberOfDomainCodes, array $domainCodes): void
    {
        $this->prepareCountViews($numberOfDomainCodes);
        $this->stmt->execute($domainCodes);
    }
    public function fetch(): mixed
    {
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function fetchAll(): mixed
    {
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
