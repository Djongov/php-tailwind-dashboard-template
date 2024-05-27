<?php

namespace Components\Page;

use Components\Html;

class Footer
{
    public static function render($theme)
    {
        $html = '';
        $siteTitle = SITE_TITLE;
        $currentYear = date('Y');
        $footerClass = 'mt-auto ' . LIGHT_COLOR_SCHEME_CLASS . ' ' . DARK_COLOR_SCHEME_CLASS;
        $textClass = TEXT_COLOR_SCHEME . ' ' . TEXT_DARK_COLOR_SCHEME;
        $html .= <<<HTML
        <footer class="$footerClass">
            <div class="w-full mx-auto max-w-screen-xl p-4 md:flex md:items-center md:justify-between">
                <span class="text-sm sm:text-center $textClass">© $currentYear <a href="/" class="hover:underline">$siteTitle</a>. All Rights Reserved.</span>
                <ul class="flex flex-wrap items-center mt-3 text-sm font-medium sm:mt-0">
                <li>
                <a href="#" class="hover:underline me-4 md:me-6">About</a>
                </li>
                <li>
                <a href="#" class="hover:underline me-4 md:me-6">Privacy Policy</a>
                </li>
                <li>
                <a href="#" class="hover:underline me-4 md:me-6">Licensing</a>
                </li>
                <li>
                <a href="#" class="hover:underline">Contact</a>
                </li>
                </ul>
            </div>
        </footer>
        HTML;
        // Scroll to top button
        $html .= '<button type="button" data-mdb-ripple="true" data-mdb-ripple-color="light" class="inline-block p-3 bg-' . $theme . '-600 text-white font-medium text-xs leading-tight uppercase rounded-full shadow-md hover:bg-' . $theme . '-700 hover:shadow-lg focus:bg-' . $theme . '-700 focus:shadow-lg focus:outline-none focus:ring-2 active:bg-' . $theme . '-800 active:shadow-lg transition duration-150 ease-in-out bottom-5 right-5 fixed hidden" id="btn-back-to-top">';
        $html .= '<svg aria-hidden="true" focusable="false" data-prefix="fas" class="w-4 h-4" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
        <path fill="currentColor" d="M34.9 289.5l-22.2-22.2c-9.4-9.4-9.4-24.6 0-33.9L207 39c9.4-9.4 24.6-9.4 33.9 0l194.3 194.3c9.4 9.4 9.4 24.6 0 33.9L413 289.4c-9.5 9.5-25 9.3-34.3-.4L264 168.6V456c0 13.3-10.7 24-24 24h-32c-13.3 0-24-10.7-24-24V168.6L69.2 289.1c-9.3 9.8-24.8 10-34.3.4z"></path></svg>';
        $html .= '</button>';
        $html .= '<input type="hidden" name="theme" value="' . $theme . '" />';
        return $html;
    }
}
