<?php


namespace Gravity\NodeBundle\Structure\Model;

use GravityCMS\Component\Field\FieldReference;

/**
 * Class ContentType
 *
 * @package Gravity\NodeBundle\Structure\Model
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class ContentType
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var FieldReference[]
     */
    protected $fields = [];

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return FieldReference[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param FieldReference[] $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }
}