<?php

return [

    'components' => [
        'backup_destination_list' => [
            'table' => [
                'actions' => [
                    'download' => 'Pobierz',
                    'delete' => 'Usuń',
                ],

                'fields' => [
                    'path' => 'Ścieżka',
                    'disk' => 'Dysk',
                    'date' => 'Data',
                    'size' => 'Rozmiar',
                ],

                'filters' => [
                    'disk' => 'Dysk',
                ],
            ],
        ],

        'backup_destination_status_list' => [
            'table' => [
                'fields' => [
                    'name' => 'Nazwa',
                    'disk' => 'Dysk',
                    'healthy' => 'Zdrowy',
                    'amount' => 'Ilość',
                    'newest' => 'Najnowszy',
                    'used_storage' => 'Użyta pamięć',
                ],
            ],
        ],
    ],

    'pages' => [
        'backups' => [
            'actions' => [
                'create_backup' => 'Utwórz kopię zapasową',
            ],

            'heading' => 'Kopie zapasowe',

            'messages' => [
                'backup_success' => 'Tworzenie nowej kopii zapasowej w tle.',
                'backup_delete_success' => 'Usuwanie tej kopii zapasowej w tle.',
            ],

            'modal' => [
                'buttons' => [
                    'only_db' => 'Tylko baza danych',
                    'only_files' => 'Tylko pliki',
                    'db_and_files' => 'Baza danych i pliki',
                ],

                'label' => 'Wybierz opcję',
            ],

            'navigation' => [
                'group' => 'Settings',
                'label' => 'Kopie zapasowe',
            ],
        ],
    ],

];
