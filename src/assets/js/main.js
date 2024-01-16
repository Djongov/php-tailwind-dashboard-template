/* Dark/Light Theme Changes */
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
/*
// Now we need to search for toggle checkboxes with class "awm-toggle" so we can add event listener so when it is checked or not checked to toggle the value between 0 and 1
const toggleCheckboxes = document.querySelectorAll('input.awm-toggle');

if (toggleCheckboxes.length > 0) {
    toggleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            checkbox.value = (checkbox.checked) ? 1 : 0;
            // Now we need to fix the issue where chekbox with value 0 is not being sent in forms so we need to add a hidden input with the same name and value 0
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = checkbox.name;
            hiddenInput.value = checkbox.value;
            // Now let's find the form which is the parent of the checkbox and append the hidden input to it
            checkbox.parentNode.parentNode.appendChild(hiddenInput);
            console.log(`Checkbox ${checkbox.name} value is ${checkbox.value}`);
        }, false);
    });
}
*/

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

// I want to set a constant called 'theme' that will be used across the script, its value needs to be taken from 'input[type="hidden"][name="theme"]' if there such an elememt, if not it needs to be 'sky'


// Initiate theme across the script
const themeInput = document.querySelector('input[type="hidden"][name="theme"]');
const theme = themeInput ? themeInput.value : 'sky';


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

const isJSONString = (string) => {
    try {
        JSON.parse(string);
        return true;
    } catch (error) {
        return false
    }
}

const decodeHTMLEntities = (text) => {
    var element = document.createElement('textarea');
    element.innerHTML = text;
    return element.value;
}

const escapeHtml = (input) => {
    return String(input).replace(/[&<>"'\/]/g, function (s) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
            '/': '&#x2F;'
        }[s];
    });
}

