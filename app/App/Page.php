<?php

namespace App;

use Components\ThemeSwitcher;
use Template\Html;

class Page
{
    public function head(string $title, string $description, array $keywords, string $thumbimage)
    {
        $html = '';
        $html .= '<!DOCTYPE html>' . PHP_EOL;
        $html .= '<html lang="en" class="h-full">' . PHP_EOL;
        $html .= '<head>' . PHP_EOL;
        $html .=  PHP_EOL . '<title>' . $title . ' - ' . SITE_TITLE . '</title>' . PHP_EOL;
        $html .= '<link rel="icon" type="image/x-icon" href="/assets/images/icon.png" >' . PHP_EOL;
        //$html .= '<link rel="apple-touch-icon" href="/apple-touch-icon.png" >' . PHP_EOL;
        $html .= '<!-- Meta tags -->' . PHP_EOL;
        //$html .= '<meta name="apple-mobile-web-app-capable" content="yes">' . PHP_EOL;
        //$html .= '<meta name="apple-mobile-web-app-status-bar-style" content="black">' . PHP_EOL;
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1">' . PHP_EOL;
        $html .= '<meta name="robots" content="index, follow" >' . PHP_EOL;
        $html .= '<meta name="author" content="Dimitar Dzhongov" >' . PHP_EOL;
        $html .= '<meta name="keywords" content="' . implode(",", $keywords) . '" >' . PHP_EOL;
        // Not needed $html .= '<link rel="canonical" href="https://"' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '">' . PHP_EOL;
        $html .= '<meta name="description" content="' . $description . '" >' . PHP_EOL;
        // Open Graph tags
        $html .= '<meta property="og:type" content="website" >' . PHP_EOL;
        $html .= '<meta property="og:url" content="https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" >' . PHP_EOL;
        $html .= '<meta property="og:title" content="' . $title . '" >' . PHP_EOL;
        $html .= '<meta property="og:description" content="' . $description . '" >' . PHP_EOL;
        $html .= '<meta property="og:image" content="' . $thumbimage . '" >' . PHP_EOL;
        $html .= '<meta property="og:image:width" content="250">' . PHP_EOL;
        $html .= '<meta property="og:image:height" content="236">' . PHP_EOL;
        $html .= '<meta property="og:image:type" content="image/png">' . PHP_EOL;
        //$html .= '<meta property="fb:app_id" content="1061751454210608" >' . PHP_EOL;
        // Twitter tags
        $html .= '<meta name="twitter:card" content="summary_large_image" >' . PHP_EOL;
        $html .= '<meta name="twitter:site" content="@Sunwell_LTD" >' . PHP_EOL;
        $html .= '<meta name="twitter:creator" content="@Djongov" >' . PHP_EOL;
        $html .= '<meta name="twitter:title" content="' . $title . '" >' . PHP_EOL;
        $html .= '<meta name="twitter:description" content="' . $description . '" >' . PHP_EOL;
        $html .= '<meta name="twitter:image" content="' . $thumbimage . '" >' . PHP_EOL;
        $html .= '<meta name="twitter:image:alt" content="' . SITE_TITLE . ' logo" >' . PHP_EOL;
        // CSS files
        //$html .= '<link rel="stylesheet" href="/assets/css/main.css">';
        // Scripts
        $html .= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>' . PHP_EOL;
        $html .= '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>' . PHP_EOL;
        $html .= '<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>' . PHP_EOL;
        $html .= '<script src="/assets/js/dataTables.js" defer></script>' . PHP_EOL;
        $html .= '<script src="/assets/js/datagrid.js" defer></script>' . PHP_EOL;
        $html .= '<script src="/assets/js/c0py.js" defer></script>' . PHP_EOL;
        $html .= '<script src="/assets/js/forms.js" defer></script>' . PHP_EOL;
        $html .= '<script src="/assets/js/main.js?'. time() . '" defer></script>' . PHP_EOL;
        $html .= '<script src="/assets/js/flowbite.js"></script>' . PHP_EOL;
        $html .= '<script src="https://cdn.tailwindcss.com?plugins=typography"></script>' . PHP_EOL;
        $tailwindConfig = file_get_contents(dirname($_SERVER['DOCUMENT_ROOT']) . '/tailwind.config.js');
        $html .= <<< InlineScript
            <script nonce="1nL1n3JsRuN1192kwoko2k323WKE">
                $tailwindConfig
            </script>
            InlineScript . PHP_EOL;
        $html .= '</head>' . PHP_EOL;
        return $html;
    }
    public function header($usernameArray, $menuArray, $isAdmin, $theme)
    {
        $html = '';
        $html .= '<header>';
        $html .=  $this->menu($menuArray, $theme, $usernameArray, $isAdmin);
        $html .= '</header>';
        return $html;
    }
    public function menu($array, $theme, $usernameArray, $isAdmin)
    {
        if (empty($array)) {
            return;
        }
        $html = '<nav class="px-2 bg-gray-50 border-gray-200 dark:border-gray-700 dark:bg-gray-900">';
        // Holder div, used to have justify-between
        $html .= '<div class="flex flex-wrap justify-center">';
        // Logo + href to homepage
        $html .= '<a href="/" class="flex items-center">' . LOGO . '<p class="font-semibold ml-2">' . SITE_TITLE . '</p></a>';
        // Button for mobile menu sandwich
        $html .= '<button data-collapse-toggle="mobile-menu" type="button" class="inline-flex justify-center items-center ml-6 text-gray-800 rounded-lg md:hidden hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-' . $theme . '-300 dark:text-gray-400 dark:hover:text-white dark:focus:ring-gray-500" aria-controls="mobile-menu-2" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-8 h-8" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
            </button>';
        // Menu itself
        $html .= '<div class="hidden w-full md:block md:w-auto" id="mobile-menu">';
        $html .= '<ul class="mx-auto flex md:flex-row flex-col flex-wrap justify-center items-center p-4 mt-4 rounded-lg border border-gray-100 md:space-x-8 md:mt-0 md:text-sm md:font-medium md:border-0 dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">';
        $uniqueIdCounter = 0;
        foreach ($array as $name => $value) {
            // Mechanism to skip entries that should be under login only
            if (isset($value['require_login']) && !isset($usernameArray['username'])) {
                continue;
            }
            if (is_array($value['link'])) {
                $html .= '<li class="min-w-fit mx-auto">';
                $html .= '<div class="flex flex-row items-center">';

                if (isset($value['icon'])) {
                    if ($value['icon']['type'] === 'link') {
                        $html .= '<img class="w-6 h-6" src="' . $value['icon']['src'] . '" alt="' . $name . '" />';
                    } else {
                        $html .= '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 w-6 h-6 stroke-' . $theme . '-500">' . $value['icon']['src'] . '</svg>';
                    }
                }

                $html .= '<button id="dropdownNavbarLink" data-dropdown-toggle="dropdownNavbar-' . $uniqueIdCounter . '" class="text-base font-normal ml-0 flex justify-between hover:bg-' . $theme . '-500 hover:boder hover:border-black hover:text-white text-gray-700 dark:text-gray-400 dark:hover:text-white dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 hover:rounded-md p-1">' . $name . '<svg class="ml-1 w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg></button>';
                $html .= '</div>';
                $html .= '<!-- Dropdown menu -->
                        <div id="dropdownNavbar-' . $uniqueIdCounter . '" class="z-10 w-44 font-normal bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 block hidden" data-popper-reference-hidden="" data-popper-escaped="" data-popper-placement="bottom" style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate3d(381px, 66px, 0px);">';
                $html .= '<ul class="py-1 text-sm text-gray-700 dark:text-gray-400" aria-labelledby="dropdownLargeButton">';
                foreach ($value['link'] as $sub_name => $sub_array) {
                    $html .= '<li class="min-w-fit">';
                    $html .= '<div class="flex flex-row items-center">';
                    $html .= '<a href="' . $sub_array['sub_link'] . '" class="w-full text-center py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">';
                    if (isset($sub_array['icon'])) {
                        if ($sub_array['icon']['type'] === 'link') {
                            $html .= '<img class="mx-2 inline-block" src="' . $sub_array['icon']['src'] . '" alt="' . $sub_name . '" />';
                        } else {
                            $html .= '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-2 w-6 h-6 stroke-' . $theme . '-500">' . $sub_array['icon']['src'] . '</svg>';
                        }
                    }
                    $html .= $sub_name . '</a>';
                    $html .= '</div>';
                    $html .= '</li>';
                }
                $html .= '</ul>';
                $html .= '</div>';
                $html .= '</li>';
                continue;
            } else {
                $html .= '<li class="min-w-fit mx-auto">';
                $html .= '<div class="flex flex-row items-center">';
                if (isset($value['icon'])) {
                    if ($value['icon']['type'] === 'link') {
                        $html .= '<img class="mr-1 w-6 h-6" src="' . $value['icon']['src'] . '" alt="' . $name . '" />';
                    } else {
                        $html .= '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 stroke-' . $theme . '-500 mr-1">' . $value['icon']['src'] . '</svg>';
                    }
                }
                $html .= '<a href="' . $value['link'] . '" class="min-w-fit block py-2 text-base font-normal hover:bg-' . $theme . '-500 hover:boder hover:border-black hover:text-white text-gray-700 dark:text-gray-400 dark:hover:text-white dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 hover:rounded-md p-1">' . $name . '</a>';
                $html .= '</div>';
                $html .= '</li>';
            }

            $uniqueIdCounter++;
        }
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '<div class="flex flex-wrap items-center ml-4">';
        // Theme switcher
        $html .= ThemeSwitcher::render();
        if (isset($usernameArray['username']) && $usernameArray['username'] !== null) {
            $name = $usernameArray['name'];
            $html .= '<div class="flex md:order-2">
                <div class="flex items-center justify-between ml-2">
                    <div class="flex items-center">
                        <svg class="ml-1 w-12 h-12 stroke-' . $theme . '-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <button id="dropdownNavbarLink2" data-dropdown-toggle="userAvatarDropDownNavBar" class="ml-1 flex justify-between items-center py-2 pr-4 pl-3 w-full font-medium hover:bg-' . $theme . '-500 rounded hover:text-gray-100 truncate max-w-sm cursor-pointer">
                            ' . $name . '
                            <svg class="ml-1 w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <!-- Dropdown menu -->
                <div id="userAvatarDropDownNavBar" class="z-10 w-44 font-normal bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 block hidden" data-popper-reference-hidden="" data-popper-escaped="" data-popper-placement="bottom" style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate3d(381px, 66px, 0px);">
                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-400" aria-labelledby="dropdownLargeButton">';
            // Display the admin menus to admin users
            foreach (USERNAME_DROPDOWN_MENU as $name => $array) {
                if ($array['admin'] && $isAdmin) {
                    $html .= '<li>';
                    $html .= '<a href="' . $array['path'] . '" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">' . $name . '</a>';
                    $html .= '</li>';
                } elseif (!$array['admin']) {
                    // Display non-admin menu items to all users
                    $html .= '<li>';
                    $html .= '<a href="' . $array['path'] . '" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">' . $name . '</a>';
                    $html .= '</li>';
                }
            }
            $html .= '</ul>
                </div>
            </div>';
        } else {
            $html .= '<a class="inline-flex items-center justify-center px-2 py-2 h-10 w-18 md:w-18 my-2 ml-4 text-lg leading-7 text-' . $theme . '-50 bg-' . $theme . '-500 hover:bg-' . $theme . '-600 font-medium focus:ring-2 focus:ring-' . $theme . '-500 focus:ring-opacity-50 border border-transparent rounded-md shadow-sm" href="/login">Login</a>';
            $html .= '</div>';
        }
        $html .= '</nav>';
        return $html;
    }
    public function footer($theme)
    {
        $html = '';
        $html .= '<footer class="p-4 bg-white sm:p-6 dark:bg-gray-900">' . PHP_EOL;
        $html .= '<div class="md:flex md:justify-between">';
        $html .= '<div class="md:flex md:justify-between">';
        $html .= '<a href="/" class="flex items-center">' . LOGO . '<span class="text-xl font-bold text-gray-800 dark:text-gray-200">' . SITE_TITLE . '</span></a>';
        $html .= '</div>';
        // Start grid
        $html .= '<div class="grid grid-cols-3 gap-8 sm:gap-6 sm:grid-cols-4">';
        $footerMenuArray = [
            'Report Issues' => [
                'Submit an issue' => '/report-issue',
            ],
            'About us' => [
                'About us' => '/about-us',
                'Contact us' => '/contact-us',
            ],
            'Legal' => [
                'Terms of service' => '/terms-of-service',
                'Privacy policy' => '/privacy-policy',
            ]
        ];
        foreach ($footerMenuArray as $title => $meta) {
            $html .= '<div>';
            $html .= HTML::h3($title);
            $html .= '<ul class="mt-2 leading-6 text-gray-500 dark:text-gray-400">';
            foreach ($meta as $linkTitle => $link) {
                $html .= '<li><a href="' . $link . '" class="hover:underline">' . $linkTitle . '</a></li>';
            }
            $html .= '</ul>';
            $html .= '</div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</footer>' . PHP_EOL;
        // Scroll to top button
        $html .= '<button type="button" data-mdb-ripple="true" data-mdb-ripple-color="light" class="inline-block p-3 bg-' . $theme . '-600 text-white font-medium text-xs leading-tight uppercase rounded-full shadow-md hover:bg-' . $theme . '-700 hover:shadow-lg focus:bg-' . $theme . '-700 focus:shadow-lg focus:outline-none focus:ring-2 active:bg-' . $theme . '-800 active:shadow-lg transition duration-150 ease-in-out bottom-5 right-5 fixed hidden" id="btn-back-to-top">';
        $html .= '<svg aria-hidden="true" focusable="false" data-prefix="fas" class="w-4 h-4" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
        <path fill="currentColor" d="M34.9 289.5l-22.2-22.2c-9.4-9.4-9.4-24.6 0-33.9L207 39c9.4-9.4 24.6-9.4 33.9 0l194.3 194.3c9.4 9.4 9.4 24.6 0 33.9L413 289.4c-9.5 9.5-25 9.3-34.3-.4L264 168.6V456c0 13.3-10.7 24-24 24h-32c-13.3 0-24-10.7-24-24V168.6L69.2 289.1c-9.3 9.8-24.8 10-34.3.4z"></path></svg>';
        $html .= '</button>';
        $html .= '<input type="hidden" name="theme" value="' . $theme . '" />';
        return $html;
    }
    public function build(string $title, string $description, array $keywords, string $thumbimage, string $theme, array $menuArray, array $usernameArray, string $controlerPath, bool $isAdmin)
    {
        $html = '';
        $html .= $this->head($title, $description, $keywords, $thumbimage, $theme);
        $html .= '<body class="h-full antialiased bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-400">';
        $html .= '<div class="md:mx-auto bg-gray-200 dark:bg-gray-800">';
        $html .= $this->header($usernameArray, $menuArray, $isAdmin, $theme);
        // Check if the file exists before including it
        if (file_exists($controlerPath)) {
            ob_start(); // Start output buffering to capture content
            include $controlerPath;
            $html .= ob_get_clean(); // Get the included content and append it to $html
        } else {
            // Handle the case when the file doesn't exist
            $html .= '<p>Error: Controller file not found.</p>';
        }

        $html .= $this->footer($theme);
        $html .= '</div>';
        $html .= '</body>';
        return $html;
    }
}
