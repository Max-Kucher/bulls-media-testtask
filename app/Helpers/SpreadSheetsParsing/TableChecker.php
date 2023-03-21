<?php

namespace App\Helpers\SpreadSheetsParsing;

use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TableChecker
{
    /**
     * @var string $table
     */
    private string $table;

    /**
     * @var array $fields_schema
     */
    private array $fields_schema;

    /**
     * @var array $mapped_fields
     */
    private array $mapped_fields;

    /**
     * @param string $table
     * @param array $fields_schema
     */
    public function __construct(string $table, array $fields_schema)
    {
        $this->table = $table;
        $this->fields_schema = $fields_schema;
    }

    /**
     * @return bool
     */
    public function buildTableStructure()
    {
        if (Schema::hasTable($this->table)) {
            $table_method = 'table';
        } else {
            $table_method = 'create';
        }

        try {
            Schema::$table_method($this->table, function (Blueprint $table) use ($table_method) {
                $primary_key_exist = false;

                foreach ($this->fields_schema as $csv_column_name => $field_data) {
                    /**
                     * Check if primary key exists in mapped fields
                     */
                    if (in_array($field_data['type'], ['bigIncrements' , 'increments', 'mediumIncrements', 'smallIncrements', 'tinyIncrements'])
                        || $field_data['db_field'] === 'id'
                        || (!empty($field_data['additional_modifiers'] && in_array('autoIncrement', $field_data['additional_modifiers'])))
                    ) {
                        $primary_key_exist = true;
                        $this->mapped_fields[$csv_column_name]['is_key'] = true;
                    }

                    /**
                     * If column does not exist -> create column in database
                     */
                    if (!Schema::hasColumn($this->table, $field_data['db_field'])) {
                        $reflection = new \ReflectionMethod($table, $field_data['type']);

                        if ($reflection->getNumberOfParameters()) {
                            $column = $table->{$field_data['type']}($field_data['db_field'], ...($field_data['type_params'] ?? []));
                        } else {
                            $column = $table->{$field_data['type']}();
                        }

                        /**
                         * Column blueprint was created -> apply additional modifiers
                         */
                        if (!empty($field_data['additional_modifiers']) && is_a($column, ColumnDefinition::class)) {
                            foreach ($field_data['additional_modifiers'] as $modifier) {
                                if (is_string($modifier) && is_callable([$column, $modifier])) {
                                    $column->$modifier();
                                } elseif (is_array($modifier)) {
                                    $method = array_splice($modifier, 0, 1);

                                    if (is_callable([$column, $method])) {
                                        $column->$method(...$modifier);
                                    }
                                }
                            }
                        }
                    }

                    $this->mapped_fields[$csv_column_name]['table_column'] = $field_data['db_field'];

                    $formatting_builder = new ColumnFormattingBuilder($field_data);
                    $this->mapped_fields[$csv_column_name]['format_function'] = $formatting_builder->getFormatter();
                }

                /**
                 * If no primary key in mapped fields
                 */
                if ($primary_key_exist === false) {
                    if ($table_method === 'table') {
                        $sm = Schema::getConnection()->getDoctrineSchemaManager();
                        $indexes = $sm->listTableIndexes($this->table);

                        if (isset($indexes['primary'])) {
                            $primary_key_exist = true;
                            $index_column = current($indexes['primary']->getColumns());
                        }
                    }

                    if ($primary_key_exist === false) {
                        $id_column = $table->id();

                        if ($table_method === 'table') {
                            $id_column->first();
                        }
                    }

                    $this->mapped_fields[$index_column ?? 'id']['is_key'] = true;
                }
            });
        } catch (\Throwable $e) {
            report($e);
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getMappedFields(): array
    {
        return $this->mapped_fields;
    }
}
