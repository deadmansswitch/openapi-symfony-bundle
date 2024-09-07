<?php

declare(strict_types=1);

use DeadMansSwitch\OpenAPI\Schema\V3_0\Extra\SchemasMap;
use DeadMansSwitch\OpenAPI\Schema\V3_0\Schema;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperInterface;

test('PropertyReflection with named builtin type', function (string $property) {
    // Setup
    $class = new class() {
        public int $id = 1;
        public float $rating = 1.0;
        public string $name = 'John Doe';
        public bool $active = true;
    };

    $reflection = new ReflectionProperty(class: $class, property: $property);
    expect($reflection)->toBeInstanceOf(Reflector::class);

    $mapper = $this->container->get(SchemaMapperInterface::class);
    expect($mapper)->toBeInstanceOf(SchemaMapperInterface::class);

    // Act
    $schema = $mapper->map($reflection);

    // Assert
    expect($schema)->toBeInstanceOf(Schema::class);
})->with(['id', 'rating', 'name', 'active']);

test('PropertyReflection with backed enum type', function () {
    // Setup
    enum Status: string {
        case Active = 'active';
        case Inactive = 'inactive';
    }

    $class = new class() {
        public Status $status = Status::Active;
    };

    $reflection = new ReflectionProperty(class: $class, property: 'status');
    expect($reflection)->toBeInstanceOf(Reflector::class);

    $mapper = $this->container->get(SchemaMapperInterface::class);
    expect($mapper)->toBeInstanceOf(SchemaMapperInterface::class);

    // Act
    $schema = $mapper->map($reflection);

    // Assert
    expect($schema)
        ->toBeInstanceOf(Schema::class)
        ->and($schema->type)->toBe('string')
        ->and($schema->format)->toBe('enum')
    ;
});

test('PropertyReflection with object type', function () {
    // Setup
    class Subclass {
        public function __construct(
            public int $id,
            public string $name,
        ) {}
    }

    class TargetClass {
        public function __construct(
            public int $id,
            public Subclass $subclass,
        ) {}
    }

    $obj = new TargetClass(
        id: 1,
        subclass: new Subclass(
            id: 5,
            name: 'Jack',
        ),
    );

    $mapper = $this->container->get(SchemaMapperInterface::class);
    expect($mapper)->toBeInstanceOf(SchemaMapperInterface::class);

    $ref = new ReflectionProperty(class: $obj, property: 'subclass');
    expect($ref)->toBeInstanceOf(Reflector::class);

    // Act
    $schema = $mapper->map($ref);

    // Assert
    expect($schema)
        ->toBeInstanceOf(Schema::class)
        ->and($schema->type)->toBe('object')
        ->and($schema->properties)->toBeInstanceOf(SchemasMap::class)
        ->and($schema->properties['id'])->toBeInstanceOf(Schema::class)
        ->and($schema->properties['name'])->toBeInstanceOf(Schema::class)
    ;
});