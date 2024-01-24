<?php

namespace Markdown;

use Components\Alerts;
use Parsedown;

class Page
{
    public static function render($fileName, $theme)
    {
        // Now let's get the contents of the file
        if (file_exists($fileName . '.md') === false) {
            http_response_code(404);
            return Alerts::danger('page does not exist');
        }
        $fileContents = file_get_contents($fileName . '.md');
        // Now let's render the markdown
        $html = '<article class="mx-6 my-4 max-w-full p-6 bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-400 prose prose-md prose-' . $theme . ' dark:prose-invert">';
            $html .= Parsedown::instance()->text($fileContents);
        $html .= '</article>';
        return $html;
    }
    public static function renderRemote($url, $theme)
    {
        // Now let's get the contents of the file
        $fileContents = file_get_contents($url);
        if (empty($fileContents)) {
            http_response_code(404);
            return Alerts::danger('Could not fetch remote page');
        }
        // Now let's render the markdown
        $html = '<article class="mx-6 my-4 max-w-full p-6 bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-400 prose prose-md prose-' . $theme . ' dark:prose-invert">';
            $html .= Parsedown::instance()->text($fileContents);
        $html .= '</article>';
        return $html;
    }
}
