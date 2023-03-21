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
        if (isset($this->column_data['date_format'])) {
            $format = $this->column_data['date_format'];
            $formatter = function ($value) use ($format) {
                $date_time = \DateTime::createFromFormat($format, $value);

                return $date_time ? $date_time->getTimestamp() : 0;
            };

        } elseif (isset($this->column_data['format_function']) && is_callable($this->column_data['format_function'])) {
            $formatter = $this->column_data['format_function'];

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
