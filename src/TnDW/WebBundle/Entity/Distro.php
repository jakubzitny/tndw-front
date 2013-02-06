<?php

namespace TnDW\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="distros")
 */
class Distro {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $os;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $homepage;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $wikipage;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $screenshots;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Distro
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set os
     *
     * @param string $os
     * @return Distro
     */
    public function setOs($os) {
        $this->os = $os;

        return $this;
    }

    /**
     * Get os
     *
     * @return string 
     */
    public function getOs() {
        return $this->os;
    }

    /**
     * Set homepage
     *
     * @param string $homepage
     * @return Distro
     */
    public function setHomepage($homepage) {
        $this->homepage = $homepage;

        return $this;
    }

    /**
     * Get homepage
     *
     * @return string 
     */
    public function getHomepage() {
        return $this->homepage;
    }

    /**
     * Set wikipage
     *
     * @param string $wikipage
     * @return Distro
     */
    public function setWikipage($wikipage) {
        $this->wikipage = $wikipage;

        return $this;
    }

    /**
     * Get wikipage
     *
     * @return string 
     */
    public function getWikipage() {
        return $this->wikipage;
    }

    /**
     * Set screenshots
     *
     * @param string $screenshots
     * @return Distro
     */
    public function setScreenshots($screenshots) {
        $this->screenshots = $screenshots;

        return $this;
    }

    /**
     * Get screenshots
     *
     * @return string 
     */
    public function getScreenshots() {
        return $this->screenshots;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Distro
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Distro
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus() {
        return $this->status;
    }

}