<?php

namespace Student\Manager\Traits;

trait Form_Error
{
    public $errors = [];

    public function has_error($key)
    {
        return isset($this->errors[$key]) ? true : false;
    }

    public function get_error($key)
    {
        if (isset($this->errors[$key])) {
            return $this->errors[$key];
        }

        return false;
    }
}
