<?php

namespace Alaaeta\Translation\Models;


use Alaaeta\Translation\Database\Factories\TranslationFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    protected static function newFactory()
    {
        return TranslationFactory::new();
    }
}
