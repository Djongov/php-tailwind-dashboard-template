<?php

namespace DataGrid;

class DataGridDBTable
{
    public static function getColumns($data): array
    {
        // Let's calculate the columns
        $totalColumns = [];
        foreach ($data as $arrays) {
            foreach ($arrays as $column => $data) {
                array_push($totalColumns, $column);
            }
        }
        // Now lets make the totalColumns unique
        return array_unique($totalColumns);
    }
    
}
