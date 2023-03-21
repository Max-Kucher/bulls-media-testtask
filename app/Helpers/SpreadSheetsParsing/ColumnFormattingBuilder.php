<?php

namespace App\Helpers\SpreadSheetsParsing;

class ColumnFormattingBuilder
{
    private array $column_data;

    public function __construct(array $column_data)
    {
        $this->column_data = $column_data;
    }

    /**
     * @return callable|\Closure
     */
    public function getFormatter()
    {
        /**
         * Work with formatting (i.e. dates and so on)
         */
        if (isset($field_data['date_format'])) {
            $format = $field_data['date_format'];
            $formatter = fn ($value) => \DateTime::createFromFormat($format, $value)->getTimestamp();

        } elseif (isset($field_data['format_function']) && is_callable($field_data['format_function'])) {
            $formatter = $field_data['format_function'];

        } else {
            /**
             * @param $value
             * @return mixed
             */
            $formatter = fn($value) => $value;
        }

        return $formatter;
    }
}
