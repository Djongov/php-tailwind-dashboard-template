<?php

namespace DataGrid;

class SimpleVerticalDataGrid
{
    public static function render(array $data) : string
    {
        $html = '';
        $tableId = uniqid();
        $columnArray = [];
        foreach ($data as $name => $value) {
            array_push($columnArray, $name);
        }
        $columnArray = array_unique($columnArray);
        if (empty($columnArray)) {
            return '<p class="m-6 my-2">No records</p>';
        }
        $html .= '<div class="m-6 overflow-x-auto relative shadow-md sm:rounded-lg max-h-[44rem]">';
        $html .= '<table id="' . $tableId . '" class="bg-gray-100 dark:bg-gray-800 w-full p-8 text-gray-700 dark:text-gray-400 border-collapse border border-slate-400 text-center">';
        $html .= '<tbody>';
        $tdClass = 'py-4 px-6 border border-slate-400 max-w-lg break-all';
        foreach ($data as $index => $array) {
            $html .=  '<tr tabindex="' . $index . '" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 focus:bg-gray-200 dark:focus:bg-gray-400 focus:text-black dark:focus:text-white focus:dark:border-gray-400">';
                $html .= '<td class="' . $tdClass . '" title="' . $index . '">' . htmlspecialchars($index) . '</td>';
                $html .= '<td class="' . $tdClass . '" title="' . $array . '">' . htmlspecialchars($array) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        return $html;
    }
}
