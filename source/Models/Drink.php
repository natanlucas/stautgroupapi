<?php

namespace Source\Models;

use Source\Core\Model;

class Drink extends Model
{
    public function __construct()
    {
        parent::__construct("drinks",["id"],["user_id", "drink_ml"]);
    }

    /**
     * @return bool
     */
    public function save(): bool
    {

        return parent::save();

        var_dump($this->fail());
    }

}