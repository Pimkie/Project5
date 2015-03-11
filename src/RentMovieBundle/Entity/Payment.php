<?php

namespace RentMovieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table(name="payment")
 * @ORM\Entity
 */
class Payment
{
    /**
     * @var string
     *
     * @ORM\Column(name="form", type="string", length=20, nullable=true)
     */
    private $form;

    /**
     * @var integer
     *
     * @ORM\Column(name="term", type="integer", nullable=true)
     */
    private $term;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="paymentdate", type="date", nullable=true)
     */
    private $paymentdate;

    /**
     * @var integer
     *
     * @ORM\Column(name="paymentid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="payment_paymentid_seq", allocationSize=1, initialValue=1)
     */
    private $paymentid;



    /**
     * Set form
     *
     * @param string $form
     * @return Payment
     */
    public function setForm($form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form
     *
     * @return string 
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set term
     *
     * @param integer $term
     * @return Payment
     */
    public function setTerm($term)
    {
        $this->term = $term;

        return $this;
    }

    /**
     * Get term
     *
     * @return integer 
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * Set paymentdate
     *
     * @param \DateTime $paymentdate
     * @return Payment
     */
    public function setPaymentdate($paymentdate)
    {
        $this->paymentdate = $paymentdate;

        return $this;
    }

    /**
     * Get paymentdate
     *
     * @return \DateTime 
     */
    public function getPaymentdate()
    {
        return $this->paymentdate;
    }

    /**
     * Get paymentid
     *
     * @return integer 
     */
    public function getPaymentid()
    {
        return $this->paymentid;
    }
}
