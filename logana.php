<?php

declare(strict_types=1);

namespace WikiLogAnalysis;

require_once __DIR__ . '/DB.php';

class Main
{
    private const EXIT_TELL_USAGE = 2;
    private const EXIT_INVALID_DOMAIN_NAME = 15;
    private array $argv;
    private int $argc;
    private DB $db;

    public function __construct(array $argv, DB $db)
    {
        $this->argv = $argv;
        $this->argc = count($argv);
        $this->db = $db;
    }
    private function tellUsage()
    {
        echo 'Usage: php' . basename(__FILE__) . ' <number|domain_code|domain_codeList...>' . PHP_EOL;
        exit(self::EXIT_TELL_USAGE);
    }
    private function tellInvalidDomainCode($arguments)
    {
        if (count($arguments) === 1) {
            echo $arguments . 'is invalid domain_code' . PHP_EOL;
        } elseif (is_array($arguments)) {
            echo implode(' ', $arguments) . ' are invalid domain_codes' . PHP_EOL;
        }
        exit(self::EXIT_INVALID_DOMAIN_NAME);
    }
    public function exec()
    {
        if ($this->argc === 1) {
            echo $this->tellUsage() . PHP_EOL;
        } elseif ($this->argc === 2) {
            // specify number
            if (is_numeric($this->argv[1])) {
                $this->db->execNumber((int) $this->argv[1]);
                while ($row = $this->db->fetch()) {
                    echo "\"{$row['domain_code']}\", \"{$row['page_title']}\", {$row['count_views']}" . PHP_EOL;
                }
            } else {
                // specify domainCode only one
                if (preg_match('/\A[a-zA-Z\.]+\z/', $this->argv[1])) {
                    $this->db->execDomainCode($this->argc - 1 , [$this->argv[1]]);
                    $countView = $this->db->fetch();
                    if (empty($countView)) {
                        $this->tellInvalidDomainCode($this->argv[1]);
                    }
                    echo "\"{$this->argv[1]}\", {$countView['COUNT(count_views)']}" . PHP_EOL;
                } else {
                    $this->tellUsage();
                }
            }
        } else {
            $arguments = array_slice($this->argv, 1);
            $numberOfDomainCodes = count($arguments);
            $validDomainCodes = array_filter($arguments, function ($argument) {
                return preg_match('/\A[a-zA-Z\.]+\z/', $argument);
            });
            if ($numberOfDomainCodes === count($validDomainCodes)) {
                $this->db->execDomainCode($numberOfDomainCodes, $arguments);
                $countViews = $this->db->fetchAll();
                if (empty($countViews)) {
                    $this->tellInvalidDomainCode($arguments);
                }
                // todo:php logana.php ja cn zn br pa fpa sp ja.m
                // DB取得を先に、値が数値となるようにしたら、できた、
                // arsort($countViews, SORT_NATURAL|SORT_NUMERIC);
                arsort($countViews);
                foreach ($countViews as $countView) {
                    echo "\"{$countView['domain_code']}\", {$countView["COUNT(count_views)"]}" . PHP_EOL;
                }
            } else {
                $this->tellUsage();
            }
        }
    }
}


$logana = new Main($argv, new DB());
$logana->exec();
