/* Theme Changes */
var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

// Check if the user's preferred color scheme is dark
var preferredColorScheme = window.matchMedia('(prefers-color-scheme: dark)').matches;

// Change the icons inside the button based on previous settings
if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && preferredColorScheme)) {
    if (themeToggleLightIcon) {
        themeToggleLightIcon.classList.remove('hidden');
    }
} else {
    if (themeToggleDarkIcon) {
        themeToggleDarkIcon.classList.remove('hidden');
    }
}

// Auto set class based on the local storage theme
if (localStorage.getItem('color-theme')) {
    if (localStorage.getItem('color-theme') === 'dark') {
        document.documentElement.classList.add('dark');
        localStorage.setItem('color-theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('color-theme', 'light');
    }
} else {
    if (preferredColorScheme) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('color-theme', 'dark');
    } else {
        localStorage.setItem('color-theme', 'light');
    }
}

var themeToggleBtn = document.getElementById('theme-toggle');

if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', function () {

        // toggle icons inside button
        themeToggleDarkIcon.classList.toggle('hidden');
        themeToggleLightIcon.classList.toggle('hidden');

        // if set via local storage previously
        if (localStorage.getItem('color-theme')) {
            if (localStorage.getItem('color-theme') === 'light') {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            }

            // if NOT set via local storage previously
        } else {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
        }

    });
}