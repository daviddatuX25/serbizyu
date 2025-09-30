<?php

namespace App\DTO;
interface Dto
{
    public function toArray();
    public function toJSON();

    // errors, warnings, isSuccess
    public function getErrors();
    public function getWarnings();
    public function isSuccess();

}