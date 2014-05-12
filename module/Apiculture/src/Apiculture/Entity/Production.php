<?php


namespace Apiculture\Entity;

use Apiculture\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Table(name="production")
 * @Entity
 */
class Production {

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var integer
     * @ORM\Column(type="integer", length = 10)
     */
    protected $id_hive;


    /**
     * @var date
     * @ORM\Column(type="string")
     */
    protected $date;

    /**
     * @var production
     * @orm\Column(type="float")
     */
    protected $production;

    /**
     * @param int $production
     */
    public function setProduction($production)
    {
        $this->production = $production;
    }

    /**
     * @return int
     */
    public function getProduction()
    {
        return $this->production;
    }

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

    /**
     * @param int $idHive
     */
    public function setIdHive($idHive)
    {
        $this->id_hive = $idHive;
    }

    /**
     * @return int
     */
    public function getIdHive()
    {
        return $this->idHive;
    }

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


}