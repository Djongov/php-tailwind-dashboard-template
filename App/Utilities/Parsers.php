<?php declare(strict_types=1);

namespace App\Utilities;

class Parsers
{
    public static function yaml($yaml_string) : array
    {
        $yaml_data = [];
        $lines = explode("\n", $yaml_string);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || $line[0] === '#') {
                continue;
            }
            list($key, $value) = explode(':', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $yaml_data[$key] = $value;
        }
        return $yaml_data;
    }
}
