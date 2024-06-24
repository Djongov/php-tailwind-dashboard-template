<?php declare(strict_types=1);

namespace App\Database;

use App\Utilities\General;

class DB
{
    private $pdo;

    public function __construct(
        string $host = DB_HOST,
        string $username = DB_USER,
        string $password = DB_PASS,
        string $database = DB_NAME,
        int $port = DB_PORT,
        string $driver = DB_DRIVER
    ) {
        $config = [
            'driver' => $driver,
            'host' => $host,
            'dbname' => $database,
            'username' => $username,
            'password' => $password,
            'port' => $port,
            'driver' => $driver
        ];

        $this->connect($config);
    }
    private function connect(array $config) : void
    {
        $dsn = $this->buildDsn($config);
        $options = $this->getPDOOptions();

        try {
            $this->pdo = new \PDO($dsn, $config['username'], $config['password'], $options);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            if (ERROR_VERBOSE) {
                throw new \PDOException("DB: PDO connection failed: " . $e->getMessage());
            } else {
                \Controllers\Api\Output::error('Database connection failed', 500);
            }
            error_log("DB: PDO connection failed: " . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            if (ERROR_VERBOSE) {
                throw new \PDOException("DB: PDO connection failed: " . $e->getMessage());
            } else {
                \Controllers\Api\Output::error('Database connection failed', 500);
            }
            error_log("DB: PDO connection failed: " . $e->getMessage());
            throw $e;
        }
    }
    public function getConnection(): \PDO
    {
        if ($this->pdo instanceof \PDO) {
            return $this->pdo;
        } else {
            throw new \PDOException("DB: Database connection has not been established.");
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
            $dsn .= "sslrootcert=" . constant('DB_CA_CERT') . ";";
        }

        return $dsn;
    }
    private function getPDOOptions(): array
    {
        // You can add any default PDO options here if needed
        $options = [];
        if (defined("DB_SSL") && DB_SSL) {
            $options[\PDO::MYSQL_ATTR_SSL_CA] = constant('DB_CA_CERT');
        }
        $options[\PDO::ATTR_EMULATE_PREPARES] = false;
        return $options;
    }
    public function executeQuery(\PDO $pdo, string $sql, array $params = []) : \PDOStatement
    {
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            if (ERROR_VERBOSE) {
                throw new \PDOException("Error executing query: " . $e->getMessage() . ' SQL: ' . $sql . ' Params: ' . json_encode($params) . ' Error Code: ' . $e->getCode());
            } else {
                throw new \PDOException("Error executing query");
            }
        } catch (\Exception $e) {
            if (ERROR_VERBOSE) {
                throw new \PDOException("Error executing query: " . $e->getMessage() . ' SQL: ' . $sql . ' Params: ' . json_encode($params) . ' Error Code: ' . $e->getCode());
            } else {
                throw new \PDOException("Error executing query");
            }
        }
    }
    public function __destruct()
    {
        $this->pdo = null;
    }

    public function multiQuery(array $queryArray) : void
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
    public function checkDBColumns(array $columns, string $table) : void
    {
        $dbTableArray = $this->describe($table);
    
        // Extract column names from the table structure
        $dbColumns = [];
        foreach ($dbTableArray as $row => $type) {
            array_push($dbColumns, $row);
        }
    
        // Check if all columns in the input array exist in the database
        foreach ($columns as $column) {
            if (!in_array($column, $dbColumns)) {
                throw new \Exception("Column '$column' does not exist in table '$table'");
            }
        }
    }
    public function checkDBColumnsAndTypes(array $array, string $table) : void
    {
        $dbTableArray = $this->describe($table);
        
        // First check if all columns exist in the database
        foreach ($array as $column=>$data) {
            if (!array_key_exists($column, $dbTableArray)) {
                throw new \Exception("Column '$column' does not exist in table '$table'");
            }
            // Now let's check the data types
            $expectedType = $dbTableArray[$column];
            $expectedType = self::normalizeDataType($expectedType);
            // Let's do the data type now
            if (is_string($data)) {
                if (General::isDateOrDatetime($data)) {
                    $dataType = 'datetime';
                } else {
                    $dataType = 'string';
                }
            } elseif (in_array($data, ['0', '1', 'true', 'false', 1, 0])) {
                $dataType = 'bool';
            } elseif (is_numeric($data)) {
                $dataType = 'int';
            } else {
                $dataType = gettype($data);
            }
            // Compare the data types
            if ($dataType !== $expectedType) {
                throw new \Exception("Data type mismatch for column '$column'. Expected '$expectedType', got '$dataType'");
            }
        }
    }
    private static function normalizeDataType($type) : string
    {
        if (str_starts_with($type, 'varchar(')) {
            return 'string';
        }
        if (str_starts_with($type, 'tinyint(')) {
            return 'bool';
        }
        // Adjust this based on your specific requirements
        // Convert common MySQL/PostgreSQL data types to PHP types
        $typeMap = [
            'tinyint' => 'int',
            'smallint' => 'int',
            'mediumint' => 'int',
            'int' => 'int',
            'integer' => 'int',
            'bigint' => 'int',
            'decimal' => 'float',
            'float' => 'float',
            'double' => 'float',
            'real' => 'float', // PostgreSQL specific
            'date' => 'datetime',
            'datetime' => 'datetime',
            'timestamp' => 'datetime',
            'timestamp without time zone' => 'datetime', // PostgreSQL specific
            'time' => 'datetime',
            'year' => 'datetime',
            'char' => 'string',
            'varchar' => 'string',
            'character varying' => 'string', // PostgreSQL specific
            'text' => 'string',
            'json' => 'string',
            'boolean' => 'bool',

            // ... add more mappings as needed
        ];
    
        return $typeMap[strtolower($type)] ?? $type;
    }
    

