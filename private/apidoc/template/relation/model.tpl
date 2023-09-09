<?php
namespace {$tables[0].namespace};

class {$tables[0].model_name} extends BaseModel
{
    public function {$lcfirst(tables[1].model_name)}()
    {
            return $this->hasMany({$tables[1].model_name}::class,'{$form.relation_field}','id');
    }
}

?>