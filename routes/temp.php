<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

Route::get('/temp/check-db', function() {
    $columns = Schema::getColumnListing('inventory_items');
    $tableInfo = DB::select('DESCRIBE inventory_items');
    
    return [
        'columns' => $columns,
        'table_info' => $tableInfo
    ];
});
