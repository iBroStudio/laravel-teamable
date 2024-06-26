<?php

return [

    'auto' => [
        'create' => true, // Create team when parent model is created
        'delete' => true, // Delete team when parent model is deleted
    ],

    // Teamable model attribute to generate Team name
    'model_name_attribute' => [
        'default' => 'name',
        //Model::class => 'attribute'
    ],
];
