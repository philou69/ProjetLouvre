<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompteReservation
 *
 * @ORM\Table(name="compte_reservation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompteReservationRepository")
 */
class CompteReservation
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
   private $id;
  /**
   * @ORM\Column(name="date_reservation", type="date", unique=true)
   */
  private $dateReservation;

  /**
   * @ORM\Column(name="total", type="integer")
   */
  private $total;

  public function __construct()
  {
    $this->total = 0;
  }

    /**
     * Set dateReservation
     *
     * @param \DateTime $dateReservation
     *
     * @return CompteReservation
     */
    public function setDateReservation($dateReservation)
    {
        $this->dateReservation = $dateReservation;

        return $this;
    }

    /**
     * Get dateReservation
     *
     * @return \DateTime
     */
    public function getDateReservation()
    {
        return $this->dateReservation;
    }

    /**
     * Set total
     *
     * @param integer $total
     *
     * @return CompteReservation
     */
    public function setTotal($total)
    {
        $this->total += $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
