<?php

namespace App\Http\Controllers;

use App\Helpers\SpreadSheetsParsing\CsvIterator;
use App\Helpers\SpreadSheetsParsing\TableChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ParseGoogleSheets extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        /**
         * Get mapping schema
         */
        $map_schema = config('google_sheets_parsing');

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

                try {
                    $csv_iterator = new CsvIterator($sheet_url . '/export?format=csv&id=' . $_sheet_id . '&gid=' . $gid);
                } catch (\RuntimeException $e) {
                    report($e);
                    continue;
                }


            }
        }

        echo 111;
        die;
    }
}
