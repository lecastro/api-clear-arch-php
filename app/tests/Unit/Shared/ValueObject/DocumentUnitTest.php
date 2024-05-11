<?php

use Domain\Shared\ValueObjects\Document;

beforeEach(fn () => $this->document = [
    'cpf'   => [
        '123.456.789-09',
        '12345678909',
        '123123'
    ],
    'cnpj'  => [
        '22.459.895/0001-60',
        '22459895000160',
        'qeqw!@'
    ],
]);

it('should valid with mask CPF', function () {
    $documnet = new Document($this->document['cpf'][0]);

    expect($documnet->get())->toBe($this->document['cpf'][0]);
    expect($documnet->get())->not->toBeNull();
    expect($documnet->get())->toBeString();
});

it('should valid without mask CPF', function () {
    $documnet = new Document($this->document['cpf'][1]);

    expect($documnet->get())->toBe($this->document['cpf'][1]);
    expect($documnet->get())->not->toBeNull();
    expect($documnet->get())->toBeString();
});

test('should throw exception if receive invalid CPF', function () {
    new Document("123.SD.SAS");
})->throws(\InvalidArgumentException::class);

it('should valid with mask CNPJ', function () {
    $documnet = new Document($this->document['cnpj'][0]);
    
    expect($documnet->get())->toBe($this->document['cnpj'][0]);
    expect($documnet->get())->not->toBeNull();
    expect($documnet->get())->toBeString();
});

it('should valid without mask CNPJ', function () {
    $documnet = new Document($this->document['cnpj'][1]);
    
    expect($documnet->get())->toBe($this->document['cnpj'][1]);
    expect($documnet->get())->not->toBeNull();
    expect($documnet->get())->toBeString();
});

test('should throw exception if receive invalid CNPJ', function () {
    new Document("123AS");
})->throws(\InvalidArgumentException::class);

test('should throw exception if receive null', function () {
    new Document(null);
})->throws(\InvalidArgumentException::class);