    public function mapDataTypesArray(string $value) : string
{
    $type = '';
    if (str_starts_with($value, 'tinyint')) {
        $type = 'bool';
    }
    // And now postgres bool
    if (str_starts_with($value, 'boolean')) {
        $type = 'bool';
    }
    if (str_starts_with($value, 'int') || str_starts_with($value, 'integer') || str_starts_with($value, 'serial') || str_starts_with($value, 'bigserial')) {
        $type = 'int';
    }
    if (str_starts_with($value, 'decimal') || str_starts_with($value, 'float') || str_starts_with($value, 'double') || str_starts_with($value, 'numeric')) {
        $type = 'float';
    }
    if (str_starts_with($value, 'date') || str_starts_with($value, 'time') || str_starts_with($value, 'year') || str_starts_with($value, 'timestamp')) {
        $type = 'datetime';
    }
    if (str_starts_with($value, 'varchar') || str_starts_with($value, 'character varying') || str_starts_with($value, 'text')) {
        $type = 'string';
    }
    return $type;
}

    public function describe(string $table): array
    {
        $db = new self();
        $pdo = $db->getConnection();

        // Check the database driver to determine the appropriate SQL syntax
        $driver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
        switch ($driver) {
            case 'mysql':
                $sql = "DESCRIBE $table";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $dbTableArray = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                // Map MySQL columns to a uniform format
                $dbColumns = [];
                foreach ($dbTableArray as $row) {
                    $dbColumns[$row['Field']] = $row['Type'];
                }
                break;
            case 'pgsql':
                $sql = "SELECT column_name, data_type FROM information_schema.columns WHERE table_name = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$table]);
                $dbTableArray = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                // Extract column names and data types from the table structure
                $dbColumns = [];
                foreach ($dbTableArray as $row) {
                    $dbColumns[$row['column_name']] = $row['data_type'];
                }
                break;
            default:
                throw new \Exception("Unsupported database driver: $driver");
        }

        return $dbColumns;
    }
    // public function describe(string $dbTable) : array
    // {
    //     $db = new self();
    //     $pdo = $db->getConnection();

    //     // Check the database driver to determine the appropriate SQL syntax
    //     $driver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
    //     switch ($driver) {
    //         case 'mysql':
    //             $sql = "DESCRIBE $dbTable";
    //             $stmt = $pdo->prepare($sql);
    //             $stmt->execute();
    //             $resultArray = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    //             // Map MySQL columns to a uniform format
    //             $resultArray = array_map(function($row) {
    //                 return [
    //                     'column_name' => $row['Field'],
    //                     'data_type' => $row['Type']
    //                 ];
    //             }, $resultArray);
    //             break;
    //         case 'pgsql':
    //             $sql = "SELECT column_name, data_type FROM information_schema.columns WHERE table_name = ?";
    //             $stmt = $pdo->prepare($sql);
    //             $stmt->execute([$dbTable]);
    //             $resultArray = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    //             break;
    //         default:
    //             throw new \Exception("Unsupported database driver: $driver");
    //     }

    //     return $resultArray;
    // }
}
