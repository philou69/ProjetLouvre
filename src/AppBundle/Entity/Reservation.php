<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reservation.
 *
 * @ORM\Table(name="reservation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReservationRepository")
 * @AppAssert\Reservation
 * @AppAssert\FullDateReservation
 */
class Reservation
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Datetime
     * @AppAssert\CloseDate
     * @ORM\Column(type="date")
     */
    protected $dateReservation;

    /**
     * @ORM\Column(name="email", type="text")
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Billet", mappedBy="reservation", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $billets;

    /**
     * @ORM\Column(name="demi_journee", type="boolean")
     */
    private $demiJournee;

    /**
     * @ORM\Column(name="prix", type="decimal", precision=2)
     */
    private $prix;

    /**
     * @ORM\Column(name="payer", type="boolean")
     */
    private $payer;

    /**
     * @ORM\Column(name="code_reservation", type="string", unique= true)
     */
    private $codeReservation;



    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->billets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->demiJournee = false;
        $this->payer = false;
        $this->codeReservation = uniqid('CODE_RESA');
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
     * Set dateReservation.
     *
     * @param \DateTime $dateReservation
     *
     * @return Reservation
     */
    public function setDateReservation($dateReservation)
    {
        $this->dateReservation = \DateTime::createFromFormat('d-m-Y', $dateReservation);

        return $this;
    }

    /**
     * Get dateReservation.
     *
     * @return \DateTime
     */
    public function getDateReservation()
    {
        return $this->dateReservation;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Reservation
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function addDemiJournee()
    {
        $this->demiJournee = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDemiJournee()
    {
        return $this->demiJournee;
    }

    /**
     * Set prix.
     *
     * @param int $prix
     *
     * @return Reservation
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix.
     *
     * @return int
     */
    public function getPrix()
    {
        $prix = 0;
        if ($this->memeNom() === true) {
            if ($this->isDemiJournee() === true) {
                $prix = 17.5;
            } else {
                $prix = 35;
            }
        } else {
            foreach ($this->billets as $billet) {
                if ($this->isDemiJournee() === true) {
                    $prix = $prix + ($billet->getPrix() / 2);
                } else {
                    $prix = $prix + $billet->getPrix();
                }
            }
        }

        return $prix;
    }

    public function setBillets($billets)
    {
        foreach ($billets as $billet) {
            $billet->setReservation($this);
        }

        $this->billets = $billets;

        return $this;
    }
    /**
     * Add billet.
     *
     * @param \AppBundle\Entity\Billet $billet
     *
     * @return Reservation
     */
    public function addBillet(\AppBundle\Entity\Billet $billet)
    {
        $billet->setReservation($this);
        $this->billets[] = $billet;

        return $this;
    }

    /**
     * Remove billet.
     *
     * @param \AppBundle\Entity\Billet $billet
     */
    public function removeBillet(\AppBundle\Entity\Billet $billet)
    {
        $this->billets->removeElement($billet);
    }

    /**
     * Get billets.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBillets()
    {
        return $this->billets;
    }

    public function memeNom()
    {
        $nom = null;
        $memeNom = 0;
        foreach ($this->billets as $key => $billet) {
            if ($key === 0) {
                $nom = $billet->getNom();
            }
            if ($billet->getNom() == $nom) {
                ++$memeNom;
            }
        }

        return $memeNom == 4;
    }

    public function addPayement()
    {
        $this->payer = true;

        return $this;
    }

    public function isPayer()
    {
        return $this->payer;
    }

    /**
     * Set demiJournee.
     *
     * @param bool $demiJournee
     *
     * @return Reservation
     */
    public function setDemiJournee($demiJournee)
    {
        $this->demiJournee = $demiJournee;

        return $this;
    }

    /**
     * Get demiJournee.
     *
     * @return bool
     */
    public function getDemiJournee()
    {
        return $this->demiJournee;
    }

    /**
     * Set payer.
     *
     * @param bool $payer
     *
     * @return Reservation
     */
    public function setPayer($payer)
    {
        $this->payer = $payer;

        return $this;
    }

    /**
     * Get payer.
     *
     * @return bool
     */
    public function getPayer()
    {
        return $this->payer;
    }



    public function memeJour()
    {
        $nowDate = date('Y-m-d');
        $nowHeure = date('H');

        if ($this->getDateReservation()->format('Y-m-d') == $nowDate && $nowHeure > 14) {
            $this->addDemiJournee(true);
        }
    }

    public function calculPrix()
    {
        if ($this->memeNom() === true) {
            if ($this->isDemiJournee() === true) {
                $this->setPrix(17.5);
            } else {
                $this->setPrix(35);
            }
        } else {
            $prix = 0;
            foreach ($this->getBillets() as $billet) {
                if ($this->isDemiJournee() === true) {
                    $prix = $prix + ($billet->getPrix() / 2);
                } else {
                    $prix = $prix + $billet->getPrix();
                }
            }
            $this->setPrix($prix);
        }
    }


    /**
     * @return mixed
     */
    public function getCodeReservation()
    {
        return $this->codeReservation;
    }

    /**
     * @param mixed $codeReservation
     */
    public function setCodeReservation($codeReservation)
    {
        $this->codeReservation = $codeReservation;
    }

    public function getPriceFormated()
    {
        return number_format($this->getPrix(),"2",'','');
    }


}
