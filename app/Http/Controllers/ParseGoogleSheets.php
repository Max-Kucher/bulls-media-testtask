<?php

namespace App\Http\Controllers;

use App\Helpers\SpreadSheetsParsing\CsvIterator;
use App\Helpers\SpreadSheetsParsing\ModelBuilder;
use App\Helpers\SpreadSheetsParsing\TableChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ParseGoogleSheets extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        /**
         * Get mapping schema
         */
        $map_schema = config('google_sheets_parsing');

        $results = [];

        foreach ($map_schema as $sheet_url => $sheet_data) {
            /**
             * Check if spreadsheet is reachable
             * -> continue if not
             */
            $headers = get_headers($sheet_url . '/edit');
            if ($headers === false || !str_contains($headers[0], '200 OK')) {
                continue;
            }

            foreach ($sheet_data as $gid => $sheet_list_data) {
                /**
                 * @var TableChecker $table_checker
                 */
                $table_checker = App::make(TableChecker::class, [
                    'table' => $sheet_list_data['db_table'],
                    'fields_schema' => $sheet_list_data['fields'],
                ]);

                /**
                 * Create table and table columns
                 */
                if (!$table_checker->buildTableStructure()) {
                    continue;
                }

                $parsed_url = parse_url($sheet_url);
                $parsed_url = explode('/', $parsed_url['path']);

                $_sheet_id = $parsed_url[array_key_last($parsed_url)];

                $export_url = $sheet_url . '/export?format=csv&id=' . $_sheet_id . '&gid=' . $gid;

                try {
                    $csv_iterator = new CsvIterator($export_url);
                } catch (\RuntimeException $e) {
                    report($e);
                    continue;
                }

                $csv_iterator->useFirstRowAsHeader();

                $first_row = true;
                foreach ($csv_iterator as $row) {
                    if ($first_row) {
                        $first_row = false;
                        continue;
                    }

                    $model = new ModelBuilder($sheet_list_data['db_table'], $table_checker->getMappedFields());
                    $model = $model->buildModelObject();

                    foreach ($row as $column_name => $column_value) {
                        $mapped_column_data = $table_checker->getMappedFields()[$column_name];
                        $column_value = call_user_func($mapped_column_data['format_function'], $column_value);

                        $model->{$mapped_column_data['table_column']} = $column_value;
                    }

                    if ($model->save()) {
                        if (!isset($results[$sheet_list_data['db_table']]['updated'])) {
                            $results[$sheet_list_data['db_table']]['updated'] = 0;
                        }

                        $results[$sheet_list_data['db_table']]['updated']++;
                    }
                }
            }
        }

        return response()->json($results);
    }
}
