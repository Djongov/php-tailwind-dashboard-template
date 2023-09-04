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

/* Back Button */
const backButtons = document.querySelectorAll('.back-button');

if (backButtons.length > 0) {
    backButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (history.length > 1) {
                history.back();
            } else {
                location.href = '/'
            }
        }, false)
    });
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

/* Scroll to top button */

// Actual snooth scroll to top function. /8 shows how smooth or quick it should do it
const scrollToTop = () => {
    const c = document.documentElement.scrollTop || document.body.scrollTop;
    if (c > 0) {
        window.requestAnimationFrame(scrollToTop);
        window.scrollTo(0, c - c / 8);
    }
};

// Scroll to top button and function
const backToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

//Get the button
let scrollToTopButton = document.getElementById("btn-back-to-top");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function () {
    scrollFunction();
};

const scrollFunction = () => {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        scrollToTopButton.style.display = "block";
    } else {
        scrollToTopButton.style.display = "none";
    }
}
// When the user clicks on the button, scroll to the top of the document
scrollToTopButton.addEventListener("click", backToTop);


/* DataGrid DataTable

Here are the functions that are for DataGrid display. Using DataTables

*/
const countAllCheckedCheckboxes = (tableId) => {
    return document.querySelectorAll(`#${tableId} > tbody > tr > td > input[type="checkbox"]:checked`).length;
}
/*
const countAllInvisibleCheckedCheckboxes = (tableId) => {
    return document.querySelectorAll(`#${tableId} > tbody > tr[style*="display: none;"] > td > input[type="checkbox"]:checked`).length;
}
*/
const countVisibleRows = (tableId) => {
    return document.querySelectorAll(`#${tableId} > tbody > tr:not([style*="display: none;"])`).length;
}


const tables = document.querySelectorAll('table.buildtable');

//console.log('tables: ' + tables.length);

