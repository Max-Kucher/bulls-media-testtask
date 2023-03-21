<?php

namespace App\Helpers\SpreadSheetsParsing;

use Illuminate\Database\Eloquent\Model;

/**
 * Class to create model on the fly
 */
class ModelBuilder
{
    private string $entity_table;

    private array $fields_data;

    public function __construct(string $table, array $fields_data)
    {
        $this->entity_table = $table;
        $this->fields_data = $fields_data;
    }

    /**
     * @return Model
     */
    public function buildModelObject()
    {
        $object = new class extends Model {};
        $object->setTable($this->entity_table);
        $object->timestamps = false;

        foreach ($this->fields_data as $field_name => $field) {
            if (isset($field['is_key']) && $field['is_key'] === true) {
                $object->setKeyName($field_name);
            }
        }

        return $object;
    }
}
