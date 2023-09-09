Route::group('{if '{$app[1].folder}'}:version/{/if}{$lcfirst(controller.class_name)}', function(){
    Route::get('list','{if '{$app[1].folder}'}:version.{/if}{$controller.class_name}/getPageList');
    Route::get('detail','{if '{$app[1].folder}'}:version.{/if}{$controller.class_name}/detail');
    Route::post('add{$tables[1].model_name}','{if '{$app[1].folder}'}:version.{/if}{$controller.class_name}/add{$tables[1].model_name}');
    Route::get('get{$tables[1].model_name}By{$form.relation_field}','{if '{$app[1].folder}'}:version.{/if}{$controller.class_name}/get{$tables[1].model_name}By{$form.relation_field}');
    Route::post('add','{if '{$app[1].folder}'}:version.{/if}{$controller.class_name}/add');
    Route::put('edit','{if '{$app[1].folder}'}:version.{/if}{$controller.class_name}/edit');
    Route::delete('delete','{if '{$app[1].folder}'}:version.{/if}{$controller.class_name}/delete');
})->allowCrossDomain();