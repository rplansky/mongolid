<?php
namespace Mongolid\Serializer\Type;

use MongoDB\BSON\ObjectID as MongoObjectID;
use MongoDB\BSON\UTCDateTime as MongoUTCDateTime;
use Mongolid\Serializer\ConvertableInterface;
use Mongolid\Serializer\SerializableTypeInterface;

/**
 * This class is responsible to convert MongoDB objects to types of our domain
 * and vice-versa.
 */
class Converter implements ConvertableInterface
{
    /**
     * @var string[]
     */
    protected $mappedTypes = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mappedTypes = [
            ObjectID::class    => MongoObjectID::class,
            UTCDateTime::class => MongoUTCDateTime::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function convert(array $data)
    {
        array_walk_recursive($data, function (&$value, $key) {
            if ($value instanceof SerializableTypeInterface) {
                $value = $value->convert();
            }
        });

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function unconvert(array $data)
    {
        array_walk_recursive($data, function (&$value) {
            $className = $this->getReflectionClass($value);
            if (class_exists($className)) {
                return $value = new $className($value);
            }
        });

        return $data;
    }

    /**
     * Checks if the given parameter is a mapped type and return its index.
     *
     * @param  mixed $value Value of array to check.
     *
     * @return boolean|integer
     */
    protected function getReflectionClass($value)
    {
        if (false === is_object($value)) {
            return false;
        }

        return array_search(get_class($value), $this->mappedTypes);
    }
}
