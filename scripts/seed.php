<?php
declare(strict_types=1);
require __DIR__ . "/../vendor/autoload.php";
use App\Core\Database;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;
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
$email = (string)env("ADMIN_EMAIL","admin@inkwell.test");
$password = (string)env("ADMIN_PASSWORD","password");
if (User::findByEmail($email) === null) {
  User::create("Inkwell Admin",$email,$password,"admin");
  echo "created\n";
} else { echo "exists\n"; }
