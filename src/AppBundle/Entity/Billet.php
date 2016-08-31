<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Billet.
 *
 * @ORM\Table(name="billet")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BilletRepository")
 */
class Billet
{
    const PRIX = 16;
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="nom", type="string")
     */
    private $nom;

    /**
     * @ORM\Column(name="prenom", type="string")
     */
    private $prenom;

    /**
     * @ORM\Column(name="date_naissance", type= "date")
     */
    private $dateNaissance;

    /**
     * @ORM\Column(name="age", type="integer")
     */
    private $age;

    /**
     * @ORM\Column(name="reduction", type="boolean")
     */
    private $reduction;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Reservation", inversedBy="billets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $reservation;

    /**
     * @ORM\Column(name="prix", type="integer")
     */
    private $prix;

    public function __construct()
    {
        $this->reduction = false;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom.
     *
     * @param string $nom
     *
     * @return Billet
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom.
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom.
     *
     * @param string $prenom
     *
     * @return Billet
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom.
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set dateNaissance.
     *
     * @param \DateTime $dateNaissance
     *
     * @return Billet
     */
    public function setDateNaissance(\DateTime $dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;
        $this->setAge();

        return $this;
    }

    /**
     * Get dateNaissance.
     *
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set age.
     *
     * @param int $age
     *
     * @return Billet
     */
    public function setAge()
    {
        $now = new \DateTime();
        $age = $this->dateNaissance->diff($now);

        $this->age = $age->y;

        $this->setPrix();

        return $this;
    }

    /**
     * Get age.
     *
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set reduction.
     *
     * @param bool $reduction
     *
     * @return Billet
     */
    public function setReduction($reduction)
    {
        $this->reduction = $reduction;

        return $this;
    }
    public function addReduction()
    {
        $this->reduction = true;

        return $this;
    }

    /**
     * Get reduction.
     *
     * @return bool
     */
    public function getReduction()
    {
        if ($this->age < 12 && $this->age > 60) {
            return false;
        }

        return $this->reduction;
    }

    public function isReduit()
    {
        return $this->reduction;
    }

    public function setPrix()
    {
        if ($this->age < 4) {
            $this->prix = 0;
        } elseif ($this->age >= 4 && $this->age <= 12) {
            $this->prix = 8;
        } elseif ($this->age >= 60) {
            $this->prix = 12;
        } else {
            if ($this->isReduit() === true) {
                $this->prix = 10;
            } else {
                $this->prix = self::PRIX;
            }
        }

        return $this;
    }

    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set reservation.
     *
     * @param \AppBundle\Entity\Reservation $reservation
     *
     * @return Billet
     */
    public function setReservation(\AppBundle\Entity\Reservation $reservation)
    {
        $this->reservation = $reservation;

        return $this;
    }

    /**
     * Get reservation.
     *
     * @return \AppBundle\Entity\Reservation
     */
    public function getReservation()
    {
        return $this->reservation;
    }
}
