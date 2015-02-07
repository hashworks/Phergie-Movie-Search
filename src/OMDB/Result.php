<?php

namespace hashworks\Phergie\Plugin\MovieSearch\OMDB;

class Result {
	private $title = 'Unknown Title';
	private $year = 'Unknown release year';
	private $rated = 'Unknown MPAA rating';
	private $release = 'Unknown release date';
	private $runtime = 'Unknown runtime';
	private $genre = 'Unknown genre';
	private $director = 'Unknown director';
	private $writer = 'Unknown writer';
	private $actors = 'Unknown actor';
	private $plot = 'Unknown plot';
	private $language = 'Unknown language';
	private $country = 'Unknown country';
	private $awards = 'Unknown awards';
	private $poster = 'Unknown poster';
	private $metascore = 'Unknown metascore';
	private $dvdRelease = 'Unknown DVD release date';
	private $boxOffice = 'Unknown box office profit';
	private $production = 'Unknown production studio';
	private $website = 'Unknown website';
	private $type = 'Unknown type';
	private $imdbRating = 'Unknown IMDB rating';
	private $imdbVotes = 'Unknown IMDB vote count';
	private $imdbID = 'Unknown IMDB ID';
	private $tomatoMeter = 'Unknown Tomato Meter';
	private $tomatoImage = 'Unknown Tomato Image';
	private $tomatoRating = 'Unknown Tomato Rating';
	private $tomatoReviews = 'Unknown Tomato Reviews';
	private $tomatoFresh = 'Unknown Tomato Freshness';
	private $tomatoRotten = 'Unknown Tomato Rottenness';
	private $tomatoConsensus = 'Unknown Tomato Consensus';
	private $tomatoUserMeter = 'Unknown Tomato UserMeter';
	private $tomatoUserRating = 'Unknown Tomato UserRating';
	private $tomatoUserReviews = 'Unknown Tomato user review count';

