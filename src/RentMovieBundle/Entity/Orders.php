<?php

namespace RentMovieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Orders
 *
 * @ORM\Table(name="orders", indexes={@ORM\Index(name="IDX_E52FFDEE7F98CD1C", columns={"clientid"}), @ORM\Index(name="IDX_E52FFDEEEEF9E56", columns={"movieid"}), @ORM\Index(name="IDX_E52FFDEEE042330C", columns={"paymentid"})})
 * @ORM\Entity
 */
class Orders
{
    /**
     * @var integer
     *
     * @ORM\Column(name="orderid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="orders_orderid_seq", allocationSize=1, initialValue=1)
     */
    private $orderid;

    /**
     * @var \RentMovieBundle\Entity\Payment
     *
     * @ORM\ManyToOne(targetEntity="RentMovieBundle\Entity\Payment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="paymentid", referencedColumnName="paymentid")
     * })
     */
    private $paymentid;

    /**
     * @var \RentMovieBundle\Entity\Movies
     *
     * @ORM\ManyToOne(targetEntity="RentMovieBundle\Entity\Movies")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="movieid", referencedColumnName="movieid")
     * })
     */
    private $movieid;

    /**
     * @var \RentMovieBundle\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="RentMovieBundle\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="clientid", referencedColumnName="clientid")
     * })
     */
    private $clientid;



    /**
     * Get orderid
     *
     * @return integer 
     */
    public function getOrderid()
    {
        return $this->orderid;
    }

    /**
     * Set paymentid
     *
     * @param \RentMovieBundle\Entity\Payment $paymentid
     * @return Orders
     */
    public function setPaymentid(\RentMovieBundle\Entity\Payment $paymentid = null)
    {
        $this->paymentid = $paymentid;

        return $this;
    }

    /**
     * Get paymentid
     *
     * @return \RentMovieBundle\Entity\Payment 
     */
    public function getPaymentid()
    {
        return $this->paymentid;
    }

    /**
     * Set movieid
     *
     * @param \RentMovieBundle\Entity\Movies $movieid
     * @return Orders
     */
    public function setMovieid(\RentMovieBundle\Entity\Movies $movieid = null)
    {
        $this->movieid = $movieid;

        return $this;
    }

    /**
     * Get movieid
     *
     * @return \RentMovieBundle\Entity\Movies 
     */
    public function getMovieid()
    {
        return $this->movieid;
    }

    /**
     * Set clientid
     *
     * @param \RentMovieBundle\Entity\Client $clientid
     * @return Orders
     */
    public function setClientid(\RentMovieBundle\Entity\Client $clientid = null)
    {
        $this->clientid = $clientid;

        return $this;
    }

    /**
     * Get clientid
     *
     * @return \RentMovieBundle\Entity\Client 
     */
    public function getClientid()
    {
        return $this->clientid;
    }
}
