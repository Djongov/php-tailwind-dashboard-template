<?php

namespace Template;

class SimpleDataGrid
{
    private $data;

    public static function render($data) {
        //var_dump($data);
        $html = '';
        $tableId = uniqid();
        foreach ($data as $name => $value) {
            $columnArray = array_unique(array_keys($value));
        }
        $html .= '<div class="m-6 overflow-x-auto relative shadow-md sm:rounded-lg max-h-[44rem]">';
            $html .= '<table id="' . $tableId . '" class="bg-gray-100 dark:bg-gray-800 w-full p-8 text-gray-700 dark:text-gray-400 border-collapse border border-slate-400 text-center">';
                // Sticky thead
                $html .= '<thead class="text-normal text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0">';
                    $html .=  '<tr>';
                    // Now let's loop the newly formed array
                    foreach ($columnArray as $column) {
                        // build the table heads columns with the names from the array
                        $html .= '<th scope="col" class="py-3 px-6 apply-filter border border-slate-400">' . $column . '</th>';
                    }
                    $html .=  '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';
                    foreach ($data as $index => $array) {
                        $html .=  '<tr tabindex="' . $index . '" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 focus:bg-gray-200 dark:focus:bg-gray-400 focus:text-black dark:focus:text-white focus:dark:border-gray-400">';
                            foreach ($array as $name => $value) {
                                $tdClass = 'py-4 px-6 border border-slate-400 max-w-lg truncate';
                                if ($value === null) {
                                    $value = 'null';
                                }
                                $html .= '<td class="' . $tdClass . '" title="' . $value . '">' . htmlspecialchars($value) . '</td>';
                            }
                        $html .= '</tr>';
                    }
                $html .= '</tbody>';
            $html .= '</table>';
        $html .= '</div>';
        return $html;
    }
}
