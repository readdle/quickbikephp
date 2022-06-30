<?php
return [

    '_parent' => 'parent',

    'ARRAY_LIST_KEY_ONE'   => [1, 2, 3, 4],
    'ARRAY_LIST_KEY_TWO'   => [1, ['test', 'another test', 'something else', 'completely different test', 'omg test test test'], 3, 4],
    'ARRAY_LIST_KEY_THREE' => [['test' => false, 'omg' => true], ['test' => false, 'omg' => true], ['test' => false, 'omg' => true]],

    'ARRAY_DICT_KEY_ONE'   => ['int_key' => 123, 'bool_key' => false, 'string_key' => 'this is a test"', 'array_list_key' => [1,2,3]],
    'ARRAY_DICT_KEY_TWO'   => ['int_key' => 123, 'dict_key' => ['int_key' => 123, 'dict_key' => [['int_key' => 123, 'dict_key' => ['key' => 'value']]]]],
    'ARRAY_DICT_KEY_THREE' => ['very-very-very-very-very-very-very-very-long-key' => 'very-very-very-very-very-very-very-very-long-value'],

];