const copyToClipboard = () => {
    // Find all elements with class "c0py"
    const elements = document.getElementsByClassName("c0py");

    const theme = document.querySelector('input[type="hidden"][name="theme"]').value;
    // Loop if data-processed is true
    // Iterate over the found elements
    Array.from(elements).forEach(element => {
        // Only proceed if data-hasCopyToClipboard is set
        if (!element.dataset.hasCopyToClipboard) {
            // Apply it to the element so we don't process it again
            element.dataset.hasCopyToClipboard = true;
            // Create the copy button
            const button = document.createElement("button");
            button.classList.add('inline-flex', 'items-center');
            // Add a data attribute that will tell us that we've already processed this element
            button.setAttribute('data-processed', 'true');
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
            }
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
                        if (response.status === 0 || response.status === 401 || response.status === 403) {
                            location.reload();
                        }
                        return response.json();
                    }).then(json => {
                        if (json.error || json.result.error) {
                            deleteLoadingScreen.classList.add('hidden');
                            alert(`${json.error || json.result.error}`);
                        } else {
                            location.reload();
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
                            if (response.status === 0 || response.status === 401 || response.status === 403) {
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
        const editButtons = document.querySelectorAll(`#${tableId} button.edit`);

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
    const tableWrapper = $('<div class="overflow-auto max-h-[44rem]"></div>'); // Create a wrapper div for the table
    const table = $(`#${id}`).DataTable({
        ordering: true, // Need to make it work so it orders from the 1st row not the 2nd where the filters are
        order: [[0, 'asc']],
        // Make sure that the ordering is done on the 1st row not the 2nd where the filters are
        orderCellsTop: true,
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


const drawDataGridFromData = (json, skeletonId, columnArray) => {
    const tableWrapper = $('<div class="overflow-auto max-h-[44rem]"></div>'); // Create a wrapper div for the table
    const tableHeaders = columnArray.map(column => ({
        title: column.name
        //data: column.name
    }));
    /*
    json = json.map(row => {
        const obj = {};
        row.forEach((value, index) => {
            obj[tableHeaders[index].data] = value;
        });
        return obj;
    });
    
    json.forEach(row => {
        parsedLog.push(row);
    });
    */
    // Create the table and add data
    const table = $(`#${skeletonId}`).DataTable({
        ordering: true,
        data: json,
        columns: tableHeaders,
        paging: true,
        pagingType: 'full_numbers',
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
        createdRow: function (row, data, dataIndex) {
            $(row).attr('tabindex', dataIndex);
            $(row).addClass('focus:outline-none focus:bg-gray-300 focus:text-gray-900 dark:focus:bg-gray-700 dark:focus:text-amber-500');
        },
        // now let's apply a common set of classes to all <td>
        "columnDefs": [{
            "targets": "_all",
            "createdCell": function (td, cellData, rowData, row, col) {
                // Apply the common class names for each table cell
                $(td).addClass('py-4 px-6 border border-gray-400 max-w-md break-words');
                // Let's deal with long text and truncate it so it doesn't make our table extremely long
                if (td.innerHTML.length > 60) {
                    $(td).addClass('truncate');
                    $(td).addClass('overflow-hidden');
                    $(td).addClass('max-w-2xl');
                    // And here only show 60 cahracters of the text
                    $(td).text(decodeHTMLEntities(cellData).substring(0, 120) + '...');
                    // Add the full text as a title so it's visible on hover
                    $(td).attr('title', decodeHTMLEntities(cellData));
                } else {
                    // If we detect that cellData is an IP address, let's surround it with a link to the IP address page
                    const ipv4Regex = /^(\d{1,3}\.){3}\d{1,3}$/;
                    const ipv6Regex = /^(?:[0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}$/;
                    if (ipv4Regex.test(cellData) || ipv6Regex.test(cellData)) {
                        $(td).html(`<a class="underline" target="_blank" href="/dashboard/tools/ip-address?ip=${cellData}">${cellData}</a>`);
                    } else {
                        // Sanitize all the cellData as it's WAF logs so it can contain malicious code
                        $(td).text(decodeHTMLEntities(cellData));
                    }
                }
            }
        }],
        initComplete: function () {
            document.getElementById('loading-screen').remove();
        }
    });

    // Create filter rows outside of header callback
    // First, get the first row of the thead
    $(`#${skeletonId} thead tr:first-child th`).append(' <span class="text-xs text-gray-400 cursor-pointer">&#x25B2;&#x25BC;</span>');
    const filtersRow = $('<tr></tr>').insertAfter($(`#${skeletonId} thead tr`));
    tableHeaders.forEach(header => {
        filtersRow.append(`<th class="py-4 px-6 border border-gray-400 max-w-md break-words"></th>`);
    });

    $(`#${skeletonId}`).wrap(tableWrapper); // Wrap the table with the wrapper div
    return table;
};

/**
 * Builds select filters for a DataTable.
 *
 * @param {DataTable} table - The DataTable instance.
 * @param {string} tableId - The ID of the table element.
 * @param {Array} columnSkipArray - An array of column indexes to skip.
 */
const buildDataGridFilters = (table, tableId, columnSkipArray) => {
    // First check if columnSkipArray is passed and if not set it to empty array
    if (!columnSkipArray) {
        columnSkipArray = [];
    }
    // Loop through each column of the DataTable
    table.columns().every(function (col) {
        if (columnSkipArray.includes(col)) {
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

        $(`#${tableId} thead tr:eq(1) th`).addClass('p-4');
        $(`#${tableId} thead tr:eq(1) th`).addClass('border');
        $(`#${tableId} thead tr:eq(1) th`).addClass('border-slate-400');
    });
};

const buildDataGridFiltersData = (table, tableId) => {
    const filtersRow = $(`#${tableId} thead tr:eq(1)`); // Get the second row in the thead

    // Clear existing filters by emptying the filter cells
    filtersRow.find('tr').empty();

    // Loop through each column of the DataTable
    table.columns().every(function (col) {
        const column = table.column(this, { search: 'applied' }); // Get the DataTable column object

        const filterCell = filtersRow.find('th').eq(column.index());
        filterCell.html(''); // Clear existing content

        // Create a select element and set it as the HTML content of the table header cell
        const select = $('<select class="max-w-sm text-center m-1 p-1 text-sm text-gray-900 border border-gray-300 rounded bg-gray-50 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500" title="filter"><option value="">No filter</option></select>')
            .appendTo(filterCell)
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
                    console.log(`options length ${optionText.length}`);
                    optionText = optionText.substring(0, maxOptionWidth / 2) + '...';
                }
                // Append the option with the selected attribute if necessary
                select.append(`<option value="${d}" title="${optionText}">${optionText}</option>`);
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
        <div class="relative p-4 w-full max-w-md h-full md:h-auto overflow-auto">
            <div class="relative p-4 max-w-md w-full mx-auto bg-white border border-gray-400 dark:border-gray-300 rounded-lg shadow dark:bg-gray-700">
                <button id="${id}-x-cancel" type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-toggle="${id}-modal">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-6 text-center">
                    <svg aria-hidden="true" class="mx-auto mb-4 w-14 h-14 text-gray-400 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 id="${id}-modal-text" class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">${text}</h3>
                    <button id="${id}-submit" data-modal-toggle="${id}-modal" type="submit" class="my-2 text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
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
    //const initialClasses = currentEvent.submitter.className;
    const initialSubmitName = currentEvent.submitter.innerText;
    currentEvent.submitter.innerHTML = `
    <div role="status">
        <svg aria-hidden="false" class="inline w-6 h-6 text-gray-50 dark:text-white animate-spin fill-${theme}-600 dark:fill-${theme}-500" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
        </svg>
    </div>
    `;
    // Pick up the data from the form and urlencode is for POST
    const formData = new FormData(form);
    // If you still want to add specific values for checkboxes, you can do it like this
    const toggleCheckboxes = form.querySelectorAll('input[type="checkbox"]')
    if (toggleCheckboxes.length > 0) {
        toggleCheckboxes.forEach(checkbox => {
            // If the checkbox is checked, set its value to 1, otherwise, set it to 0
            let checkboxValue = checkbox.checked ? 1 : 0;
            formData.set(checkbox.name, checkboxValue);
            console.log(`Setting ${checkbox.name} to ${checkboxValue}`);
        });
    }
    const data = new URLSearchParams(formData);

    const formMethod = form.getAttribute('data-method');
    const csrfToken = form.querySelector('input[name="csrf_token"]').value;

    const fetchOptions = {
        method: formMethod,
        headers: {
            'secretHeader': 'badass',
            'X-CSRF-TOKEN': csrfToken
        },
        redirect: 'manual'
    };

    if (formMethod === 'POST') {
        fetchOptions.headers['Content-Type'] = 'application/x-www-form-urlencoded';
        fetchOptions.body = data;
    } else if (formMethod === 'PUT') {
        fetchOptions.headers['Content-Type'] = 'application/json';

        // Convert form data to a JavaScript object
        const formDataObject = {};
        formData.forEach((value, key) => {
            formDataObject[key] = value;
        });

        fetchOptions.body = JSON.stringify(formDataObject);
    } else if (formMethod !== 'GET' && formMethod !== 'DELETE') {
        fetchOptions.body = data;
    }
    // Fetch function
    fetch(form.action, fetchOptions)
        // Handle response
        .then(response => {
            const contentType = response.headers.get("content-type");
            // If response is redirect (0) or 403 return by the server, usually token expired, reload the page
            if (response.status === 0 || response.status === 403) {
                if (response.type === 'opaqueredirect') {
                    // redirect to the desired page response.url
                    location.reload(response.url);
                } else {
                    // Handle fetch interruption
                    newResultDiv.innerHTML = '<p class="font-semibold text-red-500">Fetch interrupted. Refreshing page</p>';
                    location.reload();
                }
            } else {
                // If data-redirect is on the form, instruct to redirect to the specified url
                // if (form.getAttribute("data-redirect")) {
                //     location.href = form.getAttribute("data-redirect");
                // }
                // if ((form.getAttribute("data-reload") === "true")) {
                //     location.reload();
                // }
                // If returned content-type is not json, return the text
                if (contentType && contentType.indexOf("application/json") === -1) {
                    return response.text();
                }
                return response.json();
            }
            // Hanle after the response comes
        }).then(response => {
            currentEvent.submitter.innerText = initialSubmitName;
            newResultDiv.innerHTML = '';
            if (typeof response === 'undefined') {
                console.log(response);
                return;
            }
            // If the response is of type text()
            if (typeof response === 'string') {
                newResultDiv.innerHTML = `${response}`;
                // If there were copy buttons in the response, let's initiate them
                copyToClipboard();
                // If we have html coming in, it could hold other forms, so let's initiate them. So let's find if there is a form.generic selector in the response
                initiateGenericForms();
                return;
            }
            // Return the button text to the initial state, remove the loading spinner
            // handle error from the front end api processing endpoint
            if (response.error) {
                newResultDiv.classList.add('text-red-500', 'font-semibold', 'ml-0');
                newResultDiv.innerHTML = `<p class="text-red-500 font-semibold ml-0">${JSON.stringify(response.error)}</p>`;
                return;
            }
            // handle error from the remote api processing endpoint
            if (response.result !== 'success') {
                // if response is [object Object] it means it's an error
                console.log(response.data);
                newResultDiv.classList.add('text-red-500', 'font-semibold', 'ml-0');
                newResultDiv.innerHTML = `<p class="text-red-500 font-semibold ml-0">${JSON.stringify(response.data)}</p>`;
            } else {
                // Now successful respones from the API
                // If data-reload is on the form, instruct to reload the page
                if (form.getAttribute("data-reload") === "true") {
                    location.reload();
                    // Otherwise display the returned data
                } else if (form.getAttribute("data-redirect")) {
                    location.href = form.getAttribute("data-redirect");
                    // If data-delete-current-row, delete the current <tr> element
                } else if (form.getAttribute("data-delete-current-row")) {
                    // Now find the closest tr and delete it
                    currentEvent.target.closest("tr").remove();
                    /*
                    let td = currentEvent.target.parentNode.parentNode;
                    let tr = td.parentNode;
                    tr.parentNode.removeChild(tr);
                    */
                } else {
                    console.log(`result type is ${typeof response.data}`)
                    const result = (typeof response.data === 'object') ? JSON.stringify(response.data) : response.data;
                    // If the result type is html, display the response as html
                    if (resultType === 'html') {
                        if (typeof response.data === 'object') {
                            newResultDiv.innerHTML = `<pre class="text-sm font-mono text-gray-800 dark:text-gray-400 whitespace-pre-wrap">${result}</pre>`;
                        } else {
                            newResultDiv.innerHTML = `<p class="font-semibold text-green-500 -ml-4">${result}</p>`;
                        }
                        return;
                    }
                    newResultDiv.innerText = result;
                }
            }
            // Catch errors
        }).catch(error => {
            if (error === null) {
                newResultDiv.innerHTML = `<p class="font-semibold text-red-500">An unknown error occured</p>`;
            } else {
                // Handle other errors, including network errors
                console.error('Error:', error);
                // Display the error message in newResultDiv
                newResultDiv.innerHTML = `<p class="font-semibold text-red-500">An error occurred: ${error.message}</p>`;
            }
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

    if (form.classList.contains('double-confirm')) {
        // diable the submit button on the modal
        confirmButton.disabled = true;
        // create a new input field and add it to the modal
        const newInput = document.createElement("input");
        newInput.type = "text";
        newInput.classList.add('my-4', 'mx-auto', 'bg-gray-50', 'border', 'border-gray-300', 'outline-none', 'text-gray-900', 'text-sm', 'rounded-lg', 'focus:ring-red-500', 'focus:border-red-500', 'block', 'w-full', 'p-2.5', 'dark:bg-gray-700', 'dark:border-gray-600', 'dark:placeholder-gray-400', 'dark:text-white', 'dark:focus:ring-red-500', 'dark:focus:border-red-500');
        confirmButton.parentNode.insertBefore(newInput, confirmButton);

        // Find out the keyword to type
        const keyWord = (form.getAttribute('data-double-confirm-keyword')) ?? null;

        const inputParagraph = document.createElement('p');

        inputParagraph.innerHTML = `Type <span class="break-all">"${keyWord}"</span> to continue`;

        newInput.parentNode.insertBefore(inputParagraph, newInput);

        confirmButton.classList.remove('bg-red-600', 'hover:bg-red-800');
        confirmButton.classList.add('bg-gray-400', 'hover:bg-gray-600');

        newInput.addEventListener('input', () => {
            if (newInput.value === keyWord) {
                confirmButton.classList.remove('bg-gray-400', 'hover:bg-gray-600');
                confirmButton.classList.add('bg-red-600', 'hover:bg-red-800');
                confirmButton.disabled = false;
            } else {
                confirmButton.classList.remove('bg-red-600', 'hover:bg-red-800');
                confirmButton.classList.add('bg-gray-400', 'hover:bg-gray-600');
                confirmButton.disabled = true;
            }
        })

    }
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

/* Search within selects. For this to work the search input needs to be just before the select */
const searchInputs = document.querySelectorAll('input.filterSearch');

if (searchInputs.length > 0) {
    searchInputs.forEach(input => {
        input.addEventListener('input', () => {
            const searchValue = input.value.toLowerCase();
            const select = input.parentNode.querySelector('select');
            const options = select.querySelectorAll('option');
            options.forEach(option => {
                if (option.value.toLowerCase().indexOf(searchValue) > -1) {
                    option.style.display = '';
                    // Also make sure that the option is selected
                    option.selected = true;
                } else {
                    option.style.display = 'none';
                }
            })
        })
    })
}


/* Generic forms submit functionality */
const initiateGenericForms = () => {
    // Query all forms produced by the backend
    const genericForms = document.querySelectorAll('form.generic-form');

    // If there are any forms
    if (genericForms.length > 0) {
        console.log(`Initializing ${genericForms.length} generic forms`);

        // Loop through each
        genericForms.forEach(form => {
            // Check if the event listener is already attached
            if (!form.hasAttribute('data-submit-listener')) {
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
                        handleFormSubmitWithConfirm(event, form, initialButtonText, resultType);
                    } else {
                        // Otherwise just proceed with the fetch
                        handleFormFetch(form, event, resultType);
                    }
                });

                // Mark the form to indicate that the listener is attached
                form.setAttribute('data-submit-listener', 'true');
            }
        });
    }
}

initiateGenericForms();

const changeForms = document.querySelectorAll('form.select-submitter');

if (changeForms.length > 0) {
    changeForms.forEach(form => {
        const currentSelectionIndex = form.firstChild.selectedIndex;
        const formData = new FormData(form);
        const apiAction = formData.get('api-action');
        const organization = formData.get('organization');
        const username = formData.get('username');
        form.addEventListener('change', (event) => {
            const currentSelectedOption = form.firstChild.options[form.firstChild.selectedIndex].value;
            event.preventDefault();
            let modalText = 'Are you sure?';
            // Let's find out if we deal with update membership
            if (apiAction) {
                if (apiAction === 'update-membership') {
                    if (currentSelectedOption === 'Owner') {
                        modalText = `You are choosing to make ${username} an Owner of ${organization} organization. This will convert your role to Contributor and ${username}\'s to Owner. Are you sure you want to proceed?`;
                    } else {
                        modalText = `You are changing the role of ${username} to ${currentSelectedOption} in ${organization}. Are you sure you want to proceed?`;
                    }
                }
            }
            const randomId = generateUniqueId(4);
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
                let loadingDiv = document.createElement('div');
                loadingDiv.classList.add('ml-2');
                loadingDiv.innerHTML = `
                    <div role="status">
                    <svg aria-hidden="false" class="inline mr-2 w-6 h-6 text-gray-200 dark:text-white animate-spin fill-blue-600 dark:fill-amber-500" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                    </svg>
                    </div>
                    `;
                form.parentNode.insertBefore(loadingDiv, form.nextSibling);
                modal.remove();

                fetch(form.action, {
                    method: form.method,
                    body: new URLSearchParams(new FormData(form)),
                    redirect: 'manual'
                }).then(response => response.json()
                ).then(json => {
                    console.log(json);
                    if (json.error) {
                        alert(json.error);
                    } else {
                        loadingDiv.classList.add('text-green-500', 'font-semibold', 'text-xl');
                        loadingDiv.innerHTML = '&#x2713;';
                    }
                })
            });
            // If user cancels the modal, again remove the modal and return to the initial button text
            cancelButtonsArray.forEach(button => {
                button.addEventListener('click', () => {
                    modal.remove();
                    form.firstChild.selectedIndex = currentSelectionIndex;
                })
            })
        })
    })
}

/* Charts */

const createPieChart = (name, parentNodeId, canvasId, containerHeight, containerWidth, labels, data) => {
    let parentDiv = document.getElementById(parentNodeId);
    let containerDiv = document.createElement('div');
    parentDiv.appendChild(containerDiv);
    containerDiv.classList.add('w-80');
    containerDiv.style.height = containerHeight;
    containerDiv.style.width = containerWidth;
    let canvas = document.createElement('canvas');
    canvas.id = canvasId;
    containerDiv.appendChild(canvas);

    // Find out the theme - dark or light
    let theme = '';
    if (localStorage.getItem('color-theme')) {
        if (localStorage.getItem('color-theme') === 'dark') {
            theme = 'dark';
        } else {
            theme = 'light';
        }
    } else {
        theme = 'light';
    }

    const titleColor = (theme === 'dark') ? '#9ca3af' : '#111827';

    const labelColor = (theme === 'dark') ? '#9ca3af' : '#111827';

    let backgroundColorArray = [];
    /*
    let colorScheme = [
        "#25CCF7","#FD7272","#54a0ff","#00d2d3",
        "#1abc9c","#2ecc71","#3498db","#9b59b6","#34495e",
        "#16a085","#27ae60","#2980b9","#8e44ad","#2c3e50",
        "#f1c40f","#e67e22","#e74c3c","#ecf0f1","#95a5a6",
        "#f39c12","#d35400","#c0392b","#bdc3c7","#7f8c8d",
        "#55efc4","#81ecec","#74b9ff","#a29bfe","#dfe6e9",
        "#00b894","#00cec9","#0984e3","#6c5ce7","#ffeaa7",
        "#fab1a0","#ff7675","#fd79a8","#fdcb6e","#e17055",
        "#d63031","#feca57","#5f27cd","#54a0ff","#01a3a4"
    ];
    */
    let colorScheme = [];
    labels.forEach(label => {
        var item = colorScheme[Math.floor(Math.random() * colorScheme.length)];
        // For Malicious confidences let's draw red orange green for the good and bad malicious confidences
        if (name === 'maliciousConfidences') {
            let color = '';
            if (label === "0") {
                color = 'lime';
            } else if (label > 0 && label < 50) {
                color = 'green';
            } else if (label >= 50 && label < 75) {
                color = 'orange';
            } else if (label >= 75 && label <= 80) {
                color = 'crimson';
            } else if (label > 80 && label <= 100) {
                color = 'red';
            } else {
                color = 'purple';
            }
            backgroundColorArray.push(color);
            // For the rest - push from the random array of colors
        } else {
            backgroundColorArray = [
                'rgba(54, 162, 235, 1)', // blue
                'rgba(75, 192, 192, 1)', // green
                'rgba(255, 99, 132, 1)', // red
                'rgba(255, 159, 64, 1)', // orange
                'rgba(153, 102, 255, 1)', // purple
                'rgba(255, 206, 86, 1)', // yellow
                'rgba(255, 0, 0, 1)', // bright red
                'rgba(0, 255, 255, 1)', // cyan
                'rgba(255, 0, 255, 1)', // magenta
                'rgba(128, 128, 128, 1)' // grey
            ];

        }
        //console.log('Assigning color ' + item + ' to chart ' + name);
        colorScheme = colorScheme.filter(element => element !== item);
    })

    //const ctx = canvas.getContext('2d');

    const chart = new Chart(canvas, {
        type: 'pie',
        plugins: [ChartDataLabels],
        data: {
            labels: labels,
            datasets: [
                {
                    //label: name,
                    backgroundColor: backgroundColorArray,
                    data: data,
                    //color: 'red',
                    borderWidth: 0,
                    borderColor: 'rgba(255,255,255, 0.95)',
                    weight: 600,
                }
            ]
        },
        options: {
            hover: {
                mode: null
            },
            responsive: true,
            maintainAspectRatio: true,
            /*
            legendCallback: function(chart) {
                var text = [];
                text.push('<ul class="0-legend">');
                var ds = chart.data.datasets[0];
                var sum = ds.data.reduce(function add(a, b) { return a + b; }, 0);
                for (var i=0; i<ds.data.length; i++) {
                    text.push('<li>');
                    var perc = Math.round(100*ds.data[i]/sum,0);
                    text.push('<span style="background-color:' + ds.backgroundColor[i] + '">' + '</span>' + chart.data.labels[i] + ' ('+ds.data[i]+') ('+perc+'%)');
                    text.push('</li>');
                }
                text.push('</ul>');
                return text.join("");
            },
            */
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        padding: 20,
                        color: labelColor,
                        fontSize: 12,
                        borderWidth: 1,
                        /*
                        generateLabels: function(chart) {
                        var data = chart.data;
                        if (data.labels.length && data.datasets.length) {
                            return data.labels.map(function(label, i) {
                            var text = label;
                            if (text.length > 45) {
                                text = text.substring(0, 10) + '...';
                            }
                            return {
                                text: text,
                                fillStyle: data.datasets[0].backgroundColor[i],
                                strokeStyle: data.datasets[0].borderColor[i],
                                lineWidth: 2,
                                hidden: isNaN(data.datasets[0].data[i]) || chart.getDatasetMeta(0).data[i].hidden,
                                index: i
                            };
                            });
                        }
                        return [];
                        }
                        */
                    }
                },
                // Chart Title on top
                title: {
                    display: true,
                    text: name.replace('_', ' '),
                    padding: {
                        top: 15,
                        bottom: 10
                    },
                    color: titleColor,
                    align: 'center',
                    fullSize: true,
                    font: {
                        weight: 'bold',
                        size: 16
                    },
                    position: 'top'
                },
                // When you hover on a datalabel, show count and stuff
                datalabels: {
                    display: true,
                    align: 'middle',
                    color: '#fff',
                    backgroundColor: '#000',
                    borderRadius: 3,
                    font: {
                        size: 11,
                        lineHeight: 1
                    },
                }
            },
        }
    });
    return chart;
}

// Line chart

const createLineChart = (holder, type, data) => {
    let canvas = document.createElement('canvas');
    holder.appendChild(canvas);
    canvas.height = 200;
    let lineDataSets = [];

    const colors = [
        'rgba(54, 162, 235, 1)', // blue
        'rgba(75, 192, 192, 1)', // green
        'rgba(255, 99, 132, 1)', // red
        'rgba(255, 159, 64, 1)', // orange
        'rgba(153, 102, 255, 1)', // purple
        'rgba(255, 206, 86, 1)', // yellow
        'rgba(255, 0, 0, 1)', // bright red
        'rgba(0, 255, 255, 1)', // cyan
        'rgba(255, 0, 255, 1)', // magenta
        'rgba(128, 128, 128, 1)' // grey
    ]; // Array of colors

    data.forEach((array, index) => {
        let countData = array.Count.replace("[", "").replace("]", "").split(",");
        let calculatedTarget = Object.entries(array)[0][1];

        // Get the color from the colors array based on the index. Uses the remainder operator (%) to cycle through the colors array and assign a color based on the index value.
        let color = colors[index % colors.length];

        lineDataSets.push({
            label: calculatedTarget,
            data: countData,
            borderColor: color,
            backgroundColor: color,
            fill: false,
            tension: 0.1,
        });
    });

    // Now the label
    const timeValues = [];

    for (const obj of data) {
        const timeArray = JSON.parse(obj.TimeGenerated);
        timeValues.push(...timeArray);
    }

    const uniqueTimeValues = Array.from(new Set(timeValues));

    new Chart(canvas, {
        type: 'line',
        data: {
            datasets: lineDataSets,
            labels: uniqueTimeValues,
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: type.toUpperCase().replace("-", " "),
                },
            },
        }
    });

}

const doughnutChart = (name, parentNodeId, height, width, labels, data) => {
    let parentDiv = document.getElementById(parentNodeId);
    let containerDiv = document.createElement('div');
    parentDiv.appendChild(containerDiv);
    containerDiv.classList.add('w-80');
    containerDiv.style.height = height;
    containerDiv.style.width = width;
    let canvas = document.createElement('canvas');
    canvas.id = `canvas-${generateUniqueId(4)}`;
    containerDiv.appendChild(canvas);

    // Find out the theme - dark or light
    let theme = '';
    if (localStorage.getItem('color-theme')) {
        if (localStorage.getItem('color-theme') === 'dark') {
            theme = 'dark';
        } else {
            theme = 'light';
        }
    } else {
        theme = 'light';
    }

    const titleColor = (theme === 'dark') ? '#9ca3af' : '#111827';

    const labelColor = (theme === 'dark') ? '#9ca3af' : '#111827';

    let backgroundColorArray = [];
    let colorScheme = [];
    labels.forEach(label => {
        backgroundColorArray = [
            'rgba(54, 162, 235, 1)', // blue
            'rgba(75, 192, 192, 1)', // green
            'rgba(255, 99, 132, 1)', // red
            'rgba(255, 159, 64, 1)', // orange
            'rgba(153, 102, 255, 1)', // purple
            'rgba(255, 206, 86, 1)', // yellow
            'rgba(255, 0, 0, 1)', // bright red
            'rgba(0, 255, 255, 1)', // cyan
            'rgba(255, 0, 255, 1)', // magenta
            'rgba(128, 128, 128, 1)' // grey
        ];
        //console.log('Assigning color ' + item + ' to chart ' + name);
        colorScheme = colorScheme.filter(element => element !== item);
    })

    //const ctx = canvas.getContext('2d');

    let chart = new Chart(canvas, {
        type: 'doughnut',
        plugins: [ChartDataLabels],
        data: {
            labels: labels,
            datasets: [
                {
                    label: name,
                    backgroundColor: backgroundColorArray,
                    data: data,
                    //color: 'red',
                    borderWidth: 0,
                    borderColor: 'rgba(255,255,255, 0.95)',
                    weight: 600,
                }
            ]
        },
        options: {
            hover: {
                mode: null
            },
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        padding: 20,
                        color: labelColor,
                        fontSize: 12,
                        borderWidth: 1,
                    }
                },
                // Chart Title on top
                title: {
                    display: true,
                    text: name.replace('_', ' '),
                    padding: {
                        top: 15,
                        bottom: 10
                    },
                    color: titleColor,
                    align: 'center',
                    fullSize: true,
                    font: {
                        weight: 'bold',
                        size: 16
                    },
                    position: 'top'
                },
                // When you hover on a datalabel, show count and stuff
                datalabels: {
                    display: true,
                    align: 'middle',
                    color: '#fff',
                    backgroundColor: '#000',
                    borderRadius: 3,
                    font: {
                        size: 11,
                        lineHeight: 1
                    },
                }
            },
        }
    });
    return chart;
}
class Stopwatch {
    constructor(timerId) {
        this.timer = document.getElementById(timerId);
        this.offset = 0;
        this.clock = 0;
        this.interval = null;
        this.timer.innerHTML = '0s';
    }

