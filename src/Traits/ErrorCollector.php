<?php

namespace Lxj\Review\Bot\Traits;

trait ErrorCollector
{
    protected $errors = [];

    protected function collectErrors($errors)
    {
        $this->errors = array_merge($this->errors, $errors);
    }

    public function clearErrors()
    {
        $this->errors = [];
        return $this;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
