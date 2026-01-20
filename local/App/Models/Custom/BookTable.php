<?php

namespace Otus\Orm;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;

class BookTable extends DataManager
{
    public const BOOK_MINIMAL_DESCRIPTION_LENGTH = 30;

    public static function getTableName(): string
    {
        return 'aholin_book';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new StringField('TITLE'))
                ->configureRequired()
                ->configureSize(255),

            (new IntegerField('YEAR', [
                'validation' => [__CLASS__, 'validateYear'],
            ])),//->addValidator(__CLASS__, 'validateYear'),

            (new IntegerField('PAGES')),

            (new TextField('DESCRIPTION', [
                'validation' => [__CLASS__, 'validateDescription'],
            ])),

            (new DateField('PUBLISH_DATE'))->configureDefaultValue((new \DateTime())->format('d.m.Y')),

            (new ManyToMany('AUTHORS', AuthorTable::class))
                ->configureTableName('aholin_book_author')
                ->configureLocalPrimary('ID', 'BOOK_ID')
                ->configureRemotePrimary('ID', 'AUTHOR_ID'),
        ];
    }

    public static function validateDescription()
    {
        return [
            new LengthValidator(self::BOOK_MINIMAL_DESCRIPTION_LENGTH),
        ];
    }

    public static function validateYear()
    {
        return [
            function ($value) {
                if ($value < 0) {
                    return 'Год не должен быть меньше 0';
                } elseif ($value > 3000) {
                    return 'Год не должен быть больше 3000';
                } else {
                    return true;
                }
            }
        ];
    }
}