if (tables.length > 0) {
    tables.forEach(table => {
        // Let's get some elements we will be using
        const tableId = table.id;
        const editModalTextResult = document.getElementById(`${tableId}-edit-modal-text-result`);
        const saveEditMotal = document.getElementById(`${tableId}-save-edit-modal-text`);
        const massDeleteModalTriggerer = document.getElementById(tableId + '-mass-delete-modal-trigger');
        let totalResults = document.getElementById(tableId + '-total');
        let selectedResults = document.getElementById(tableId + '-selected');
        let filteredResults = document.getElementById(tableId + '-filtered');
        filteredResults.innerText = countVisibleRows(tableId);
        const massDeleteModalText = document.getElementById(tableId + '-mass-delete-modal-text');
        const deleteLoadingScreen = document.getElementById(tableId + '-delete-loading-screen');

        const saveEditForm = document.getElementById(tableId + '-edit-data-form');
        if (saveEditForm) {
            saveEditForm.addEventListener('submit', (evt) => {
                evt.preventDefault();
            }, false);
        }

        // Calculate how many results we have on each search
        const searchInputs = document.querySelectorAll(`#${tableId} > thead > tr > th > input.js-filter`);
        if (searchInputs.length > 0) {
            searchInputs.forEach(input => {
                ['change', 'keyup', 'keydown'].forEach(evt => {
                    input.addEventListener(evt, () => {
                        filteredResults.innerText = countVisibleRows(tableId);
                    }, false);
                })
            });
        }

        if (saveEditMotal) {
            saveEditMotal.addEventListener('click', () => {
                saveEditMotal.innerText = 'Please wait...';
                const data = new URLSearchParams(new FormData(saveEditForm));
                fetch('/api/update-records', {
                    method: 'post',
                    // Let's send this secret header
                    headers: {
                        'secretHeader': 'badass'
                    },
                    body: data,
                    redirect: 'manual'
                }).then(response => {
                    if (response.status === 0) {
                        location.reload();
                    }
                    if (response.status >= 400) {
                        saveEditMotal.innerText = 'Retry';
                        response.json().then(errorData => {
                            editModalTextResult.innerHTML = `<p class="ml-4 w-fit font-bold text-red-500">${errorData.error}</p>`;
                        }).catch(error => {
                            editModalTextResult.innerHTML = `<p class="ml-4 w-fit font-bold text-red-500">Error occurred, please try again later.</p>`;
                        });
                    }
                    return response.text();
                }).then(text => {
                    saveEditMotal.innerText = 'Save';
                    editModalTextResult.innerHTML = `<p class="ml-4 w-fit font-bold text-green-500">${text}</p>`;
                    if (text === 'Success') {
                        window.location.reload();
                    } else {
                        alert(text);
                    }
                });

            }, false);
        }
        // Disable it after Flowbite's JS
        if (massDeleteModalTriggerer) {
            massDeleteModalTriggerer.disabled = true;
        }
        // Get all the checkboxes in the table
        const allTableCheckboxes = document.querySelectorAll(`#${tableId} > tbody > tr > td > input[type=checkbox]`);
        if (allTableCheckboxes.length > 0) {
            allTableCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    // make row focused, 2 parent nodes behind
                    if (checkbox.checked) {
                        checkbox.parentNode.parentNode.classList.add('bg-gray-200', 'text-black');
                    } else {
                        checkbox.parentNode.parentNode.classList.remove('bg-gray-200', 'text-black');
                    }
                    const allTableCheckedCheckboxes = countAllCheckedCheckboxes(tableId);
                    selectedResults.innerText = allTableCheckedCheckboxes;
                    filteredResults.innerText = countVisibleRows(tableId);
                    massDeleteModalText.innerText = 'Are you sure you want to delete ' + allTableCheckedCheckboxes + ' entries?';
                    massDeleteModalTriggerer.disabled = (allTableCheckedCheckboxes > 0) ? false : true;
                }, false);
            })
        }
        //
        const deleteForms = document.querySelectorAll(`#${tableId}-container > form.delete-selected-form`);
        //console.log(`Found deleteForms ${deleteForms.length} in ${tableId}`);

        if (deleteForms.length > 0) {
            deleteForms.forEach(form => {
                //console.log(form);
                form.addEventListener('submit', (event) => {
                    deleteLoadingScreen.classList.remove('hidden');
                    event.preventDefault();
                    const data = new URLSearchParams(new FormData(form));
                    fetch('/api/delete-records', {
                        method: 'post',
                        // Let's send this secret header
                        headers: {
                            'secretHeader': 'badass'
                        },
                        body: data,
                        redirect: 'manual'
                    }).then(response => {
                        if (response.status === 0) {
                            location.reload();
                        }
                        return response.text();
                    }).then(text => {
                        deleteLoadingScreen.classList.add('hidden');
                        if (text === "Success") {
                            window.location.reload();
                        } else {
                            //console.log('deleteForms error: ' + text);
                            alert(text);
                        }
                    });
                }, false);
            })
        }
        // Delete
        const deleteButtons = document.querySelectorAll(`#${tableId} > tbody > tr > td > button.delete`);
        //console.log(`Found deleteButtons ${deleteButtons.length} in ${tableId}`);

        if (deleteButtons.length > 0) {
            deleteButtons.forEach(button => {
                button.addEventListener('click', (event) => {
                    let choice = confirm('Are you sure you want to delete?');
                    if (choice) {
                        //console.log('Using loadscreen ' + deleteLoadingScreen.id + ' to delete');
                        deleteLoadingScreen.classList.remove('hidden');
                        fetch('/api/delete-records', {
                            method: 'post',
                            // Let's send this secret header
                            headers: {
                                'secretHeader': 'badass'
                            },
                            body: new URLSearchParams({
                                'id': event.target.dataset.id,
                                'table': event.target.dataset.table
                            }),
                            redirect: 'manual'
                        }).then(response => {
                            if (response.status === 0) {
                                location.reload();
                            }
                            if (response.status >= 400) {
                                response.json().then(errorData => {
                                    alert(errorData.error);
                                }).catch(error => {
                                    alert('Error occurred, please try again later');
                                });
                            }
                            return response.text();
                        }).then(text => {
                            console.log(text);
                            deleteLoadingScreen.classList.add('hidden');
                            if (text === "Success") {
                                // Remove the table row
                                let td = event.target.parentNode;
                                let tr = td.parentNode;
                                tr.parentNode.removeChild(tr);

                                // Decrement

                                totalResults.innerText--;
                                filteredResults.innerText = countVisibleRows(tableId);
                            } else {
                                alert(text);
                            }
                        });
                    } else {
                        event.preventDefault();
                    }
                }, false);
            });
        }

        // Edit buttons
        const editButtons = document.querySelectorAll(`#${tableId} > tbody > tr > td > button.edit`);
        //console.log(`Found editButtons ${editButtons.length} in ${tableId}`);

        if (editButtons.length > 0) {
            editButtons.forEach(button => {
                button.addEventListener('click', (event) => {
                    let editModalText = document.getElementById(tableId + '-edit-modal-text');
                    editModalText.innerHTML = '<p class="text-gray-900 dark:text-gray-300">Loading data please wait...</p>';
                    fetch('/api/get-records', {
                        method: 'post',
                        // Let's send this secret header
                        headers: {
                            'secretHeader': 'badass'
                        },
                        body: new URLSearchParams({
                            'id': event.target.dataset.id,
                            'table': event.target.dataset.table
                        }),
                        redirect: 'manual'
                    }).then(response => {
                        if (response.status === 0) {
                            location.reload();
                        }
                        return response.text();
                    }).then(text => {
                        editModalText.innerHTML = '';
                        deleteLoadingScreen.classList.add('hidden');
                        editModalText.innerHTML = text;
                    });
                }, false);
            });
        }
        /* Check all checkboxes functionality */
        // Get the "All" checkbox in the table
        // So basically find the checkbox all checkbox and bind a click event on it
        $(document).on("change", `#${tableId} input.select-all`, function (event) {
            //$(`#${tableId} > thead > tr > th > input[type=checkbox].select-all`).click(function(event) {
            // Find all the visible checkboxes, jquery returns an object so we user Object.entries() to conver to array so we can forEach it and get through the individual rows in the table
            Object.entries($(`#${tableId} tbody tr:visible :checkbox`).prop('checked', this.checked).closest('tr')).forEach(([index, row]) => {
                // Weirdly jquery returns an extra key in the array that's not an html element, so it throws errors unless we do this check
                if (row instanceof Element) {
                    // If select all checkbox is checked, change the color of the row
                    if (event.target.checked) {
                        row.classList.add('bg-gray-200', 'text-black');
                    } else {
                        // Else remove the extra classes
                        row.classList.remove('bg-gray-200', 'text-black');
                    }
                }
            });
            // After this action, we need to update the "Selected" count, as well as tell the modal how many rows we have selected         
            const allTableCheckedCheckboxes = countAllCheckedCheckboxes(tableId);
            selectedResults.innerText = allTableCheckedCheckboxes;
            massDeleteModalText.innerText = 'Are you sure you want to delete ' + allTableCheckedCheckboxes + ' entries?';
            massDeleteModalTriggerer.disabled = (allTableCheckedCheckboxes > 0) ? false : true;
        });
        /* NO JQUERY ATTEMPT for check all boxes
        const checkAllCheckbox = document.querySelectorAll(`#${tableId} > thead > tr > th > input[type=checkbox].select-all`);
        // Bind a click event to that All Checkbox
        checkAllCheckbox[0].addEventListener('click', (event) => {
            // Go through all the other checkboxes
            allTableCheckboxes.forEach(checkbox => {
                // If All is checked, make all the other checkboxes checked
                if (!event.target.checked && checkbox.style.display === 'none') {
                    checkbox.checked = false;
                    checkbox.parentNode.parentNode.style.background = '';
                    checkbox.parentNode.parentNode.style.color = '';
                } else {
                    checkbox.checked = true;
                    checkbox.parentNode.parentNode.style.background = 'gray';
                    checkbox.parentNode.parentNode.style.color = 'white';
                }
            });
            // Each time the All is clicked, calculate the checked boxes and also change the modal count
            const allTableCheckedCheckboxes = countAllCheckedCheckboxes(tableId);
            selectedResults.innerText = allTableCheckedCheckboxes;
            massDeleteModalText.innerText = 'Are you sure you want to delete ' + allTableCheckedCheckboxes +  ' entries?';
            massDeleteModalTriggerer.disabled = (allTableCheckedCheckboxes > 0) ?  false : true;
        });
        */
    });
}

