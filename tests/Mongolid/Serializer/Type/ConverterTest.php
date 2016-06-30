<?php
namespace Mongolid\Serializer\Type;

use MongoDB\BSON\UTCDateTime as MongoUTCDateTime;
use MongoDB\BSON\ObjectID as MongoObjectID;
use Mongolid\Serializer\ConvertableInterface;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Test case for Converter class
 */
class ConvererTest extends TestCase
{
    /**
     * @var Converter
     */
    protected $converter;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->converter = new Converter();
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->converter);
    }

    public function testConverterShouldBeAnInstanceOfConvertableInterface()
    {
        $this->assertInstanceOf(ConvertableInterface::class, $this->converter);
    }

    public function testConvertShouldReplaceAllMongoObjectsToOurObjects()
    {
        $mongoId   = new MongoObjectID();
        $timestamp = time();
        $mongoDate = new MongoUTCDateTime($timestamp*1000);
        $id        = new ObjectID($mongoId);
        $date      = new UTCDateTime($timestamp);

        $data = [
            '_id' => $id,
            'created_at' => $date,
            'parents' => [$id, $id, $id],
            'comments' => [
                [
                    'author' => 'Jhon',
                    'date' => $date,
                ],
                [
                    'author' => 'Doe',
                    'date' => $date,
                    'versions' => [
                        [
                            '_id' => $id,
                            'date' => $date,
                            'content' => 'Awsome',
                        ],
                        [
                            '_id' => $id,
                            'date' => $date,
                            'content' => 'Great',
                        ],
                    ]
                ],
            ]
        ];

        $expected = [
            '_id' => $mongoId,
            'created_at' => $mongoDate,
            'parents' => [$mongoId, $mongoId, $mongoId],
            'comments' => [
                [
                    'author' => 'Jhon',
                    'date' => $mongoDate,
                ],
                [
                    'author' => 'Doe',
                    'date' => $mongoDate,
                    'versions' => [
                        [
                            '_id' => $mongoId,
                            'date' => $mongoDate,
                            'content' => 'Awsome',
                        ],
                        [
                            '_id' => $mongoId,
                            'date' => $mongoDate,
                            'content' => 'Great',
                        ],
                    ]
                ],
            ]
        ];

        $this->assertEquals($expected, $this->converter->convert($data));
    }

    public function testConvertShouldReplaceOurObjectsToMongoDBObjects()
    {
        $mongoId   = new MongoObjectID();
        $timestamp = time();
        $mongoDate = new MongoUTCDateTime($timestamp*1000);
        $id        = new ObjectID($mongoId);
        $date      = new UTCDateTime($timestamp);

        $data = [
            '_id' => $mongoId,
            'created_at' => $mongoDate,
            'parents' => [$mongoId, $mongoId, $mongoId],
            'comments' => [
                [
                    'author' => 'Jhon',
                    'date' => $mongoDate,
                ],
                [
                    'author' => 'Doe',
                    'date' => $mongoDate,
                    'versions' => [
                        [
                            '_id' => $mongoId,
                            'date' => $mongoDate,
                            'content' => 'Awsome',
                        ],
                        [
                            '_id' => $mongoId,
                            'date' => $mongoDate,
                            'content' => 'Great',
                        ],
                    ]
                ],
            ]
        ];

        $expected = [
            '_id' => $id,
            'created_at' => $date,
            'parents' => [$id, $id, $id],
            'comments' => [
                [
                    'author' => 'Jhon',
                    'date' => $date,
                ],
                [
                    'author' => 'Doe',
                    'date' => $date,
                    'versions' => [
                        [
                            '_id' => $id,
                            'date' => $date,
                            'content' => 'Awsome',
                        ],
                        [
                            '_id' => $id,
                            'date' => $date,
                            'content' => 'Great',
                        ],
                    ]
                ],
            ]
        ];

        $this->assertEquals($expected, $this->converter->unconvert($data));
    }
}
