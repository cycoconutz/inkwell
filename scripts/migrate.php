<?php
declare(strict_types=1);
require __DIR__ . "/../vendor/autoload.php";
use App\Core\Database;
use App\Support\Env;
Env::load(__DIR__ . "/../.env");
Database::configure([
  "host" => env("DB_HOST","db"),
  "port" => (int)env("DB_PORT",3306),
  "database" => env("DB_DATABASE","inkwell"),
  "username" => env("DB_USERNAME","inkwell"),
  "password" => env("DB_PASSWORD","inkwell"),
  "charset" => "utf8mb4",
]);
$sql = file_get_contents(__DIR__ . "/../database/schema.sql");
if ($sql === false) { fwrite(STDERR,"err\n"); exit(1); }
Database::pdo()->exec($sql);
echo "ok\n";