const drawDataGrid = (id) => {
    const tableWrapper = $('<div style="max-height: 600px; overflow: auto;"></div>'); // Create a wrapper div for the table
    const table = $(`#${id}`).DataTable({
        ordering: false, // Need to make it work so it orders from the 1st row not the 2nd where the filters are
        order: [[0, 'asc']],
        /*
        scrollY: 600,
        scrollX: 600,
        */
        //scrollCollapse: false,
        paging: true,
        pagingType: 'full_numbers',
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        //stateSave: true,
        createdRow: function (row, data, dataIndex) {
            $(row).attr('tabindex', dataIndex)
            $(row).addClass('focus:outline-none focus:bg-gray-300 focus:text-gray-900 dark:focus:bg-gray-700 dark:focus:text-amber-500');
        },
        "columnDefs": [{
            "targets": "_all",
            "createdCell": function (td, cellData, rowData, row, col) {
                $(td).addClass('py-4 px-6 border border-slate-400 max-w-md break-words');
            }
        }],
        initComplete: function () {
            $(`#${id}-loading-table`).remove();
            document.getElementById(`${id}`).classList.remove('hidden');
        },
    });
    $(`#${id}`).wrap(tableWrapper); // Wrap the table with the wrapper div
    return table;
}

/**
 * Builds select filters for a DataTable.
 *
 * @param {DataTable} table - The DataTable instance.
 * @param {string} tableId - The ID of the table element.
 * @param {number[]} columnSkipArray - An array of column indexes to skip.
 * @param {boolean} extraColumns - Indicates whether to skip the first and last columns regardless of columnSkipArray. Boolean should be "1" for true and anything else for false
 */
