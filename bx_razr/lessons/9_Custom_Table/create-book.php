<?php
use Otus\Orm\BookTable;
use Otus\Orm\AuthorTable;
use Bitrix\Main\Type\Date;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

$APPLICATION->SetTitle('Работа с собственными таблицами - добавление');

$authorId = (AuthorTable::add([
    'FIRST_NAME' => 'O',
    'LAST_NAME' => 'Henry',
    'SECOND_NAME' => '',
    'BIRTH_DATE' => (new Date())->add('-200Y'),
]))->getId();

$author = AuthorTable::getById($authorId)->fetchObject();

$bookId = (BookTable::add([
	'TITLE' => 'Дары волхвов',
	'YEAR' => 1872,
	'PAGES' => 412,
	'DESCRIPTION' => 'Сборник рассказов писателя О. Генри',
]))->getId();

$book = BookTable::getById($bookId)->fetchObject();

$book->addToAuthors($author);
$book->save();

echo sprintf(
    'Имя автора: %s; Фамилия автора: %s; Дата рождения автора %s<br>',
    $author->getFirstName(),
    $author->getLastName(),
    $author->getBirthDate()->format('d.m.Y'),
);

$bookAuthors = $book->getAuthors();
foreach ($bookAuthors as $bookAuthor) {
    dump($bookAuthor->getId());
    dump($bookAuthor->getFirstName());
    dump($bookAuthor->getLastName());
    dump($bookAuthor->getBirthDate());
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
