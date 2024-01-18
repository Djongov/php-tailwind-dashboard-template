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
                fetch('/api/datagrid/update-records', {
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
                    fetch('/api/datagrid/delete-records', {
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
                    fetch('/api/datagrid/get-records', {
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