	/**
	 * @return string
	 */
	public function getTitle () {
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle ($title) {
		$this->title = trim($title);
	}

	/**
	 * @return string
	 */
	public function getYear () {
		return $this->year;
	}

	/**
	 * @param string $year
	 */
	public function setYear ($year) {
		$this->year = trim($year);
	}

	/**
	 * @return string
	 */
	public function getRated () {
		return $this->rated;
	}

	/**
	 * @param string $rated
	 */
	public function setRated ($rated) {
		$this->rated = trim($rated);
	}

	/**
	 * @return string
	 */
	public function getRelease () {
		return $this->release;
	}

	/**
	 * @param string $release
	 */
	public function setRelease ($release) {
		$this->release = trim($release);
	}

	/**
	 * @return string
	 */
	public function getRuntime () {
		return $this->runtime;
	}

	/**
	 * @param string $runtime
	 */
	public function setRuntime ($runtime) {
		$this->runtime = trim($runtime);
	}

	/**
	 * @return string
	 */
	public function getGenre () {
		return $this->genre;
	}

	/**
	 * @param string $genre
	 */
	public function setGenre ($genre) {
		$this->genre = trim($genre);
	}

	/**
	 * @return string
	 */
	public function getDirector () {
		return $this->director;
	}

	/**
	 * @param string $director
	 */
	public function setDirector ($director) {
		$this->director = trim($director);
	}

	/**
	 * @return string
	 */
	public function getWriter () {
		return $this->writer;
	}

	/**
	 * @param string $writer
	 */
	public function setWriter ($writer) {
		$this->writer = trim($writer);
	}

	/**
	 * @return string
	 */
	public function getActors () {
		return $this->actors;
	}

	/**
	 * @param string $actors
	 */
	public function setActors ($actors) {
		$this->actors = trim($actors);
	}

	/**
	 * @return string
	 */
	public function getPlot () {
		return $this->plot;
	}

	/**
	 * @param string $plot
	 */
	public function setPlot ($plot) {
		$this->plot = trim($plot);
	}

	/**
	 * @return string
	 */
	public function getLanguage () {
		return $this->language;
	}

	/**
	 * @param string $language
	 */
	public function setLanguage ($language) {
		$this->language = trim($language);
	}

	/**
	 * @return string
	 */
	public function getCountry () {
		return $this->country;
	}

	/**
	 * @param string $country
	 */
	public function setCountry ($country) {
		$this->country = trim($country);
	}

	/**
	 * @return string
	 */
	public function getAwards () {
		return $this->awards;
	}

	/**
	 * @param string $awards
	 */
	public function setAwards ($awards) {
		$this->awards = trim($awards);
	}

	/**
	 * @return string
	 */
	public function getPoster () {
		return $this->poster;
	}

	/**
	 * @param string $poster
	 */
	public function setPoster ($poster) {
		$this->poster = trim($poster);
	}

	/**
	 * @return string
	 */
	public function getMetascore () {
		return $this->metascore;
	}

	/**
	 * @param string $metascore
	 */
	public function setMetascore ($metascore) {
		$this->metascore = trim($metascore);
	}

	/**
	 * @return string
	 */
	public function getDvdRelease () {
		return $this->dvdRelease;
	}

	/**
	 * @param string $dvdRelease
	 */
	public function setDvdRelease ($dvdRelease) {
		$this->dvdRelease = trim($dvdRelease);
	}

	/**
	 * @return string
	 */
	public function getBoxOffice () {
		return $this->boxOffice;
	}

	/**
	 * @param string $boxOffice
	 */
	public function setBoxOffice ($boxOffice) {
		$this->boxOffice = trim($boxOffice);
	}

	/**
	 * @return string
	 */
	public function getProduction () {
		return $this->production;
	}

	/**
	 * @param string $production
	 */
	public function setProduction ($production) {
		$this->production = trim($production);
	}

	/**
	 * @return string
	 */
	public function getWebsite () {
		return $this->website;
	}

	/**
	 * @param string $website
	 */
	public function setWebsite ($website) {
		$this->website = trim($website);
	}

	/**
	 * @return string
	 */
	public function getType () {
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType ($type) {
		$this->type = trim($type);
	}

	/**
	 * @return string
	 */
	public function getImdbRating () {
		return $this->imdbRating;
	}

	/**
	 * @param string $imdbRating
	 */
	public function setImdbRating ($imdbRating) {
		$this->imdbRating = trim($imdbRating);
	}

	/**
	 * @return string
	 */
	public function getImdbVotes () {
		return $this->imdbVotes;
	}

	/**
	 * @param string $imdbVotes
	 */
	public function setImdbVotes ($imdbVotes) {
		$this->imdbVotes = trim($imdbVotes);
	}

	/**
	 * @return string
	 */
	public function getImdbID () {
		return $this->imdbID;
	}

	/**
	 * @param string $imdbID
	 */
	public function setImdbID ($imdbID) {
		$this->imdbID = trim($imdbID);
	}

	/**
	 * @return string
	 */
	public function getTomatoMeter () {
		return $this->tomatoMeter;
	}

	/**
	 * @param string $tomatoMeter
	 */
	public function setTomatoMeter ($tomatoMeter) {
		$this->tomatoMeter = trim($tomatoMeter);
	}

	/**
	 * @return string
	 */
	public function getTomatoImage () {
		return $this->tomatoImage;
	}

	/**
	 * @param string $tomatoImage
	 */
	public function setTomatoImage ($tomatoImage) {
		$this->tomatoImage = trim($tomatoImage);
	}

	/**
	 * @return string
	 */
	public function getTomatoRating () {
		return $this->tomatoRating;
	}

	/**
	 * @param string $tomatoRating
	 */
	public function setTomatoRating ($tomatoRating) {
		$this->tomatoRating = trim($tomatoRating);
	}

	/**
	 * @return string
	 */
	public function getTomatoReviews () {
		return $this->tomatoReviews;
	}

	/**
	 * @param string $tomatoReviews
	 */
	public function setTomatoReviews ($tomatoReviews) {
		$this->tomatoReviews = trim($tomatoReviews);
	}

	/**
	 * @return string
	 */
	public function getTomatoFresh () {
		return $this->tomatoFresh;
	}

	/**
	 * @param string $tomatoFresh
	 */
	public function setTomatoFresh ($tomatoFresh) {
		$this->tomatoFresh = trim($tomatoFresh);
	}

	/**
	 * @return string
	 */
	public function getTomatoRotten () {
		return $this->tomatoRotten;
	}

	/**
	 * @param string $tomatoRotten
	 */
	public function setTomatoRotten ($tomatoRotten) {
		$this->tomatoRotten = trim($tomatoRotten);
	}

	/**
	 * @return string
	 */
	public function getTomatoConsensus () {
		return $this->tomatoConsensus;
	}

	/**
	 * @param string $tomatoConsensus
	 */
	public function setTomatoConsensus ($tomatoConsensus) {
		$this->tomatoConsensus = trim($tomatoConsensus);
	}

	/**
	 * @return string
	 */
	public function getTomatoUserMeter () {
		return $this->tomatoUserMeter;
	}

	/**
	 * @param string $tomatoUserMeter
	 */
	public function setTomatoUserMeter ($tomatoUserMeter) {
		$this->tomatoUserMeter = trim($tomatoUserMeter);
	}

	/**
	 * @return string
	 */
	public function getTomatoUserRating () {
		return $this->tomatoUserRating;
	}

	/**
	 * @param string $tomatoUserRating
	 */
	public function setTomatoUserRating ($tomatoUserRating) {
		$this->tomatoUserRating = trim($tomatoUserRating);
	}

	/**
	 * @return string
	 */
	public function getTomatoUserReviews () {
		return $this->tomatoUserReviews;
	}

	/**
	 * @param string $tomatoUserReviews
	 */
	public function setTomatoUserReviews ($tomatoUserReviews) {
		$this->tomatoUserReviews = trim($tomatoUserReviews);
	}

}