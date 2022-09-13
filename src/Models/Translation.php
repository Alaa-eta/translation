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
        $query =  $this->where('language_code',$language);
        if ($lang_file_name) $query->where(DB::raw('SUBSTRING_INDEX(`key`, "." ,1)'), '=' , $lang_file_name);
        return $query->paginate((request()->pageNumber ?? 10))->appends(request()->query());
    }

    public function findTranslation($translationKey)
    {
        $translations =   $this->where('key',$translationKey)->get();
        if (count($translations) == 0) throw new \Exception('No Data Founded');
        return $translations;
    }

    public function saveEntity($translationModel)
    {
        $translationModel->save();
    }
}
