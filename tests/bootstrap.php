<?php

declare(strict_types=1);

require __DIR__ . '/../src/Support/autoload.php';

use App\Core\Database;

Database::setPdo(null);
