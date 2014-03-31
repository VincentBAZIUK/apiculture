<?php


namespace Apiculture\Entity;

use Apiculture\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Table(name="intervention")
 * @Entity
 */
class Intervention {
    /**
     * @param int $idHive
     */
    public function setIdHive($idHive)
    {
        $this->idHive = $idHive;
    }

    /**
     * @return int
     */
    public function getIdHive()
    {
        return $this->idHive;
    }
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $description;

    /**
     * @var integer
     * @ORM\Column(type="integer", length = 10)
     */
    protected $id_hive;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id_hive;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @var date
     * @ORM\Column(type="string")
     */
    protected $date;

    /**
     * @param \Apiculture\Entity\date $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return \Apiculture\Entity\date
     */
    public function getDate()
    {
        return $this->date;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }


}