const buildDataGridFilters = (table, tableId, columnSkipArray, extraColumns) => {
    const isExtraColumns = extraColumns === "1"; // Convert string to boolean

    // Loop through each column of the DataTable
    table.columns().every(function (col) {
        // Check if the current column should be skipped based on conditions
        if ((isExtraColumns && (col === 0 || col === table.columns().indexes().length - 1)) || columnSkipArray.includes(col)) {
            return;
        }
        const column = table.column(this, { search: 'applied' }); // Get the DataTable column object

        // Create a select element and append it to the appropriate table header cell. (1) in this case is the 2nd thead so it doesn't do it on the first where the column names are
        const select = $('<select class="text-center m-1 p-1 text-sm text-gray-900 border border-gray-300 rounded bg-gray-50 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"><option value="">No filter</option></select>')
            .appendTo($(`#${tableId} thead tr:eq(1) th`).eq(column.index()).empty())
            .on('change', function () {
                const val = $.fn.dataTable.util.escapeRegex(
                    $(this).val()
                );

                // Apply the selected filter value to the column and redraw the table
                column
                    .search(val ? '^' + val + '$' : '', true, false)
                    .draw();
            });

        // Calculate the maximum width for the select options
        const maxOptionWidth = Math.min(
            $(column.header()).outerWidth() || select.width(),
            150
        ); // You can adjust the maximum width as needed

        // Iterate through the unique values in the column, create options for the select element
        column.data().unique().sort().each(function (d, j) {
            if (d !== null) {
                // Truncate long select fields and add a title for hover
                let optionText = d;
                if (optionText.length > maxOptionWidth / 2) {
                    optionText = optionText.substring(0, maxOptionWidth / 2) + '...';
                }
                // Append the option with the selected attribute if necessary
                select.append(`<option value="${d}" title="${d}">${optionText}</option>`);
            }
        });

        // Repopulate the select element based on the current search filter
        const currSearch = column.search();
        if (currSearch) {
            const searchValue = currSearch.substring(1, currSearch.length - 1);
            select.val(searchValue); // Set the selected value

            // Loop through the options and set the 'selected' attribute explicitly for the matched option
            select.find('option').each(function () {
                const optionValue = $(this).val();
                // We need to replace the special characters from searchValue as searchValue comes with escaped special characters from column.search(). We want to apply the selected prop for the currently selected option so it's visible what has been filtered right now
                if (optionValue === searchValue.replace(/\\/g, '')) {
                    $(this).prop('selected', true);
                } else {
                    $(this).prop('selected', false);
                }
            });

            select.addClass('border-red-500');
            select.addClass('dark:border-red-500');
        }
    });
};

// This is the fucntion that will create the modal for the form submission if confirm class on the form exists
const generateModal = (text, id) => {
    // Create the html element div
    const modalDiv = document.createElement('div');
    // The modal HTML
    const html = `<div id="${id}-mass-delete-modal" tabindex="-1" class="fixed inset-0 flex items-center justify-center z-50" aria-hidden="true">
        <div class="relative p-4 w-full max-w-md h-full md:h-auto">
            <div class="relative p-4 max-w-md w-full mx-auto bg-white border border-gray-400 dark:border-gray-300 rounded-lg shadow dark:bg-gray-700">
                <button id="${id}-x-cancel" type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-toggle="${id}-modal">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-6 text-center">
                    <svg aria-hidden="true" class="mx-auto mb-4 w-14 h-14 text-gray-400 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 id="${id}-modal-text" class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">${text}</h3>
                    <button id="${id}-submit" data-modal-toggle="${id}-modal" type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                        Yes, I\'m sure
                    </button>
                    <button id="${id}-cancel" data-modal-toggle="${id}-modal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">No, cancel</button>
                </div>
            </div>
        </div>
    </div>`;
    // Append
    modalDiv.innerHTML += html;
    // Return
    return modalDiv;
}