    start() {
        if (!this.interval) {
            this.offset = Date.now();
            this.interval = setInterval(() => this.update(), 0.1);
        }
    }

    stop() {
        if (this.interval) {
            clearInterval(this.interval);
            this.interval = null;
        }
    }

    reset() {
        this.clock = 0;
        this.render();
    }

    update() {
        this.clock += this.delta();
        this.render();
    }

    render() {
        this.timer.innerHTML = (this.clock / 1000).toFixed(3) + 's';
    }

    delta() {
        const now = Date.now();
        const d = now - this.offset;
        this.offset = now;
        return d;
    }
}

// Gauge
const createGauge = (parentDiv, title, width, height, currentValue, maxValue) => {

    const percentage = (currentValue / maxValue) * 100;

    const container = document.getElementById(parentDiv);

    const gaugeContainer = document.createElement('div');

    gaugeContainer.classList.add('flex', 'flex-col', 'items-center');

    const titleElement = document.createElement('div');
    titleElement.classList.add('gauge-title', 'font-semibold');
    titleElement.textContent = title;

    const gaugeElement = document.createElement('div');
    gaugeElement.classList.add('gauge');
    gaugeElement.style.width = `${width}px`;
    gaugeElement.style.height = `${height}px`;

    const gaugeFill = document.createElement('div');
    gaugeFill.classList.add('gauge-fill');

    const gaugeText = document.createElement('div');
    gaugeText.classList.add('gauge-text');
    gaugeText.textContent = `${currentValue} / ${maxValue}`;

    gaugeElement.appendChild(gaugeFill);
    gaugeElement.appendChild(gaugeText);

    container.appendChild(gaugeContainer);

    gaugeContainer.appendChild(titleElement);
    gaugeContainer.appendChild(gaugeElement);

    if (percentage >= 100) {
        gaugeFill.style.background = `conic-gradient(#FF0000 0% 100%, #FF0000 100% 100%)`;
    } else if (percentage >= 75) {
        gaugeFill.style.background = `conic-gradient(#FF0000 0% ${percentage}%, #FF0000 ${percentage}% 100%)`;
    } else if (percentage >= 50) {
        gaugeFill.style.background = `conic-gradient(#FFA500 0% ${percentage}%, #FFA500 ${percentage}% 100%)`;
    } else if (percentage >= 25) {
        gaugeFill.style.background = `conic-gradient(#FFD700 0% ${percentage}%, #FFD700 ${percentage}% 100%)`;
    } else {
        gaugeFill.style.background = `conic-gradient(#4CAF50 0% ${percentage}%, #4CAF50 ${percentage}% 100%)`;
    }
}

