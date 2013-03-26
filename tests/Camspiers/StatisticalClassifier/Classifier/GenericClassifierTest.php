<?php

namespace Camspiers\StatisticalClassifier\Classifier;

use Camspiers\StatisticalClassifier\ClassificationRule\ClassificationRuleInterface;
use Camspiers\StatisticalClassifier\Index\IndexInterface;
use Camspiers\StatisticalClassifier\Transform\TransformInterface;
use Camspiers\StatisticalClassifier\Tokenizer\TokenizerInterface;
use Camspiers\StatisticalClassifier\Normalizer\NormalizerInterface;

use Camspiers\StatisticalClassifier\Index\Index;
use Camspiers\StatisticalClassifier\DataSource\DataArray;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-03-26 at 18:06:08.
 */
class GenericClassifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GenericClassifier
     */
    protected $classifier;
    /**
     * @var Index
     */
    protected $index;

    protected function setUp()
    {
        $this->classifier = new GenericClassifier(
            $this->index = new Index(
                new DataArray(
                    array(
                        'spam' => array(
                            'Some spam document'
                        ),
                        'ham' => array(
                            'Some ham document'
                        )
                    )
                )
            ),
            new TestClassificationRule(),
            new TestTokenizer(),
            new TestNormalizer()
        );
    }
    public function testGetTransforms()
    {
        $this->assertEquals(array(), $this->classifier->getTransforms());
        return $this->classifier;
    }
    /**
     * @depends testGetTransforms
     */
    public function testAddTransform($classifier)
    {
        $classifier->addTransform(
            $transform = new TestTransform(
                new TestTokenizer(),
                new TestNormalizer()
            )
        );
        $this->assertEquals(array($transform), $classifier->getTransforms());
        return $classifier;
    }

    /**
     * @depends testAddTransform
     */
    public function testClassify($classifier)
    {
        $this->assertEquals(
            'spam',
            $classifier->classify('Some spam document')
        );
        return $classifier;
    }

    /**
     * @depends testClassify
     */
    public function testIs($classifier)
    {
        $this->assertTrue(
            $classifier->is('spam', 'Some spam document')
        );
        $this->assertTrue(
            $classifier->is('ham', 'Some ham document')
        );
        return $classifier;
    }

    /**
     * @depends testIs
     * @expectedException RuntimeException
     * @expectedExceptionMessage The category 'test' doesn't exist
     */
    public function testIsException($classifier)
    {
        $classifier->is('test', 'Some spam document');
    }

    /**
     * @depends testGetTransforms
     */
    public function testSetTransforms($classifier)
    {
        $classifier->setTransforms(
            $ts = array(
                new TestTransform(
                    new TestTokenizer(),
                    new TestNormalizer()
                )
            )
        );
        $this->assertEquals($ts, $classifier->getTransforms());
        return $classifier;
    }
    /**
     * @depends testSetTransforms
     * @expectedException Exception
     */
    public function testSetTransformsException($classifier)
    {
        $classifier->setTransforms(array(new \stdClass));
    }

    public function testGetIndex()
    {
        $this->assertEquals($this->index, $this->classifier->getIndex());
        return $this->classifier;
    }
    /**
     * @depends testGetIndex
     */
    public function testSetIndex($classifier)
    {
        $classifier->setIndex($index = new Index);
        $this->assertEquals($index, $classifier->getIndex());
        return array($classifier, $index);
    }
    /**
     * @depends testSetIndex
     */
    public function testPrepareIndex($objs)
    {
        list($classifier, $index) = $objs;
        $classifier->prepareIndex();
        $this->assertTrue($index->isPrepared());
    }
}

class TestClassificationRule implements ClassificationRuleInterface
{
    public function classify(IndexInterface $index, $document)
    {
        foreach ($index->getPartition('end') as $category => $documents) {
            foreach ($documents as $doc) {
                if ($document == $doc) {
                    return $category;
                }
            }
        }
        return false;
    }
}

class TestTransform implements TransformInterface
{
    protected $t;
    protected $n;

    public function __construct($t, $n)
    {
        $this->t = $t;
        $this->n = $n;
    }

    public function apply(IndexInterface $index)
    {
        $data = $index->getDataSource()->getData();
        foreach ($data as $category => $documents) {
            foreach ($documents as $documentIndex => $document) {
                $data[$category][$documentIndex] = $this->n->normalize(
                    $this->t->tokenize(
                        $document
                    )
                );
            }
        }
        $index->setPartition(
            'end',
            $data
        );
    }
}

class TestTokenizer implements TokenizerInterface
{
    public function tokenize($document)
    {
        return explode(' ', $document);
    }
}

class TestNormalizer implements NormalizerInterface
{
    public function normalize(array $tokens)
    {
        return $tokens;
    }
}