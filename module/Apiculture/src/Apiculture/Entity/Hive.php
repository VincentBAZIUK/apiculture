<?php


namespace Apiculture\Entity;

use Apiculture\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @ORM\Table(name="hive")
 * @Entity
 */
class Hive {
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
    protected $name;
    
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="hives", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idUser", referencedColumnName="id")
     * })
     */
    protected $user;
    
    /**
     * @var float
     * @ORM\Column(type="float")
     */
    protected $longitude;
    
    /**
     * @var float
     * @ORM\Column(type="float")
     */
    protected $latitude;
    
    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($longitude) 
    {
        $this->longitude = $longitude;
    }

    public function getLatitude() 
    {
        return $this->latitude;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }
    
    public function getId() 
    {
        return $this->id;
    }

    public function setId($id) 
    {
        $this->id = $id;
    }

    public function getName() 
    {
        return $this->name;
    }

    public function setName($name) 
    {
        $this->name = $name;
    }

    public function getUser() 
    {
        return $this->user;
    }

    public function setUser(User $user) 
    {
        $this->user = $user;
    }



    
    
}