const gauge = (title, parentNodeId, width, height, labels, deita) => {
    let parentDiv = document.getElementById(parentNodeId);
    let containerDiv = document.createElement('div');
    parentDiv.appendChild(containerDiv);
    containerDiv.classList.add('m-4');
    let canvas = document.createElement('canvas');
    containerDiv.appendChild(canvas);
    canvas.id = `canvas-${generateUniqueId(4)}`;
    canvas.height = height;
    canvas.width = width;

    // Find out the theme - dark or light
    let theme = '';
    if (localStorage.getItem('color-theme')) {
        if (localStorage.getItem('color-theme') === 'dark') {
            theme = 'dark';
        } else {
            theme = 'light';
        }
    } else {
        theme = 'light';
    }

    const titleColor = (theme === 'dark') ? '#9ca3af' : '#111827';

    const labelColor = (theme === 'dark') ? '#9ca3af' : '#111827';

    // data for the gauge chart
    // you can supply your own values here
    // max is the Gauge's maximum value
    var data = {
        value: deita[0],
        max: deita[1],
        label: "used"
    };

    let backgroundColor = ''

    if (deita[0] === deita[1]) {
        backgroundColor = 'red'
    } else {
        backgroundColor = ['green', 'gray'];
    }

    // Chart.js chart's configuration
    // We are using a Doughnut type chart to 
    // get a Gauge format chart 
    // This is approach is fine and actually flexible
    // to get beautiful Gauge charts out of it
    const config = {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: [data.value, data.max - data.value],
                backgroundColor: backgroundColor,
                borderWidth: 0
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: true,
            cutoutPercentage: 85,
            rotation: -90,
            circumference: 180,
            tooltips: {
                enabled: false
            },
            legend: {
                display: false
            },
            animation: {
                animateRotate: true,
                animateScale: false
            },
            title: {
                display: true,
                text: title,
                fontSize: 16
            },
            plugins: {
                title: {
                    display: true,
                    text: title,
                    padding: {
                        top: 15,
                        bottom: 10
                    },
                    color: titleColor,
                    align: 'center',
                    fullSize: true,
                    font: {
                        weight: 'bold',
                        size: 16
                    },
                    position: 'top'
                },
                doughnutlabel: {
                    labels: [
                        {
                            text: title,
                            font: {
                                size: 30,
                                family: 'Arial, Helvetica, sans-serif',
                                weight: 'bold'
                            },
                            backgroundColor: 'green',
                            color: '#777'
                        }
                    ]
                },
            }
        }
    };

    // Create the chart
    let gaugeChart = new Chart(canvas, config);
}


