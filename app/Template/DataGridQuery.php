<?php

namespace Template;

use Database\MYSQL;
use Template\DataGrid;

class DataGridQuery
{
    public static function render($dbTable, $title, $query, $theme, $delete = true, $edit = true)
    {
        // First the SELECT query

        $result = MYSQL::query($query);

        // Save the result as array in $data to be used for export later
        $data = $result->fetch_all(MYSQLI_ASSOC);

        if (count($data) === 0) {
            return '<p class="ml-4 text-black dark:text-gray-400">No results for ' . $dbTable . '</p>';
        }
        // get the columns

        $columnArray = [];

        foreach ($data as $index => $array) {
            foreach ($array as $column => $value) {
                array_push($columnArray, $column);
            }
        }
        $columnArray = array_unique($columnArray);


        return DataGrid::dataGridTemplate($title, $dbTable, $result, $columnArray, $delete, $edit, $data, $theme);
    }
}
