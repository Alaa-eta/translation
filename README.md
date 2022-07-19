# Translation


## Description

Translation is a developer friendly, database driven, automatic translator for Laravel 9. Wouldn't it be nice to just write text regularly
on your application and have it automatically translated, added to the database, and cached at runtime? Take this for example:

Controller:

    public function index()
    {
        return view('home.index');
    }

View:

    @extends('layout.default')
    
    {{ _t('Welcome to our home page') }}

Seen:

    Welcome to our home page

When you visit the page, you won't notice anything different, but if you take a look at your database, your default
application locale has already been created, and the translation attached to that locale.

Now if we set locale to something different, such as Arabic (ar), it'll automatically translate it for you.

Controller:

    public function index()
    {
        app()->setLocale('ar');
        
        return view('home.index');
    }

View:

    @extends('layout.default')
    
    {{ _t('Welcome to our home page') }}

Seen:

    Welcome to our home page

We can even use placeholders for dynamic content:

View:

    {{ _t('Welcome :name, to our home page', ['name' => 'John']) }}

Seen:

    Welcome John, to our home page


## Installation

Publish the config

    php artisan vendor:publish --tag=translation-config

Run the migrations

    php artisan migrate

Your good to go!

## Usage

Anywhere in your application, either use the the shorthand function (can be disabled in config file)

    _t('Translate me!')

Or

    Translation::translate('Translate me!')

This is typically most useful in blade views:

    {{ _t('Translate me!') }}

And you can even translate models easily by just plugging in your content:

    {{ _t($post->title) }}

Or use placeholders:

    {{ _t('Post :title', ['title' => $post->title]) }}

In your `translations` database table you'll have:

    | id | key | value | language_code |
      1        'Translate me!'         'Translate me!'        'en'

To switch languages for the users session, all you need to call is:

    app()->setLocale('en') // Setting to Englisg locale

Locales are automatically created when you call the ` app()->setLocale($code)` method,
and when the translate function is called, it will automatically create a new translation record
for the new locale, with the default locale translation. The default locale is taken from the laravel `app.php` config file.

this in your `translations` table:

    | id | key | value | language_code |
      1        'Translate me!'         'Translate me!'        'en'
      1        'Translate me!'         'Translate me!'        'ar'


You can now update the translation on the new record and it will be shown wherever it's called:

    _t('Translate me!')`

###### Need to translate a single piece of text without setting the users default locale?

Just pass in the locale into the third argument inside the translation functions show above like so:


View:

    {{ _t('Our website also supports english!', [], 'en') }}
    
    <br>
    
    {{ _t('And arabic!', [], 'ar') }}


This is great for showing users that your site supports different languages without changing the entire site
language itself. You can also perform replacements like usual:

View:

    {{ _t('Hello :name, we also support french!', ['name' => 'John Doe'], 'ar') }}

Seen:

    Bonjour John Doe , nous soutenons aussi le français !

Performing this will also create the locale in your database, save the translation, and cache it in case you need it again.

You must provide you're own way of updating translations (controllers/views etc) using the eloquent models provided.

## Injecting Translation

As of `v1.3.4` you can now inject the `Translation` contract into your controllers without the use of a facade:

```php
use Alaaeta\Translation\Contracts\Translation;

class BlogController extends Controller
{
    /**
     * @var Translation
     */
    protected $translation;
    
    /**
     * Constructor.
     *
     * @param Translation $translation
     */
    public function __construct(Translation $translation)
    {
        $this->translation = $translation;
    }
    
    /**
     * Returns all blog entries.
     *
     * @return Illuminate\View\View
     */
    public function index()
    {
        $title = $this->translation->translate('My Blog');
        
        $entries = Blog::all();
        
        return view('pages.blog.index', compact('title', 'entries'));
    }
}
```

## Models

By default, translation model are included and selected inside the configuration file. If you'd like to use your own models
you must create them and implement their trait. Here's an example:

The Translation Model:

    use Stevebauman\Translation\Traits\TranslationTrait;
    use Illuminate\Database\Eloquent\Model;
    
    class Translation extends Model
    {
        use TranslationTrait;
    
        /**
         * The locale translations table.
         *
         * @var string
         */
        protected $table = 'translations';
    
        /**
         * The fillable locale translation attributes.
         *
         * @var array
         */
        protected $fillable = [
            'key',
            'value',
            'language_code',
        ];
       
    }

Once you've created translation model, insert them into the `translation.php` configuration file:

    |--------------------------------------------------------------------------
    | Translation Model
    |--------------------------------------------------------------------------
    |
    |  The translation model is used for storing translations.
    |
    */

    'translation' => App\Models\Translation::class,

## Routes

Translating your site with a locale prefix couldn't be easier. First inside your `app/Http/Kernel.php` file, insert
the locale middleware:

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        
        // Insert Locale Middleware
        'locale' => \Stevebauman\Translation\Middleware\LocaleMiddleware::class
    ];

Now, in your `app/Http/routes.php` file, insert the middleware and the following Translation method in the route
group prefix like so:

    Route::group(['prefix' => Translation::getRoutePrefix(), 'middleware' => ['locale']], function()
    {
        Route::get('home', function ()
        {
            return view('home');
        });
    });

You should now be able to access routes such as:

    http://localhost/home
    http://localhost/en/home
    http://localhost/fr/home

