<?php

namespace Alaaeta\Translation\Tests\Feature;


use \Alaaeta\Translation\Facades\Translation;
use Alaaeta\Translation\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;

class InitTest extends TestCase
{
    use RefreshDatabase;

    public function testTranslationInvalidText()
    {
        $this->expectException(InvalidArgumentException::class);
        Translation::translate(['Invalid']);
    }

    public function testTranslationPlaceHolders()
    {
        $this->assertEquals('api.Hi :name', Translation::translate('api.Hi :name'));
        $translations = \Alaaeta\Translation\Models\Translation::get();
        $this->assertEquals('api.Hi :name', $translations->first()->key);
        $this->assertCount(1, $translations);
    }

    public function testTranslationKeyNotDuplicated()
    {
        $this->assertEquals('api.Hi :name', Translation::translate('api.Hi :name'));
        $this->assertEquals('api.Hi :name', Translation::translate('api.Hi :name'));
        $translations = \Alaaeta\Translation\Models\Translation::get();
        $this->assertCount(1, $translations);
    }

    public function testTranslationKeyMultipleAdded()
    {
        $this->assertEquals('api.Hi :name', Translation::translate('api.Hi :name'));
        $this->assertEquals('api.Hi :name1', Translation::translate('api.Hi :name1'));
        $translations = \Alaaeta\Translation\Models\Translation::get();
        $this->assertCount(2, $translations);
    }

    public function testTranslationPlaceHoldersDynamicLanguage()
    {
        $replace = ['name' => 'John'];
        $this->assertEquals('Hello John', Translation::translate('Hello :name', $replace));
    }

    public function testTranslationPlaceHoldersMultiple()
    {
        $replace = ['name' => 'John' , 'age' => 29];
        $this->assertEquals('Hello John 29', Translation::translate('Hello :name :age', $replace));
    }

    public function testTranslationPlaceHoldersMultipleOfTheSame()
    {
        $replace = ['name' => 'John'];
        $this->assertEquals('John John', Translation::translate(':name :name', $replace));

    }

//    public function testTranslationPlaceHoldersHtmlFormat()
//    {
//        $this->assertEquals('api.Welcome', Translation::translate('api.Welcome'));
//        $translation = \Alaaeta\Translation\Models\Translation::first();
//        $translation->value='<h1>Welcome</h1>';
//        $translation->save();
//        $this->assertEquals('<h1>Welcome</h1>', Translation::translate('api.Welcome'));
//        $translations = \Alaaeta\Translation\Models\Translation::get();
//        $this->assertCount(1, $translations);
//
//    }

//    public function testTranslationPlaceHoldersHtmlFormatWithDynamicLanguage()
//    {
//        $replace = ['name' => 'Alaa'];
//        $this->assertEquals('api.Welcome Alaa', Translation::translate('api.Welcome :name',$replace));
//        $translation = \Alaaeta\Translation\Models\Translation::first();
//        $translation->value='<h1>Welcome :name</h1>';
//        $translation->save();
//        $this->assertEquals('<h1>Welcome</h1>', Translation::translate('api.Welcome'));
//
//    }

//    public function first_test()
//    {
//        $translation = Translation::newFactory()->create();
//        $this->assertTrue(true);
//    }


}
