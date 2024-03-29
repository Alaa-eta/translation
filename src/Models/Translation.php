<?php

namespace Alaaeta\Translation\Models;


use Alaaeta\Translation\Database\Factories\TranslationFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function getTypes()
    {
        return DB::select( DB::raw("SELECT  SUBSTRING_INDEX(`key`,'.',1) AS type  FROM translations group by type") );
    }

    public function getTranslations()
    {
        $language = request()->lang_name ?? 'en';
        $lang_file_name = request()->lang_file_name ?? null;
        $key = request()->key ?? null;
        $query =  $this->where('language_code',$language);
        if ($lang_file_name) $query->where(DB::raw('SUBSTRING_INDEX(`key`, "." ,1)'), '=' , $lang_file_name);
        if ($key) {
            $query->where(function ($query2) use ($key){
                $query2->where('key','LIKE',"%{$key}%")->orWhere('value','LIKE',"%{$key}%");
            });
        }
        return $query->paginate((request()->pageNumber ?? 10))->appends(request()->query());
    }

    public function findTranslation($translationKey)
    {
        $translations =   $this->where(DB::raw("BINARY `key`"), $translationKey)->get();
        if (count($translations) == 0) throw new \Exception('No Data Founded');
        return $translations;
    }

    public function updateOrCreateTranslation($matchArray , $changeArray)
    {
        $this->updateOrCreate($matchArray , $changeArray);
    }

    public function destroyTranslation($key)
    {
        $this->where('key',$key)->delete();
    }

}
