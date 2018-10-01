<?php 
class Liedje {
		private $titel;
		private $artiest;

		function __construct($t, $a) {
			$this->titel = $t;
			$this->artiest = $a;
		}

    /**
     * Gets the value of titel.
     *
     * @return mixed
     */
    public function getTitel()
    {
        return $this->titel;
    }

    /**
     * Sets the value of titel.
     *
     * @param mixed $titel the titel
     *
     * @return self
     */
    public function setTitel($titel)
    {
        $this->titel = $titel;

        return $this;
    }

    /**
     * Gets the value of artiest.
     *
     * @return mixed
     */
    public function getArtiest()
    {
        return $this->artiest;
    }

    /**
     * Sets the value of artiest.
     *
     * @param mixed $artiest the artiest
     *
     * @return self
     */
    public function setArtiest($artiest)
    {
        $this->artiest = $artiest;

        return $this;
    }
}

 ?>