/* Autload stuff */

const autoLoadParams = document.querySelectorAll('input[type="hidden"][name="autoload"]');

if (autoLoadParams.length > 0) {
    autoLoadParams.forEach(input => {
        const value = JSON.parse(input.value);
        const type = value.type;
        const data = value.data;
        if (type === 'doughnut') {
            gauge(data.title, data.parentDiv, data.width, data.height, data.labels, data.data);
        }
    })
}

// Handle the submitting of the IP address form if there is a get request with query paramter "ip". If it does, find a form with .generic-form class and simulate a click on the form submitter
const urlParams = new URLSearchParams(window.location.search);
const ip = urlParams.get('ip');
if (ip) {
    const form = document.querySelector('form.generic-form');
    // Also update the value of input with name "ip" to the ip address
    const ipInput = document.querySelector('input[name="ipAddress"]');
    ipInput.value = ip;

    // Find the submit button by tag name (button) or by class name (e.g., "submit-button-class")
    const submitButton = form.querySelector('button'); // By tag name
    // Or, if the button has a class name:
    // const submitButton = form.querySelector('.submit-button-class');
    if (submitButton) {
        submitButton.click();
    }
}

// Universal fetch function
const fetchData = async (url, method, jsonData) => {
    console.log(jsonData);
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(jsonData),
            redirect: 'manual'
        });

        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return await response.json(); // If the response is JSON, parse and return it
        } else {
            return await response.text(); // If not JSON, return the response as text
        }
    } catch (error) {
        throw new Error(`Fetch error: ${error.message}`);
    }
}

