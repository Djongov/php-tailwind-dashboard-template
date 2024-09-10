<?php declare(strict_types=1);

namespace App;

use Components\Alerts;
use Components\Page\Head;
use Components\Page\Menu;
use Components\Page\Footer;

class Page
{
    public function head(string $title, string $description, array $keywords, string $thumbimage) : string
    {
        // Load these scripts
        $scriptsArray = [
            'https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js' => [
                'cache' => true,
            ],
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js' => [
                'cache' => true,
            ],
            'https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js' => [
                'cache' => true,
            ],
            '/assets/js/dataTables.js' => [
                'defer' => 'true',
                'cache' => true
            ],
            '/assets/js/datagrid.js' => [
                'defer' => 'true'
            ],
            '/assets/js/c0py.js' => [
                'defer' => 'true',
                'cache' => true
            ],
            '/assets/js/forms.js' => [
                'defer' => 'true'
            ],
            '/assets/js/charts.js' => [
                'defer' => 'true'
            ],
            'https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js' => [
                'cache' => true
            ],
            'https://cdn.jsdelivr.net/npm/apexcharts' => [
                'cache' => true
            ],
            'https://cdn.tailwindcss.com?plugins=typography' => [
                'cache' => true
            ],
            '/assets/js/main.js' => [
                'defer' => 'true'
            ],
        ];
        // If loading screen is enabled, add the loading screen js with defer and cache
        if (SHOW_LOADING_SCREEN) {
            $scriptsArray['/assets/js/loading-screen.js'] = [
                'defer' => 'true',
                'cache' => true
            ];
        }
        // Load these styles
        $cssArray = [
            '/assets/css/main.css'
        ];
        return Head::render($title, $description, $keywords, $thumbimage, $scriptsArray, $cssArray);
    }
    public function header($usernameArray, $menuArray, $isAdmin, $theme) : string
    {
        $html = '';
        $html .= '<header>';
        $html .=  $this->menu($menuArray, $theme, $usernameArray, $isAdmin);
        $html .= '</header>';
        return $html;
    }
    public function menu($array, $theme, $usernameArray, $isAdmin) : string
    {
        // If the array is empty, don't render the menu
        return (!empty($array)) ? Menu::render($array, $usernameArray, $isAdmin, $theme) : '';
    }
    public function footer($theme) : string
    {
        return Footer::render($theme);
    }
    public function build(string $title, string $description, array $keywords, string $thumbimage, string $theme, array $menuArray, array $usernameArray, string $controlerPath, bool $isAdmin) : string
    {
        $html = '';
        $html .= $this->head($title, $description, $keywords, $thumbimage, $theme);
        $html .= '<body class="h-full antialiased ' . LIGHT_COLOR_SCHEME_CLASS . ' ' . DARK_COLOR_SCHEME_CLASS . ' ' . TEXT_COLOR_SCHEME . ' ' . TEXT_DARK_COLOR_SCHEME . '">';
            $html .= '<div class="md:mx-auto  ' . BODY_COLOR_SCHEME_CLASS . ' ' . BODY_DARK_COLOR_SCHEME_CLASS . '">';
                $html .= $this->header($usernameArray, $menuArray, $isAdmin, $theme);
                if (SHOW_LOADING_SCREEN) {
                    $html .= '<div id="loading-screen" class="w-fit mx-auto my-12 flex items-center border border-black dark:border-gray-400 ' . LIGHT_COLOR_SCHEME_CLASS . ' ' . BODY_DARK_COLOR_SCHEME_CLASS . ' p-8 rounded z-99999"><div class="animate-spin border-t-4 border-' . $theme . '-500 border-solid rounded-full h-16 w-16"></div>
                    <p class="ml-2">Loading...</p></div>';
                }
                $mainContentClass = (SHOW_LOADING_SCREEN) ? 'class="hidden"' : 'class=""';
                $html .= '<main id="main-content" ' . $mainContentClass . '">';
                    // Check if the file exists before including it
                    if (file_exists($controlerPath)) {
                        ob_start(); // Start output buffering to capture content
                        include $controlerPath;
                        $html .= ob_get_clean(); // Get the included content and append it to $html
                    } else {
                        // Handle the case when the file doesn't exist
                        $html .= Alerts::danger('The file ' . $controlerPath . ' does not exist');
                    }
                $html .= '</main>';
                // Do not show the footer on the login page
                if (!str_starts_with($_SERVER['REQUEST_URI'], '/login')) {
                    $html .= $this->footer($theme);
                }
            $html .= '</div>';
        $html .= '</body>';
        return $html;
    }
}
