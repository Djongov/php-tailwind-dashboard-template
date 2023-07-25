<?php

namespace Template;

class Html
{
    public static function h1($text, $classes = [])
    {
        if (empty($classes)) {
            return '<h1 class="text-3xl dark:text-gray-400 font-bold break-all">' . $text . '</h1>';
        } else {
            return '<h1 class="' . $classes . '">' . $text . '</h1>';
        }
    }
    public static function h2($text, $classes = [])
    {
        if (empty($classes)) {
            return '<h2 class="text-2xl dark:text-gray-400 font-bold break-all">' . $text . '</h2>';
        } else {
            return '<h2 class="' . $classes . '">' . $text . '</h2>';
        }
    }
    public static function h3($text, $classes = [])
    {
        if (empty($classes)) {
            return '<h3 class="text-xl dark:text-gray-400 font-bold break-all">' . $text . '</h3>';
        } else {
            return '<h3 class="' . $classes . '">' . $text . '</h3>';
        }
    }
    public static function h4($text, $classes = [])
    {
        if (empty($classes)) {
            return '<h4 class="text-lg dark:text-gray-400 font-bold break-all">' . $text . '</h4>';
        } else {
            return '<h4 class="' . $classes . '">' . $text . '</h4>';
        }
    }
    public static function p($text, $classes = [])
    {
        if (empty($classes)) {
            return '<p class="ml-4 break-all">' . $text . '</p>';
        } else {
            return '<p class="' . $classes . '">' . $text . '</p>';
        }
        
    }
    public static function code($text, $codeTitle = '', $classes = [])
    {
        if (empty($classes)) {
            return '<pre class="p-4 m-4 max-w-fit overflow-auto bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700 break-all"><p class="font-bold">' . $codeTitle . '</p><code class="c0py">' . $text . '</code></pre>';
        } else {
            return '<pre class="' . $classes . '"><code class="c0py">' . $text . '</code></pre>';
        }
    }
}
