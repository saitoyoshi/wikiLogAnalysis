<?php

declare(strict_types=1);

namespace WikiLogAnalysis;

use PDO;
use PDOStatement;

require_once __DIR__ . '/vendor/autoload.php';

class DB
{
    // private string $dns;
    // private string $username;
    // private string $password;
    private const OUTPUT_NUMBER_QUERY = 'SELECT domain_code, page_title, count_views FROM pageviews ORDER BY count_views DESC LIMIT :number';
    private const COUNT_VIEWS_QUERY = 'SELECT COUNT(count_views) FROM pageviews WHERE domain_code = :domainCode';
    private PDO $pdo;
    private PDOStatement $stmt;
    public function __construct()
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        $this->pdo = new PDO($_ENV['DB_DSN'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
    private function prepareMaxViews(): void
    {
        $this->stmt = $this->pdo->prepare(self::OUTPUT_NUMBER_QUERY);
    }
    private function prepareCountViews(): void
    {
        $this->stmt = $this->pdo->prepare(self::COUNT_VIEWS_QUERY);
    }
    public function execNumber(int $number): void
    {
        $this->prepareMaxViews();
        $this->stmt->bindValue(':number', $number, PDO::PARAM_INT);
        $this->stmt->execute();
    }
    public function execDomainCode(string $domainCode)
    {
        $this->prepareCountViews();
        $this->stmt->bindValue(':domainCode', $domainCode, PDO::PARAM_STR);
        $this->stmt->execute();
    }
    public function fetch(): mixed
    {
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }
}

$db = new DB();
$db->execNumber(2);
while ($r = $db->fetch()) {
    var_dump($r);
}