const createLoader = (parentDiv, id, text) => {
    const loaderDiv = document.createElement('div');
    loaderDiv.id = id;
    loaderDiv.classList.add('text-left', 'hidden', 'mt-6');
    const loader = document.createElement('div');
    loader.role = 'status';
    loaderDiv.appendChild(loader);
    const svg = document.createElement('svg');
    svg.setAttribute('aria-hidden', 'true');
    svg.classList.add('inline', 'mr-2', 'w-8', 'h-8', 'text-gray-200', 'dark:text-white', 'animate-spin', 'fill-blue-500', 'dark:fill-amber-500');
    svg.setAttribute('viewBox', '0 0 100 101');
    svg.setAttribute('fill', 'none');
    svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
    const path = document.createElement('path');
    path.setAttribute('d', 'M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"');
    svg.appendChild(path);
    loader.appendChild(svg);
    const span = document.createElement('span');
    span.textContent = text;
    loader.appendChild(span);
    parentDiv.appendChild(loaderDiv);
}

const editModal = (id) => {
    let html = `
        <!-- Main modal -->
        <div id="${id}-container" class="mx-2 relative w-full bg-gray-50 dark:bg-gray-700 max-w-2xl max-h-full mx-auto border border-gray-700 dark:border-gray-400 shadow">
            <!-- Modal content -->
            <div class="relative overflow-auto max-h-[44rem] rounded-lg dark:bg-gray-700 ">
                <!-- Modal header -->
                <div class="mx-4 flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Edit
                    </h3>
                    <button id="${id}-x-button" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="${id}">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div id="${id}-body" class="p-4 md:p-5 space-y-4 break-words">
                    <p class="text-base leading-relaxed text-gray-700 dark:text-gray-400">
                        Loading data...
                    </p>
                </div>
            </div>
            <!-- Modal Result -->
            <div id="${id}-result" class="m-4"></div>
            <!-- Modal footer -->
            <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button id="${id}-edit" data-modal-hide="${id}" type="button" class="text-white bg-${theme}-700 hover:bg-${theme}-800 focus:ring-4 focus:outline-none focus:ring-${theme}-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-${theme}-600 dark:hover:bg-${theme}-700 dark:focus:ring-${theme}-800">Edit</button>
                <button id="${id}-close-button" data-modal-hide="${id}" type="button" class="ms-3 text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-${theme}-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Cancel</button>
            </div>
        </div>
    `;
    const modal = document.createElement('div');
    modal.id = id;
    // Add data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
    modal.classList.add('hidden', 'overflow-auto', 'fixed', 'top-0', 'right-0', 'left-0', 'z-50', 'justify-center', 'items-center', 'w-full', 'md:inset-0', 'h-[calc(100%-1rem)]', 'max-h-full', 'mt-8');
    modal.setAttribute('data-modal-backdrop', 'static');
    modal.setAttribute('tabindex', '-1');
    modal.setAttribute('aria-hidden', 'true');
    // Add the html to the modal
    modal.innerHTML = html;
    return modal;
}

const deleteModal = (id, data) => {
    const jsonData = JSON.stringify(data);
    let html = `
    <!-- Main modal -->
        <div id="${id}-container" class="relative w-full max-w-2xl max-h-full mx-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 border border-gray-700 dark:border-gray-400">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Delete 
                    </h3>
                    <button id="${id}-x-button" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="${id}">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div id="${id}-body" class="p-4 md:p-5 space-y-4 break-words">
                    <p class="text-base leading-relaxed text-gray-700 dark:text-gray-400">
                        Are you sure you want to delete entry: ${jsonData}?
                    </p>
                </div>
                <!-- Modal Result -->
                <div id="${id}-result" class="m-4"></div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button id="${id}-delete" data-modal-hide="${id}" type="button" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Delete</button>
                    <button id="${id}-close-button" data-modal-hide="${id}" type="button" class="ms-3 text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-red-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Cancel</button>
                </div>
            </div>
        </div>
    `;
    const modal = document.createElement('div');
    modal.id = id;
    // Add data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
    modal.classList.add('hidden', 'overflow-y-hidden', 'overflow-x-hidden', 'fixed', 'top-0', 'right-0', 'left-0', 'z-50', 'justify-center', 'items-center', 'w-full', 'md:inset-0', 'h-[calc(100%-1rem)]', 'max-h-full', 'mt-12');
    modal.setAttribute('data-modal-backdrop', 'static');
    modal.setAttribute('tabindex', '-1');
    modal.setAttribute('aria-hidden', 'true');
    // Add the html to the modal
    modal.innerHTML = html;
    return modal;
}

