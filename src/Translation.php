<?php

namespace Alaaeta\Translation;

use Alaaeta\Translation\Exception\InvalidInputException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Translation
{

    protected $translationModel;

    protected $cache;

    private $cacheTime = 30;

    public function __construct()
    {
        $this->cache = app()->make('cache');
        $this->translationModel = app()->make(\Alaaeta\Translation\Models\Translation::class);
    }

    public function updateOrCreateTranslation($matchArray , $changeArray)
    {
        return $this->translationModel->updateOrCreateTranslation($matchArray , $changeArray);
    }

    public function destroy($key)
    {
        return $this->translationModel->destroyTranslation($key);
    }

    public function getTypes()
    {
        return $this->translationModel->getTypes();
    }

    public function getTranslations()
    {
        return $this->translationModel->getTranslations();
    }

    public function findTranslation($translationKey)
    {
        return $this->translationModel->findTranslation($translationKey);
    }


    public function translate($text = '', $replacements = [], $toLocale = '')
    {
        $this->validateText($text);
        $text = str_replace('/','-',$text);
        $translation = $this->firstOrCreateTranslation($text , $replacements);
        $defaultTranslation = $this->makeTranslationSafePlaceholders($translation->value,$replacements);
        return $defaultTranslation;
    }

    protected function firstOrCreateTranslation($text , $replacements)
    {
        $hasBinaryConstraint =  !$this->sqliteConnectionType() ? 'BINARY' : '';
        $cachedTranslation = $this->getCacheTranslation($text);
        if ($cachedTranslation) return $cachedTranslation;

        $translation = $this->translationModel
            ->where(DB::raw("$hasBinaryConstraint `key`"), $text)
            ->where('language_code' , app()->getLocale())->first();
        if (empty($translation)) {
            $translation = $this->translationModel->create([
                'key' => $text,
                'language_code' => app()->getLocale(),
                'value' => ucfirst(substr($text, (strpos($text, '.') ?: -1) + 1))
            ]);
        }
        $this->setCacheTranslation($text , $translation);
        return $translation;
    }

    protected function validateText($text)
    {
        if (!is_string($text)) {
            $message = 'Invalid Argument. You must supply a string to be translated.';
            throw new InvalidInputException($message);
        }

        return true;
    }

    protected function getCacheTranslation($text)
    {

        $id = $this->getTranslationCacheId($text);
        $cachedTranslation = $this->cache->get($id);
        return $cachedTranslation;
    }

    protected function getTranslationCacheId($text)
    {
        $compressed = $this->compressString($text);

        return sprintf('%s.%s', app()->getLocale(), $compressed);
    }

    protected function compressString($string)
    {
        return md5($string);
    }

    protected function setCacheTranslation($text , $translation)
    {
        $id = $this->getTranslationCacheId($text);
        if (!$this->cache->has($id)) {
            $this->cache->put($id, $translation, $this->cacheTime);
        }
        return $id;
    }

    private function sqliteConnectionType()
    {
        $databaseName = DB::connection()->getDatabaseName();
        return $databaseName == ':memory:' ? true : false;

    }

    private function makeTranslationSafePlaceholders($text, $replacements)
    {
        if (count($replacements) > 0) {
            foreach ($replacements as $key => $value) {
                $search = ':'.$key;
                $text = str_replace($search, $value, $text);
            }
        }
        return $text;
    }

}
