<?php

namespace App\Database;

class DB
{
    private $pdo;

    public function __construct(
        string $host = DB_HOST,
        string $username = DB_USER,
        string $password = DB_PASS,
        string $database = DB_NAME,
        string $charset = 'utf8'
    ) {
        $config = [
            'driver' => defined('DB_DRIVER') ? constant('DB_DRIVER') : 'mysql', // Default to MySQL if DB_DRIVER constant is not defined
            'host' => $host,
            'dbname' => $database,
            'username' => $username,
            'password' => $password,
            'charset' => $charset
        ];
        
        $this->connect($config);
    }

    private function connect(array $config)
    {
        $dsn = $this->buildDsn($config);
        $options = $this->getPDOOptions();
        $this->pdo = new \PDO($dsn, $config['username'], $config['password'], $options);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function getConnection(): \PDO
    {
        if ($this->pdo instanceof \PDO) {
            return $this->pdo;
        } else {
            throw new \PDOException("Database connection has not been established.");
        }
    }

    private function buildDsn(array $config): string
    {
        $dsn = "{$config['driver']}:";
        unset($config['driver'], $config['username'], $config['password']);

        foreach ($config as $key => $value) {
            $dsn .= "$key=$value;";
        }

        // Add SSL options if enabled
        if (defined("DB_SSL") && DB_SSL) {
            $dsn .= "sslmode=require;";
            // Add CA certificate path
            $dsn .= "sslrootcert=" . constant('CA_CERT') . ";";
        }

        return $dsn;
    }

    private function getPDOOptions(): array
    {
        // You can add any default PDO options here if needed
        $options = [];
        if (defined("DB_SSL") && DB_SSL) {
            $options[\PDO::MYSQL_ATTR_SSL_CA] = constant('CA_CERT');
        }
        return $options;
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    public function multiQuery(array $queryArray)
    {
        try {
            $pdo = $this->getConnection();
            $pdo->beginTransaction();

            foreach ($queryArray as $query) {
                $pdo->exec($query);
            }

            $pdo->commit();
        } catch (\PDOException $e) {
            // If an error occurs, roll back the transaction
            $pdo->rollBack();

            // Handle the exception, log it, or throw a custom exception
            throw new \PDOException("Error executing multiple queries: " . $e->getMessage());
        }
    }

    public function checkDBColumnsAndTypes(array $array, string $table)
    {
        $db = new self();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("DESCRIBE `$table`");
        $stmt->execute();
        $dbTableArray = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Extract column names and data types from the table structure
        $dbColumns = array_column($dbTableArray, 'Type', 'Field');

        // Check if all columns in $_POST exist in the database
        foreach ($array as $key => $value) {
            if (is_int($key)) {
                // This is a numeric array, so the column name is the value
                $key = $value;
            }
            if (!array_key_exists($key, $dbColumns)) {
                // Column does not exist in the database
                throw new \Exception("Column '$key' does not exist in table '$table'");
            } else {
                // Column exists, check data type
                $expectedType = self::normalizeDataType($dbColumns[$key]);
                $actualType = self::normalizeDataType(gettype($value));

                if (self::checkDataType($expectedType, $actualType)) {
                    throw new \Exception("Column '$key' in table '$table' has incorrect data type. Expected '$expectedType', got '$actualType'");
                }
            }
        }
    }
    private static function checkDataType($expectedType, $actualType)
    {
        // Implement your own logic for data type checking
        // This is a simple example, you may need to extend it based on your requirements
        return $expectedType === $actualType;
    }
    private static function normalizeDataType($type)
    {
        // Adjust this based on your specific requirements
        // Convert common MySQL data types to PHP types
        $typeMap = [
            'tinyint' => 'int',
            'smallint' => 'int',
            'mediumint' => 'int',
            'int' => 'int',
            'bigint' => 'int',
            'decimal' => 'float',
            'float' => 'float',
            'double' => 'float',
            // ... add more mappings as needed
        ];

        return $typeMap[strtolower($type)] ?? $type;
    }
    public function describe(string $dbTable) : array
    {
        $db = new self();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("DESCRIBE `$dbTable`");
        $stmt->execute();

        $resultArray = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // Now we want to return an array of the column names and their types only
        $resultArray = array_column($resultArray, 'Type', 'Field');
        // Now go through the values and convert them to their respective types
        foreach ($resultArray as $key => $value) {
            $resultArray[$key] = $db->mapDataTypesArray($value);
        }
        return $resultArray;
    }
    public function mapDataTypesArray(string $value)
    {
        $type = '';
        if (str_starts_with($value, 'tinyint')) {
            $type = 'bool';
        }
        if (str_starts_with($value, 'int')) {
            $type = 'int';
        }
        if (str_starts_with($value, 'decimal') || str_starts_with($value, 'float') || str_starts_with($value, 'double')) {
            $type = 'float';
        }
        if (str_starts_with($value, 'date') || str_starts_with($value, 'time') || str_starts_with($value, 'year')) {
            $type = 'datetime';
        }
        if (str_starts_with($value, 'varchar') || str_starts_with($value, 'text')) {
            $type = 'string';
        }
        return $type;
    }
}
