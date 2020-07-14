<?php

Route::get('phonebookdatatable', 'Axilweb\Phonebook\PhonebookController@phonebookdatatable');
Route::get('get-phonebook-data-datatable', 'Axilweb\Phonebook\PhonebookController@getPhonebookDataDatatable');
Route::get('get-chart-data', 'Axilweb\Phonebook\PhonebookController@getChartData');

Route::get('phonebook', 'Axilweb\Phonebook\PhonebookController@phonebook');
Route::get('get-phonebook-data', 'Axilweb\Phonebook\PhonebookController@getPhonebookData');