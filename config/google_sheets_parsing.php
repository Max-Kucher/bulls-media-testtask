<?php

return [
    /**
     * Sheets url
     */
    'https://docs.google.com/spreadsheets/d/1nQofQXzr8rMZP4f1LC10LkteK0-Zs2xMYLTWVjf6qVE' => [
        /**
         * Sheet id
         * Please pay ATTENTION here
         */
        0 => [
            /**
             * Database table name
             */
            'db_table' => 'products',

            /**
             * Database columns mapping
             */
            'fields' => [
                /**
                 * Column name from sheet
                 */
                'date' => [
                    /**
                     * Database column name
                     */
                    'db_field' => 'date',

                    /**
                     * Database column type
                     */
                    'type' => 'integer',

                    /**
                     * If it is date -> set up the date time format for converting
                     */
                    'date_format' => 'd.m.Y',

                    /**
                     * Additional modifiers for column
                     */
                    'additional_modifiers' => [
                        'unsigned', 'nullable'
                    ],
                ],
                'product_name' => [
                    'db_field' => 'name',
                    'type' => 'string',
                    /**
                     * Additional params for column creation
                     */
                    'type_params' => [100],
                    'additional_modifiers' => [
                        'nullable'
                    ],
                ],
                'price' => [
                    'db_field' => 'price',
                    'type' => 'unsignedDecimal',
                    'type_params' => [8, 2],
                    'additional_modifiers' => [
                        'nullable'
                    ],
                ],
                'amount' => [
                    'db_field' => 'stocks',
                    'type' => 'integer',
                    'additional_modifiers' => [
                        'nullable'
                    ],
                ],
            ],
        ],

        1256509563 => [
            'db_table' => 'products',
            'fields' => [
                'date123' => [
                    'db_field' => 'date',
                    'type' => 'integer',
                    'date_format' => 'd.m.Y',

                    /**
                     * Additional modifiers for column
                     */
                    'additional_modifiers' => [
                        'unsigned', 'nullable'
                    ],
                ],
                'product_name123' => [
                    'db_field' => 'name',
                    'type' => 'string',
                    'type_params' => [100],
                    'additional_modifiers' => [
                        'nullable'
                    ],
                ],
                'price' => [
                    'db_field' => 'price',
                    'type' => 'unsignedDecimal',
                    'type_params' => [8, 2],
                    'additional_modifiers' => [
                        'nullable'
                    ],
                ],
                'amount' => [
                    'db_field' => 'stocks',
                    'type' => 'integer',
                    'additional_modifiers' => [
                        'nullable'
                    ],
                ],
            ],
        ],
    ],

    'https://docs.google.com/spreadsheets/d/1-TMK2G_wpzvczCqFDKVYm97AqvuCa9DLUdgpjur2QBg' => [
        0 => [
            'db_table' => 'sales',
            'fields' => [
                'sale_id' => [
                    'db_field' => 'sale_id',
                    'type' => 'increments',
                ],
                'amount' => [
                    'db_field' => 'sale_amount',
                    'type' => 'unsignedDecimal',
                    'type_params' => [8, 2],
                    'additional_modifiers' => [
                        'nullable'
                    ],
                ],
            ],
        ],
    ],
];
