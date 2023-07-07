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

const generateUniqueId = (length) => {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const charactersLength = characters.length;
    let id = '';

    for (let i = 0; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * charactersLength);
        id += characters.charAt(randomIndex);
    }

    return id;
};

const copyToClipboard = () => {
    // Find all elements with class "c0py"
    const elements = document.getElementsByClassName("c0py");

    const theme = document.querySelector('input[type="hidden"][name="theme"]').value;

    // Iterate over the found elements
    Array.from(elements).forEach(element => {
        // Create the copy button
        const button = document.createElement("button");
        button.classList.add('inline-flex', 'items-center');

        // Set button content as the provided SVG
        button.innerHTML = `<svg class="w-6 h-6 text-${theme}-500 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="8" y="8" width="12" height="12" rx="2" /><path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" />
        </svg>`;

        // Set the tooltip text
        button.title = "Copy to clipboard";

        // Create the tooltip div element
        const div1 = document.createElement('div');
        const uniqueId = `tooltip-${generateUniqueId(8)}`;
        div1.setAttribute('id', uniqueId);
        div1.setAttribute('role', 'tooltip');
        div1.classList.add('absolute', 'hidden', `bg-${theme}-500`, 'text-gray-50', 'p-1', 'text-sm');

        // Create a container element
        const container = document.createElement('div');
        container.classList.add('copy-container', 'inline-block');

        // Append the new div elements to the container
        container.appendChild(button);
        container.appendChild(div1);

        // Append the container after the element
        element.insertAdjacentElement('afterend', container);

        // Set the click event handler for copying to clipboard
        button.addEventListener("click", () => {
            const textToCopy = element.textContent || element.innerText;
            navigator.clipboard.writeText(textToCopy)
                .then(() => {
                    div1.textContent = 'Copied';
                    console.log("Text copied to clipboard:", textToCopy);

                    // Change the color to red
                    button.querySelector("svg").classList.add("text-red-500");
                })
                .catch(error => {
                    div1.textContent = 'Failed to copy';
                    console.error("Failed to copy text:", error);
                });

            // Toggle the visibility of the tooltip
            div1.classList.toggle('hidden');

            // Hide the tooltip after 2 seconds
            setTimeout(() => {
                div1.classList.add('hidden');
            }, 1000);
        });

        // Position the tooltip relative to the button
        button.addEventListener("mouseenter", () => {
            const buttonRect = button.getBoundingClientRect();
            const buttonCenterX = buttonRect.left + buttonRect.width / 1;
            const buttonTop = buttonRect.top;
            const tooltipHeight = div1.offsetHeight;

            div1.style.left = `${buttonCenterX}px`;
            div1.style.top = `${buttonTop - tooltipHeight}px`;
        });
    });
};

// Call the function when the DOM content is loaded
document.addEventListener("DOMContentLoaded", copyToClipboard);

// Theme changer from user settings
const themeForm = document.getElementById('theme-change');

if (themeForm) {
    themeForm.method = 'post';
    themeForm.elements[0].addEventListener('change', () => {
        themeForm.submit();
    }, false);
}
