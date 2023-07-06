<?php

use Database\DB;

use App\GeneralMethods;

$allowed_themes = ['amber', 'green', 'stone', 'rose', 'lime', 'teal', 'sky', 'purple', 'red', 'fuchsia', 'indigo'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theme']) && in_array($_POST['theme'], $allowed_themes)) {
        DB::queryPrepared("UPDATE `users` SET `theme`=? WHERE `username`=?", [$_POST['theme'], $usernameArray['username']]);
        $theme = $_POST['theme'];
}

$locale = (isset($usernameArray['origin_country'])) ? GeneralMethods::country_code_to_locale($usernameArray['origin_country']) : null;

echo '<div class="p-4 m-4 max-w-fit bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700 text-black dark:text-slate-300">';

echo '<ul>';
foreach ($usernameArray as $name => $setting) {
    if ($name === 'id') {
        continue;
    }
    if ($name === 'role' and $setting === null or $setting === '') {
        continue;
    }
    // Check if date
    if ($setting !== null and strtotime($setting)) {
        $fmt = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::GREGORIAN);
        echo '<li><strong>' . $name . '</strong> : ' . $fmt->format(strtotime($setting)) . '</li>';
        continue;
    }
    if ($name === 'theme') {
        echo '<li><div class="flex"><strong>' . $name . '</strong> : ';
        echo '<form id="theme-change">
                <select name="theme" class="ml-2 p-1 text-sm text-gray-900 border border-gray-300 rounded bg-gray-50 focus:ring-' . $theme . '-500 focus:border-' . $theme . '-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-' . $theme . '-500 dark:focus:border-' . $theme . '-500" autocomplete="off">';
        foreach ($allowed_themes as $themes) {
            if ($theme === $themes) {
                echo '<option value="' . $themes . '" selected="true">' . $themes . '</option>';
            } else {
                echo '<option value="' . $themes . '">' . $themes . '</option>';
            }
        }
        echo '</select></form></div></li>';
        continue;
    }
    echo '<li><strong>' . $name . '</strong> : ' . $setting . '</li>';
}
echo '</ul>';
echo '<p><strong>Token: </strong></p><p class="break-all c0py">' . $_COOKIE['auth_cookie'] . '</p>';
echo '</div>';
