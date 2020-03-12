<?php 
Route::get('cms/login', 'Devdojo\Calculator\CmsUsersController@show')->name('login')->middleware('web');
Route::post('cms/login', 'Devdojo\Calculator\CmsUsersController@authenticate')->middleware('web');
Route::get('cms/register', 'Devdojo\Calculator\CmsUsersController@register');


Route::group(['middleware' => ['web', 'auth:cms_user']], function () {
    // web routes
  Route::get('cms/dashboard', 'Devdojo\Calculator\CalculatorController@index');
  Route::get('cms/table/{table}', 'Devdojo\Calculator\CalculatorController@view');

  Route::get('cms/configure', 'Devdojo\Calculator\CalculatorController@configureCmsFields');
  Route::get('cms/configureTables', 'Devdojo\Calculator\CalculatorController@configureTables');

  Route::get('cms/createEntry/{table}', 'Devdojo\Calculator\CalculatorController@create');
  Route::get('cms/edit/{table}/{entry}', 'Devdojo\Calculator\CalculatorController@create');
  Route::post('cms/create/{table}', 'Devdojo\Calculator\CalculatorController@store');
  Route::post('cms/update/{table}', 'Devdojo\Calculator\CalculatorController@update');
  Route::get('cms/delete/{table}/{entryId}', 'Devdojo\Calculator\CalculatorController@destroy');
Route::get('cms/logout', 'Devdojo\Calculator\CmsUsersController@logout');

});
?>