<?php

use App\Example\Example;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpStanExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

require_once __DIR__ . '/vendor/autoload.php';

// a full list of extractors is shown further below
$phpDocExtractor = new PhpDocExtractor();
$phpStanExtractor = new PhpStanExtractor();
$reflectionExtractor = new ReflectionExtractor();

// list of PropertyListExtractorInterface (any iterable)
$listExtractors = [$reflectionExtractor];

// list of PropertyTypeExtractorInterface (any iterable)
$typeExtractors = [
    $phpStanExtractor,
//    $phpDocExtractor,
    $reflectionExtractor,
];

// list of PropertyDescriptionExtractorInterface (any iterable)
$descriptionExtractors = [$phpDocExtractor];

// list of PropertyAccessExtractorInterface (any iterable)
$accessExtractors = [$reflectionExtractor];

// list of PropertyInitializableExtractorInterface (any iterable)
$propertyInitializableExtractors = [$reflectionExtractor];

$propertyInfo = new PropertyInfoExtractor(
    $listExtractors,
    $typeExtractors,
    $descriptionExtractors,
    $accessExtractors,
    $propertyInitializableExtractors
);

// see below for more examples
$properties = $propertyInfo->getTypes(Example::class, 'children');

// MUST returns ""App\Child\ExampleChild"" but it returns "App\Example\ExampleChild"
dump($properties[0]->getCollectionValueTypes()[0]->getClassName());

$normalizers = [new ObjectNormalizer(propertyTypeExtractor: $propertyInfo)];

$serializer = new Serializer($normalizers);

$result = $serializer->denormalize([
    'children' => [
        'testProperty' => '123',
    ],
], Example::class);

dump($result);