const editSingleButton = document.querySelectorAll('button.edit-single-button');

if (editSingleButton.length > 0) {
    console.log(`found ${editSingleButton.length} edit single buttons`);
    editSingleButton.forEach(button => {
        button.addEventListener('click', () => {
            const uniqueId = generateUniqueId(4);
            // add data-modal-target="static-modal" data-modal-toggle="static-modal" to the button
            button.setAttribute('data-modal-target', uniqueId);
            button.setAttribute('data-modal-toggle', uniqueId);
            // Generate the modal
            let modal = editModal(uniqueId);
            // Insert the modal at the bottom of the first div after the body
            document.body.insertBefore(modal, document.body.firstChild);
            // Now show the modal
            modal.classList.remove('hidden');
            // Now let's disable scrolling on the rest of the page by adding overflow-hidden to the body
            document.body.classList.add('overflow-hidden');
            // Blur the body excluding the modal
            toggleBlur(modal);
            // Let's make some bindings
            const modalResult = document.getElementById(`${uniqueId}-result`);
            // First, the close button
            const closeXButton = document.getElementById(`${uniqueId}-x-button`);
            // The cancel button
            const cancelButton = document.getElementById(`${uniqueId}-close-button`);
            const cancelButtonsArray = [closeXButton, cancelButton];
            cancelButtonsArray.forEach(cancelButton => {
                cancelButton.addEventListener('click', () => {
                    // Completely remove the modal
                    modal.remove();
                    // Return the overflow of the body
                    document.body.classList.remove('overflow-hidden');
                    // Remove the blur by toggling the blur class
                    toggleBlur(modal);
                })
            })
            let modalBody = document.getElementById(`${uniqueId}-body`);
            let responseStatus = 0;
            // Now let's fetch data from the API and populate the modalBody. We will read the data-api-action from the button
            const formData = new FormData();
            const apiAction = button.getAttribute('data-api-action');
            const csrfToken = button.getAttribute('data-csrf');
            const apiData = JSON.parse(button.getAttribute('data-api-data'));
            Object.entries(apiData).forEach(([key, value]) => {
                formData.append(key, value);
            });
            // Now let's send a fetch request to /api/process with form-data api-action and the apiAction value
            formData.append('api-action', 'get-' + apiAction);
            formData.append('csrf_token', csrfToken);
            fetch('/api/process', {
                method: 'POST',
                body: formData,
                redirect: 'manual'
            }).then(response => {
                responseStatus = response.status;
                if (responseStatus === 403 || responseStatus === 401 || responseStatus === 0) {
                    modalBody.innerHTML = `<p class="text-red-500 font-semibold">Response not ok, refreshing</p>`;
                    location.reload();
                } else {
                    return response.json()
                }
            }).then(json => {
                if (responseStatus === 404 || responseStatus === 400) {
                    editButton.innerText = 'Retry';
                    let errorMessage = '';
                    if (json.data) {
                        errorMessage = json.data;
                    } else if (json.error) {
                        errorMessage = json.error;
                    }
                    modalBody.innerHTML = `<p class="text-red-500 font-semibold">${errorMessage}</p>`;
                } else {
                    // Assuming json.data exists, you can access its contents here
                    const dataContents = json.data;
                    // Now let's populate the modal body
                    const newForm = buildEditForm(dataContents, `${uniqueId}-form`);
                    newForm.action = `/api/process`;
                    // Add api-action and csrf_token to the form
                    const apiActionInput = document.createElement('input');
                    apiActionInput.type = 'hidden';
                    apiActionInput.name = 'api-action';
                    apiActionInput.value = `update-${apiAction}`;
                    newForm.appendChild(apiActionInput);
                    const csrfTokenInput = document.createElement('input');
                    csrfTokenInput.type = 'hidden';
                    csrfTokenInput.name = 'csrf_token';
                    csrfTokenInput.value = csrfToken;
                    newForm.appendChild(csrfTokenInput);
                    modalBody.innerHTML = '';
                    modalBody.appendChild(newForm);
                }
                // Now you can work with the dataContents as needed
            }).catch(error => {
                console.error('Error during fetch:', error);
            });
            // Now the edit button
            const editButton = document.getElementById(`${uniqueId}-edit`);
            const initialButtonText = editButton.textContent;
            editButton.addEventListener('click', () => {
                // transform the button text to a loader
                editButton.innerHTML = `<div role="status" class="flex items-center justify-center">
                    <span class="sr-only">Loading...</span>
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0
                                014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>`;
                const editForm = document.getElementById(`${uniqueId}-form`);
                // We cannot directly submit() newForm, we will need to use another fetch with the form data
                const formData = new FormData(editForm);
                // Now let's send a fetch request to /api/process with form-data api-action and the apiAction value
                fetch('/api/process', {
                    method: 'POST',
                    body: formData,
                    redirect: 'manual'
                }).then(response => {
                    responseStatus = response.status;
                    if (responseStatus === 403 || responseStatus === 401 || responseStatus === 0) {
                        modalBody.innerHTML = `<p class="text-red-500 font-semibold">Response not ok, refreshing</p>`;
                        location.reload();
                    } else {
                        return response.json()
                    }
                }).then(json => {
                    // So if the response is status is 404 on the update, it mans nothing got updated, so we display it.
                    if (responseStatus === 404 || responseStatus === 400) {
                        editButton.innerText = 'Retry';
                        let errorMessage = '';
                        if (json.data) {
                            errorMessage = json.data;
                        } else if (json.error) {
                            errorMessage = json.error;
                        }
                        modalResult.innerHTML = `<p class="text-red-500 font-semibold">${errorMessage}</p>`;
                    } else {
                        editButton.innerText = initialButtonText;
                        modalResult.innerHTML = `<p class="text-green-500 font-semibold">${json.data}</p>`;
                        location.reload();
                    }
                }).catch(error => {
                    console.error('Error during fetch:', error);
                });
            })
        })
    })
}

