<?php

use Authentication\AzureAD;

if (isset($_COOKIE[AUTH_COOKIE_NAME])) {
    if (AzureAD::checkJWTToken($_COOKIE[AUTH_COOKIE_NAME])) {
        header("Location: /dashboard");
    }
}

$login_message = 'You are not logged in';

?>
<!-- wrapper div -->
<div class="flex items-center justify-center mx-4">
    <div class="flex flex-col w-full max-w-md my-16 px-4 py-8 bg-gray-50 rounded-lg dark:bg-gray-900 sm:px-6 md:px-8 lg:px-10 border border-gray-300 shadow-md">
        <p class="py-2 px-4 flex justify-center items-center text-red-600"><?= $login_message ?>
        </p>
        <div class="self-center mb-6 text-xl font-light text-gray-600 sm:text-2xl dark:text-white">
            Login To Your Account
        </div>
        <div class="flex gap-4 item-center">
            <a class="mb-4 w-full text-black dark:text-slate-400 font-medium text-center border border-gray-200 rounded-md shadow-sm hover:bg-gray-200 hover:dark:text-black" href="<?= Login_Button_URL ?>
">
                <div class="flex items-center justify-center py-3 px-3 leading-5">
                    <img height="32" width="32" src="/assets/images/MSFT.png" alt="MS Logo" />
                    <span class="ml-3">Sign in with Microsoft</span>
                </div>
            </a>
        </div>
        <?php

        use Security\CRSF;

        if (LOCAL_USER_LOGIN) :

        ?>
            <p class="text-center">or login with a local account</p>
            <div class="mt-5">
                <form id="local-login-form" action="/api/local-login-process" autoComplete="on" method="post">
                    <div class="flex flex-col mb-2">
                        <div class="flex relative">
                            <span class="bg-gray-500 dark:bg-gray-800 rounded-l-md inline-flex items-center px-3 border-t bg-white border-l border-b border-gray-300 text-gray-500 shadow-sm text-sm">
                                <svg width="15" height="15" fill="currentColor" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1792 710v794q0 66-47 113t-113 47h-1472q-66 0-113-47t-47-113v-794q44 49 101 87 362 246 497 345 57 42 92.5 65.5t94.5 48 110 24.5h2q51 0 110-24.5t94.5-48 92.5-65.5q170-123 498-345 57-39 100-87zm0-294q0 79-49 151t-122 123q-376 261-468 325-10 7-42.5 30.5t-54 38-52 32.5-57.5 27-50 9h-2q-23 0-50-9t-57.5-27-52-32.5-54-38-42.5-30.5q-91-64-262-182.5t-205-142.5q-62-42-117-115.5t-55-136.5q0-78 41.5-130t118.5-52h1472q65 0 112.5 47t47.5 113z">
                                    </path>
                                </svg>
                            </span>
                            <input type="email" id="sign-in-email" name="username" class="rounded-r-lg flex-1 appearance-none border border-gray-300 w-full py-2 px-4 bg-white text-gray-700 dark:text-gray-400 dark:bg-gray-700 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:ring-<?= COLOR_SCHEME ?>
-600 focus:border-transparent" placeholder="Your email" required autocomplete="username" />
                        </div>
                    </div>
                    <div class="flex flex-col mb-6">
                        <div class="flex relative ">
                            <span class="bg-gray-500 dark:bg-gray-800 rounded-l-md inline-flex  items-center px-3 border-t bg-white border-l border-b  border-gray-300 text-gray-500 shadow-sm text-sm">
                                <svg width="15" height="15" fill="currentColor" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1376 768q40 0 68 28t28 68v576q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-576q0-40 28-68t68-28h32v-320q0-185 131.5-316.5t316.5-131.5 316.5 131.5 131.5 316.5q0 26-19 45t-45 19h-64q-26 0-45-19t-19-45q0-106-75-181t-181-75-181 75-75 181v320h736z">
                                    </path>
                                </svg>
                            </span>
                            <input type="password" id="sign-in-password" name="password" class="dark:text-gray-400 dark:bg-gray-700 rounded-r-lg flex-1 appearance-none border border-gray-300 w-full py-2 px-4 bg-white text-gray-700 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:ring-<?= COLOR_SCHEME ?>
-600 focus:border-transparent" placeholder="Your password" required autocomplete="current-password" />
                        </div>
                    </div>
            </div>
            <div class="flex w-full">
                <button type="submit" class="py-2 px-4 bg-<?= COLOR_SCHEME ?>
-600 hover:bg-<?= COLOR_SCHEME ?>
-700 focus:ring-<?= COLOR_SCHEME ?>
-500 focus:ring-offset-<?= COLOR_SCHEME ?>
-200 text-white w-full transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2  rounded-lg ">
                    Login
                </button>
            </div>
            <input type="hidden" name="destination" value="<?= (isset($_GET['destination'])) ? $_GET['destination'] : '/' ?>" />
            <?= CRSF::createTag() ?>
            </form>
            <?php
            if (MANUAL_REGISTRATION) :
            ?>
                <p class="text-sm mt-4">Or you can sign up <a class="underline text-<?= COLOR_SCHEME ?>-500" href="/register">here.</a></p>
            <?php
            endif;
            ?>
    </div>
<?php
            echo '<script src="/assets/js/local-login.js"></script>';
        endif;
?>
</div>
</div>
