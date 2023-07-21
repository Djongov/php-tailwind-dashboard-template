<?php

namespace Template;

class Html
{
    public static function h1($text)
    {
        return '<h1 class="text-3xl ml-4 dark:text-gray-400 font-bold break-all">' . $text . '</h1>';
    }
    public static function h2($text)
    {
        return '<h2 class="text-2xl ml-4 dark:text-gray-400 font-bold break-all">' . $text . '</h1>';
    }
    public static function p($text, $classes = [])
    {
        if (empty($classes)) {
            return '<p class="ml-4 break-all">' . $text . '</p>';
        } else {
            return '<p class="' . $classes . '">' . $text . '</p>';
        }
        
    }
}
