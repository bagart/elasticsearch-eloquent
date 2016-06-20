<?php

namespace Isswp101\Persimmon\Traits;

use Isswp101\Persimmon\Elasticsearch\InnerHits;
use Isswp101\Persimmon\Elasticsearch\Response;
use Isswp101\Persimmon\Exceptions\InvalidModelEndpointException;

trait Elasticsearchable
{
    /**
     * @var string
     */
    protected static $_index;

    /**
     * @var string
     */
    protected static $_type;

    /**
     * @var string
     */
    protected static $_parentType;

    /**
     * @var InnerHits
     */
    public $_innerHits;

    /**
     * @var float
     */
    public $_score;

    /**
     * @var int
     */
    public $_position;

    /**
     * @return string
     */
    public static function getIndex()
    {
        return static::$_index;
    }

    /**
     * @return string
     */
    public static function getType()
    {
        return static::$_type;
    }

    /**
     * @return string
     */
    public static function getParentType()
    {
        return static::$_parentType;
    }

    /**
     * @throws InvalidModelEndpointException
     */
    final protected function validateModelEndpoint()
    {
        if (!$this->getIndex()) {
            throw new InvalidModelEndpointException(sprintf(
                'Please specify the index for your Elasticsearch model %s',
                static::class
            ));
        }

        if (!$this->getType()) {
            throw new InvalidModelEndpointException(sprintf(
                'Please specify the type for your Elasticsearch model %s',
                static::class
            ));
        }
    }

    /**
     * @param InnerHits $innerHits
     */
    protected function setInnerHits(InnerHits $innerHits)
    {
        $this->_innerHits = $innerHits;
    }

    /**
     * @return InnerHits
     */
    public function getInnerHits()
    {
        return $this->_innerHits;
    }

    /**
     * @param array $response
     * @return $this
     */
    public function fillByResponse(array $response)
    {
        $res = new Response($response);
        $this->fill($res->getSource());
        $this->setId($res->getId());
        return $this;
    }

    /**
     * @param array $response
     * @return $this
     */
    public function fillByInnerHits(array $response)
    {
        $innerHits = new InnerHits($response);
        $this->setInnerHits($innerHits);
        $this->setParentId($innerHits->getParentId($this->getParentType()));
        return $this;
    }

    public function getPosition()
    {
        return $this->_position;
    }

    abstract public function fill(array $attributes);

    abstract public function getId();

    abstract public function setId($id);

    abstract public function getParentId();

    abstract public function setParentId($id);
}