// This function handles the form submission, takes care of the result div where data return is displayed
const handleFormFetch = (form, currentEvent, resultType) => {
    const resultDiv = form.nextSibling;
    if (resultDiv && resultDiv.classList.contains('generic-form-submit-div')) {
        resultDiv.remove();
    }
    let newResultDiv = document.createElement('div');
    newResultDiv.classList.add('ml-4', 'my-4', 'text-gray-900', 'dark:text-gray-300', 'generic-form-submit-div', 'break-words');
    form.parentNode.insertBefore(newResultDiv, form.nextSibling);
    const initialClasses = currentEvent.submitter.className;
    const initialSubmitName = currentEvent.submitter.innerText;
    currentEvent.submitter.innerHTML = `
    <div role="status">
        <svg aria-hidden="false" class="inline w-6 h-6 text-gray-400 dark:text-white animate-spin fill-${themeValue}-600 dark:fill-${themeValue}-500" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
        </svg>
    </div>
    `;
    // Pick up the data from the form and urlencode is for POST
    const data = new URLSearchParams(new FormData(form));
    // Fetch function
    fetch(form.action, {
        method: 'post',
        headers: {
            'secretHeader': 'badass'
        },
        body: data,
        redirect: 'manual'
    })
    // Handle response
    .then(response => {
        // If response is redirect (0) or 401 return by the server, usually token expired, reload the page
        if (response.status === 0 || response.status === 401) {
            // Handle fetch interruption
            console.log(response);
            newResultDiv.innerHTML = '<p class="font-semibold text-red-500">Fetch interrupted. Refreshing page</p>';
            location.reload();
        } else {
            return response.text();
        }
    })
    // Hanle after the response comes
    .then(text => {
        if (typeof text === 'undefined') {
            return;
        }
        newResultDiv.innerHTML = '';
        // If data-reload is on the form, instruct to reload the pagte
        if (form.getAttribute("data-reload") === "true") {
            location.reload();
        // Otherwise display the returned text
        } else {
            if (resultType === 'html') {
                newResultDiv.innerHTML = text;
            } else {
                newResultDiv.innerText = text;
            }
            // Set the button text to what it was, because it was loading until now
            currentEvent.submitter.innerText = initialSubmitName;
        }
    })
    // Catch errors
    .catch(error => {
        // Handle other errors, including network errors
        console.error('Error:', error);

        // Display the error message in newResultDiv
        newResultDiv.innerHTML = '<p class="font-semibold text-red-500">An error occurred: ' + error.message + '</p>';
    });
}

// This function handles form submission when confirm class exists
const handleFormSubmitWithConfirm = (currentEvent, form, initialSubmitName, resultType) => {
    // Create a random Id for the modal
    const randomId = generateUniqueId(4);
    // See if we are passing text for the modal via the form data-confirm
    const confirmText = form.getAttribute('data-confirm');
    // If we are set it, if not default to Are you sure?
    const modalText = (confirmText) ? form.getAttribute("data-confirm") : 'Are you sure?';
    // Generate the modal
    const modal = generateModal(modalText, randomId);
    // Insert the modal after the form
    form.parentNode.insertBefore(modal, form.nextSibling);
    // Let's get the buttons of the modal so we can attach event listeners
    const confirmButton = document.getElementById(`${randomId}-submit`);
    const cancelButton = document.getElementById(`${randomId}-cancel`);
    const xCancelButton = document.getElementById(`${randomId}-x-cancel`);
    // There are two close buttons so put them in an array
    const cancelButtonsArray = [cancelButton, xCancelButton];
    // Hide the modal
    modal.classList.remove('hidden');
    // So on Yes click on the modal, remove the modal and start the fetch function
    confirmButton.addEventListener('click', () => {
        modal.remove();
        handleFormFetch(form, currentEvent, resultType)
    });
    // If user cancels the modal, again remove the modal and return to the initial button text
    cancelButtonsArray.forEach(button => {
        button.addEventListener('click', () => {
            modal.remove();
            currentEvent.submitter.innerText = initialSubmitName;
        })
    })
}

/* Generic forms submit functionality */

// Query all forms produced by the backend
const genericForms = document.querySelectorAll('form.generic-form');
// Set the them (used mainy in loading div)
const themeValue = 'sky';

// If there are any forms
if (genericForms.length > 0) {
    // Loop through each
    genericForms.forEach(form => {
        // Attach submit event
        form.addEventListener('submit', (event) => {
            // Prevent normal submitting
            event.preventDefault();
            // Check the result type (text or html) if declared in the form
            let resultType = form.getAttribute('data-result');
            // Remember the initial button text
            const initialButtonText = event.submitter.innerText;
            // If there is a confirm required (via confirm class)
            if (form.classList.contains('confirm')) {
                // use the handle with confirm function
                handleFormSubmitWithConfirm(event, form, initialButtonText, resultType)
            } else {
                // Otherwise just proceed with the fetch
                handleFormFetch(form, event, resultType);
            }
        });
    })
}