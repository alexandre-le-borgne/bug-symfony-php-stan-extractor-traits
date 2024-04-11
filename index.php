<?php

use App\Example\Example;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpStanExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

require_once __DIR__ . '/vendor/autoload.php';

$phpDocExtractor = new PhpDocExtractor();
$phpStanExtractor = new PhpStanExtractor();
$reflectionExtractor = new ReflectionExtractor();

$listExtractors = [$reflectionExtractor];

$typeExtractors = [
    $phpStanExtractor, // Comment this line to avoid the bug
    $phpDocExtractor,
    $reflectionExtractor,
];

$descriptionExtractors = [$phpDocExtractor];
$accessExtractors = [$reflectionExtractor];
$propertyInitializableExtractors = [$reflectionExtractor];

$propertyInfo = new PropertyInfoExtractor(
    $listExtractors,
    $typeExtractors,
    $descriptionExtractors,
    $accessExtractors,
    $propertyInitializableExtractors
);

$properties = $propertyInfo->getTypes(Example::class, 'children');

// MUST returns ""App\Child\ExampleChild"" but it returns "App\Example\ExampleChild"
dump($properties[0]->getCollectionValueTypes()[0]->getClassName());

$normalizers = [
    new ArrayDenormalizer(),
    new ObjectNormalizer(propertyTypeExtractor: $propertyInfo),
];

$serializer = new Serializer($normalizers);

$result = $serializer->denormalize([
    'children' => [[
        'testProperty' => '123',
    ]],
], Example::class);

dump($result);
