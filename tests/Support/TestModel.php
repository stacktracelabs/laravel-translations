<?php


namespace Tests\Support;


use Illuminate\Database\Eloquent\Model;
use StackTrace\Translations\HasTranslations;

class TestModel extends Model
{
    use HasTranslations;

    protected $guarded = false;

    protected array $translatable = [
        'title',
    ];
}
