<?php

return [
    'mapFilter' => [
        'file' => 'mapfilter',
        'description' => 'mapFilter snippet',
        'properties' => [
            'tpl' => [
                'type' => 'textfield',
                'value' => 'tpl.mapFilter.row',
            ],
            'tplOuter'=>[
                'type' => 'textfield',
                'value' => 'tpl.mapFilter.Outer',
            ],
            'tplFilter.outer.default'=>[
                'type' => 'textfield',
                'value' => 'tpl.mapFilter.filter.outer',
            ],
            'tplFilter.row.default'=>[
                'type' => 'textfield',
                'value' => 'tpl.mapFilter.filter.checkbox',
            ],
            'js' => [
                'type' => 'textfield',
                'value' => 'js/default.js',
            ],
            'css' => [
                'type' => 'textfield',
                'value' => 'css/default.css',
            ],
            'sortby' => [
                'type' => 'textfield',
                'value' => 'name',
            ],
            'sortdir' => [
                'type' => 'list',
                'options' => [
                    ['text' => 'ASC', 'value' => 'ASC'],
                    ['text' => 'DESC', 'value' => 'DESC'],
                ],
                'value' => 'ASC',
            ],
            'limit' => [
                'type' => 'numberfield',
                'value' => 10,
            ],
            'outputSeparator' => [
                'type' => 'textfield',
                'value' => "\n",
            ],
            'toPlaceholder' => [
                'type' => 'combo-boolean',
                'value' => false,
            ],
        ],
    ],
];