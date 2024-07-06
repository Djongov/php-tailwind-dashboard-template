<?php declare(strict_types=1);

use Components\Html;
use App\Database\DB;
use App\Security\Firewall;
use Controllers\Api\Output;
use Components\Gallery;

// First firewall check
Firewall::activate();

// Admin check
if (!$isAdmin) {
    Output::error('You are not an admin', 403);
}

$dbTables = [];

$db = new DB();
$pdo = $db->getConnection();

// Check the database driver to determine the appropriate SQL syntax
$driver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);

switch ($driver) {
    case 'mysql':
        $sql = "SHOW TABLES";
        break;
    case 'pgsql':
        $sql = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'";
        break;
    case 'sqlite':
        $sql = "SELECT name FROM sqlite_master WHERE type='table'";
        break;
    default:
        throw new \Exception("Unsupported database driver: $driver");
}

$stmt = $pdo->prepare($sql);
$stmt->execute();

// Fetch table names based on the database driver
switch ($driver) {
    case 'mysql':
        while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $dbTables[] = $row[0];
        }
        break;
    case 'pgsql':
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $dbTables[] = $row['table_name'];
        }
        break;
    case 'sqlite':
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $dbTables[] = $row['name'];
        }
        break;
    default:
        throw new \Exception("Unsupported database driver: $driver");
}

$db->__destruct();

// $dbTables now contains the table names fetched from the database


echo '<div class="p-4 m-4 max-w-md ' . LIGHT_COLOR_SCHEME_CLASS . ' rounded-lg border border-gray-200 shadow-md ' . DARK_COLOR_SCHEME_CLASS . ' dark:border-gray-700 overflow-auto">';
    echo HTML::h2('Database Tables', true);
    echo HTML::p('Connected to DB: ' . DB_NAME);
    echo HTML::p('DB Host: ' . DB_HOST);
    echo HTML::p('DB User: ' . DB_USER);
    echo HTML::p('DB Driver: ' . $driver);
    echo HTML::p('Using SSL: ' . (DB_SSL ? 'Yes' : 'No'));
    echo HTML::p('Total tables: ' . count($dbTables));
    echo \Components\Table::auto($dbTables);
echo '</div>';

echo '<div class="max-w-md">';
    echo Gallery::fromFolder($_SERVER['DOCUMENT_ROOT'] . '/assets/images', 'Public Images', false, false, '160', 'auto');
echo '</div>';

echo Components\Image::display('/assets/images/msft.png', 'Microsoft Logo', 'Microsoft Logo', '150', 'auto', true, 'qweqweqwe');
