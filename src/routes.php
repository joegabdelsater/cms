<?php 
Route::get('cms/login', 'Xtnd\Cms\CmsUsersController@show')->name('login')->middleware('web');
Route::post('cms/login', 'Xtnd\Cms\CmsUsersController@authenticate')->middleware('web');
Route::get('cms/register', 'Xtnd\Cms\CmsUsersController@register');


Route::group(['middleware' => ['web', 'auth:cms_user']], function () {
    // web routes
  Route::get('cms/dashboard', 'Xtnd\Cms\CmsController@index');
  Route::get('cms/table/{table}', 'Xtnd\Cms\CmsController@view');

  Route::get('cms/configure', 'Xtnd\Cms\CmsController@configureCmsFields');
  Route::get('cms/configureTables', 'Xtnd\Cms\CmsController@configureTables');

  Route::get('cms/createEntry/{table}', 'Xtnd\Cms\CmsController@create');
  Route::get('cms/edit/{table}/{entry}', 'Xtnd\Cms\CmsController@create');
  Route::post('cms/create/{table}', 'Xtnd\Cms\CmsController@store');
  Route::post('cms/update/{table}', 'Xtnd\Cms\CmsController@update');
  Route::get('cms/delete/{table}/{entryId}', 'Xtnd\Cms\CmsController@destroy');
Route::get('cms/logout', 'Xtnd\Cms\CmsUsersController@logout');

});
?>