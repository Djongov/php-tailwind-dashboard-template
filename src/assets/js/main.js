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
        //console.log(`Found tableId: ${tableId}`);
        const editModalTextResult = document.getElementById(`${tableId}-edit-modal-text-result`);
        //console.log(`Found editModalTextResult: ${editModalTextResult}`);
        const saveEditMotal = document.getElementById(`${tableId}-save-edit-modal-text`);
        //console.log(`Found saveEditMotal: ${saveEditMotal}`);
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
                fetch('/system/update-records', {
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
                    saveEditMotal.innerText = 'Save';
                    editModalTextResult.innerHTML = `<p class="w-fit font-bold text-green-500">${text}</p>`;
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
                    fetch('/system/delete-records', {
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
                            /*
                            // Delete the rows instead of reloading
                            // `data` might look like this `system_log_length=10&row[]=9278&row[]=9277&deleteRecords=system_log` so i get all the row[]s. Thanks to ChatGPT for this suggestion, didn't know getAll even existed
                            const rowIds = data.getAll('row[]');
                            console.log(`${rowIds.length} rows deleted`);
                            // Loop through and select it, delete if selected properly
                            rowIds.forEach(rowId => {
                                const rowToDelete = document.querySelector(`tr[data-row-id="${rowId}"]`);
                                if (rowToDelete) {
                                    rowToDelete.remove();
                                }
                            });

                            // Decrement the total and filtered results
                            totalResults.innerText -= rowIds.length;
                            filteredResults.innerText = countVisibleRows(tableId);
                            selectedResults.innerText -= +rowIds.length;
                            // Deselect select-all
                            document.querySelector(`#${tableId} input.select-all`).checked = false;
                            
                            
                            const visibleRowCount = table.querySelectorAll('tbody > tr:not(.hidden)').length;
                            console.log(visibleRowCount);
                            
                            // We need to find out if the deleted rows are leaving the table empty or not. However, need to take care of pagination
                            */
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
                        fetch('/system/delete-records', {
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
                            deleteLoadingScreen.classList.add('hidden');
                            if (text === "1") {
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

        // Iterate through the unique values in the column, create options for the select element
        column.data().unique().sort().each(function (d, j) {
            if (d !== null) {
                // Truncate long select fields and add a title for hover
                let optionText = d;
                if (optionText.length > 90) {
                    optionText = optionText.substring(0, 90) + '...';
                    // For truncated options, have a title that has the full value so it can be visible
                    select.append(`<option value="${d}" title="${d}">${optionText}</option>`);
                } else {
                    select.append(`<option value="${d}">${optionText}</option>`);
                }
            }
        });

        // Repopulate the select element based on the current search filter
        const currSearch = column.search();
        if (currSearch) {
            const searchValue = currSearch.substring(1, currSearch.length - 1);
            select.val(searchValue);
            select.addClass('border-red-500');
            select.addClass('dark:border-red-500');
        }

    });
};
