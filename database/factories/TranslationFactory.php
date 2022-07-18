<?php

namespace Alaaeta\Translation\Database\Factories;;

use alaaeta\Translation\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition()
    {
        return [
            'key'     => $this->faker->text,
            'value'     => $this->faker->text,
            'language_code'         => 'en'
        ];
    }


}
