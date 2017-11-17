<?php

namespace Ratings\Form;

use Zend\Form\Form;

class AddForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('rating');
        $this->add([
            'type' => 'Csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ]
        ]);
    }
}