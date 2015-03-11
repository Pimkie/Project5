<?php

namespace RentMovieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Movies
 *
 * @ORM\Table(name="movies")
 * @ORM\Entity
 */
class Movies
{
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=30, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="genre", type="string", length=20, nullable=true)
     */
    private $genre;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="actors", type="string", length=100, nullable=true)
     */
    private $actors;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="ratingborrowed", type="integer", nullable=true)
     */
    private $ratingborrowed;

    /**
     * @var string
     *
     * @ORM\Column(name="cover", type="string", length=50, nullable=true)
     */
    private $cover;

    /**
     * @var string
     *
     * @ORM\Column(name="trailer", type="string", length=50, nullable=true)
     */
    private $trailer;

    /**
     * @var float
     *
     * @ORM\Column(name="borrowingprice", type="float", precision=10, scale=0, nullable=true)
     */
    private $borrowingprice;

    /**
     * @var integer
     *
     * @ORM\Column(name="movieid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="movies_movieid_seq", allocationSize=1, initialValue=1)
     */
    private $movieid;



    /**
     * Set title
     *
     * @param string $title
     * @return Movies
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set genre
     *
     * @param string $genre
     * @return Movies
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get genre
     *
     * @return string 
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Movies
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set actors
     *
     * @param string $actors
     * @return Movies
     */
    public function setActors($actors)
    {
        $this->actors = $actors;

        return $this;
    }

    /**
     * Get actors
     *
     * @return string 
     */
    public function getActors()
    {
        return $this->actors;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Movies
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set ratingborrowed
     *
     * @param integer $ratingborrowed
     * @return Movies
     */
    public function setRatingborrowed($ratingborrowed)
    {
        $this->ratingborrowed = $ratingborrowed;

        return $this;
    }

    /**
     * Get ratingborrowed
     *
     * @return integer 
     */
    public function getRatingborrowed()
    {
        return $this->ratingborrowed;
    }

    /**
     * Set cover
     *
     * @param string $cover
     * @return Movies
     */
    public function setCover($cover)
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * Get cover
     *
     * @return string 
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * Set trailer
     *
     * @param string $trailer
     * @return Movies
     */
    public function setTrailer($trailer)
    {
        $this->trailer = $trailer;

        return $this;
    }

    /**
     * Get trailer
     *
     * @return string 
     */
    public function getTrailer()
    {
        return $this->trailer;
    }

    /**
     * Set borrowingprice
     *
     * @param float $borrowingprice
     * @return Movies
     */
    public function setBorrowingprice($borrowingprice)
    {
        $this->borrowingprice = $borrowingprice;

        return $this;
    }

    /**
     * Get borrowingprice
     *
     * @return float 
     */
    public function getBorrowingprice()
    {
        return $this->borrowingprice;
    }

    /**
     * Get movieid
     *
     * @return integer 
     */
    public function getMovieid()
    {
        return $this->movieid;
    }
}
