<?php

use Components\DataGrid\DataGrid;
use Components\DataGrid\DataGridDBTable;

echo DataGridDBTable::renderTable('Dummy', 'dummy', $theme, false, false);