const buildEditForm = (data, id) => {
    const form = document.createElement('form');
    form.id = id;
    // Create a holder div for all the inputs and labels with flex flex-col flex-wrap
    const holder = document.createElement('div');
    holder.classList.add('flex', 'flex-col', 'flex-wrap');
    form.appendChild(holder);
    // So the data is in format key = > value, we need to build a form with input fields with names (keys) and values (values)
    Object.entries(data).forEach(([key, value]) => {
        const input = document.createElement('input');
        const readOnlyFields = ['id', 'created_at', 'date_created', 'created_by', 'organization'];
        if (readOnlyFields.includes(key)) {
            input.readOnly = true;
        }
        const sensitiveFields = ['client_secret'];
        // if value is int or float, make it number, otherwise text
        if (typeof value === 'number') {
            input.type = 'number';
        } else {
            input.type = 'text';
        }
        if (sensitiveFields.includes(key)) {
            input.type = 'password';
        }
        if (input.type === 'text') {
            if (input.readOnly) {
                input.classList.add('text-sm', `bg-gray-200`, 'appearance-none', 'border', `border-red-500`, 'rounded-lg', 'py-2', 'px-4', 'text-gray-700', 'leading-tight', 'focus:outline-none', `focus:bg-gray-100`, `focus:border-red-700`, 'dark:bg-gray-700', 'dark:border-red-600', 'dark:placeholder-gray-400', 'dark:text-white', `dark:focus:ring-red-600`, `dark:focus:border-red-600`, `cursor-not-allowed`);
            } else {
                input.classList.add('text-sm', `bg-gray-100`, 'appearance-none', 'border-2', `border-${theme}-100`, 'rounded-lg', 'py-2', 'px-4', 'text-gray-700', 'leading-tight', 'focus:outline-none', `focus:bg-gray-100`, `focus:border-${theme}-500`, 'dark:bg-gray-700', 'dark:border-gray-600', 'dark:placeholder-gray-400', 'dark:text-white', `dark:focus:ring-${theme}-500`, `dark:focus:border-${theme}-500`);
            }
            // For now this is number
        } else {
            if (input.readOnly) {
                input.classList.add('text-sm', `bg-gray-200`, 'appearance-none', 'border', `border-red-500`, 'rounded-lg', 'py-2', 'px-4', 'text-gray-700', 'leading-tight', 'focus:outline-none', `focus:bg-gray-100`, `focus:border-red-700`, 'dark:bg-gray-700', 'dark:border-red-600', 'dark:placeholder-gray-400', 'dark:text-white', `dark:focus:ring-red-600`, `dark:focus:border-red-600`, `cursor-not-allowed`);
            } else {
                input.classList.add('ml-2', 'p-1', 'text-sm', 'text-gray-900', 'border', `border-${theme}-300`, 'rounded', `bg-gray-200`, `focus:ring-${theme}-500`, `focus:border-${theme}-500`, 'dark:bg-gray-700', 'dark:border-gray-600', 'dark:placeholder-gray-400', 'dark:text-white', `dark:focus:ring-${theme}-500`, `dark:focus:border-${theme}-500`);
            }
        }
        const label = document.createElement('label');
        // let's put the key into a span with class font-semibold, mr-2
        const span = document.createElement('span');
        span.classList.add('font-semibold', 'mr-2');
        span.textContent = key;
        label.classList.add('my-2');
        label.appendChild(span);
        label.appendChild(input);
        form.appendChild(label);
        input.name = key;
        input.value = value;
        // append to the holder
        holder.appendChild(label);
    })
    return form;
}

const toggleBlur = (excludeElement) => {
    const addBlurClass = (element) => {
        // Apply the hardcoded class 'blur-sm' to the element
        element.classList.toggle('blur-sm');

        // Recursively process child elements
        for (const child of element.children) {
            addBlurClass(child);
        }
    };

    // Get all elements in the body
    const allElements = document.body.getElementsByTagName('*');

    // Iterate through the elements and apply the class conditionally
    for (const currentElement of allElements) {
        // Check if the element is not the one to exclude or its descendant
        if (currentElement !== excludeElement && !excludeElement.contains(currentElement)) {
            addBlurClass(currentElement);
        }
    }
};

const deleteSignleButton = document.querySelectorAll('button.delete-single-button');

if (deleteSignleButton.length > 0) {
    deleteSignleButton.forEach(button => {
        button.addEventListener('click', () => {
            // Get the data attributes from the delete button
            const apiAction = button.getAttribute('data-api-action');
            const csrfToken = button.getAttribute('data-csrf');
            const apiData = JSON.parse(button.getAttribute('data-api-data'));
            const formData = new FormData();
            Object.entries(apiData).forEach(([key, value]) => {
                formData.append(key, value);
            });
            // Now let's send a fetch request to /api/process with form-data api-action and the apiAction value
            formData.append('api-action', apiAction);
            formData.append('csrf_token', csrfToken);
            const uniqueId = generateUniqueId(4);
            // Create the modal
            let modal = deleteModal(uniqueId, apiData);
            // Insert the modal at the bottom of the first div after the body
            document.body.insertBefore(modal, document.body.firstChild);
            // Now show the modal
            modal.classList.remove('hidden');
            // Now let's disable scrolling on the rest of the page by adding overflow-hidden to the body
            document.body.classList.add('overflow-hidden');
            // Blur the body excluding the modal
            toggleBlur(modal);
            // First, the close button
            const closeXButton = document.getElementById(`${uniqueId}-x-button`);
            // The cancel button
            const cancelButton = document.getElementById(`${uniqueId}-close-button`);
            const cancelButtonsArray = [closeXButton, cancelButton];
            cancelButtonsArray.forEach(cancelButton => {
                cancelButton.addEventListener('click', () => {
                    // Completely remove the modal
                    modal.remove();
                    // Return the overflow of the body
                    document.body.classList.remove('overflow-hidden');
                    // Remove the blur by toggling the blur class
                    toggleBlur(modal);
                })
            })
            const deleteModalButton = document.getElementById(`${uniqueId}-delete`);
            let modalBody = document.getElementById(`${uniqueId}-body`);
            let responseStatus = 0;
            deleteModalButton.addEventListener('click', () => {
                // transform the button text to a loader
                deleteModalButton.innerHTML = `
                <div role="status" class="flex items-center justify-center">
                    <span class="sr-only">Loading...</span>
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0
                                014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
                `;
                fetch('/api/process', {
                    method: 'POST',
                    body: formData,
                    redirect: 'manual'
                }).then(response => {
                    responseStatus = response.status;
                    if (responseStatus === 403 || responseStatus === 401 || responseStatus === 0) {
                        modalBody.innerHTML = `<p class="text-red-500 font-semibold">Response not ok, refreshing</p>`;
                        location.reload();
                    } else {
                        return response.json()
                    }
                }).then(json => {
                    // Return the overflow of the body
                    document.body.classList.remove('overflow-hidden');
                    // Remove the blur by toggling the blur class
                    toggleBlur(modal);
                    if (responseStatus === 200) {
                        // if we are deleting from a table, Delete the closest row
                        const closestRow = button.closest('tr');
                        if (closestRow) {
                            closestRow.remove();
                        }
                        // Completely remove the modal
                        modal.remove();
                    } else {
                        deleteModalButton.textContent = 'Retry';
                        modalBody.innerHTML = `<p class="text-red-500 font-semibold">${json.error}</p>`;
                    }
                }).catch(error => {
                    console.error('Error during fetch:', error);
                })
            })
        })
    })
}