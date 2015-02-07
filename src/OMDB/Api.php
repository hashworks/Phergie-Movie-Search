<?php

namespace hashworks\Phergie\Plugin\MovieSearch\OMDB;

use \WyriHaximus\Phergie\Plugin\Http\Request;
use \Evenement\EventEmitterInterface;

class Api {

	private $plotLength = 'short';

	/**
	 * Event emitter used to register callbacks for IRC events of interest to
	 * the plugin
	 *
	 * @var EventEmitterInterface
	 */
	protected $emitter;

	/**
	 * @param EventEmitterInterface $emitter
	 */
	public function __construct (EventEmitterInterface $emitter) {
		$this->emitter = $emitter;
	}

	protected function fetchData (array $query, $successCallback, $errorCallback) {
		if (isset($query['id']) && !empty($query['id'])) {
			$parameter = "?i=" . rawurlencode($query['id']);
		} elseif (isset($query['title']) && !empty($query['title'])) {
			$parameter = "?t=" . rawurlencode($query['title']);
			if (isset($query['year']) && !empty($query['year'])) {
				$parameter .= "&y=" . rawurlencode($query['year']);
			}
		} else {
			return false;
		}
		$this->emitter->emit('http.request', [new Request([
				'url'             => 'http://www.omdbapi.com/' . $parameter . '&r=json&v=1&plot=' . $this->plotLength,
				'resolveCallback' => function ($data) use ($successCallback, $errorCallback) {
					$this->handleData($data, $successCallback, $errorCallback);
				},
				'rejectCallback'  => $errorCallback
		])]);

		return true;
	}

	protected function handleData ($data, $successCallback, $errorCallback) {
		$data = json_decode($data, true);
		if ($data !== null) {
			if (isset($data['Response']) && $data['Response'] == "False") {
				if (!isset($data['Error'])) {
					$data['Error'] = "Got no API response. Sorry!";
				}
				$errorCallback($data['Error']);
			} else {
				$result = new Result();

				if (isset($data['Title'])) $result->setTitle($data['Title']);
				if (isset($data['Year'])) $result->setYear($data['Year']);
				if (isset($data['Rated'])) $result->setRated($data['Rated']);
				if (isset($data['Released'])) $result->setRelease($data['Released']);
				if (isset($data['Runtime'])) $result->setRuntime($data['Runtime']);
				if (isset($data['Genre'])) $result->setGenre($data['Genre']);
				if (isset($data['Director'])) $result->setDirector($data['Director']);
				if (isset($data['Writer'])) $result->setWriter($data['Writer']);
				if (isset($data['Actors'])) $result->setActors($data['Actors']);
				if (isset($data['Plot'])) $result->setPlot($data['Plot']);
				if (isset($data['Language'])) $result->setLanguage($data['Language']);
				if (isset($data['Country'])) $result->setCountry($data['Country']);
				if (isset($data['Awards'])) $result->setAwards($data['Awards']);
				if (isset($data['Poster'])) $result->setPoster($data['Poster']);
				if (isset($data['Metascore'])) $result->setMetascore($data['Metascore']);
				if (isset($data['imdbRating'])) $result->setimdbRating($data['imdbRating']);
				if (isset($data['imdbVotes'])) $result->setimdbVotes($data['imdbVotes']);
				if (isset($data['imdbID'])) $result->setimdbID($data['imdbID']);
				if (isset($data['Type'])) $result->setType($data['Type']);
				if (isset($data['tomatoMeter'])) $result->setTomatoMeter($data['tomatoMeter']);
				if (isset($data['tomatoImage'])) $result->setTomatoImage($data['tomatoImage']);
				if (isset($data['tomatoRating'])) $result->setTomatoRating($data['tomatoRating']);
				if (isset($data['tomatoReviews'])) $result->setTomatoReviews($data['tomatoReviews']);
				if (isset($data['tomatoFresh'])) $result->setTomatoFresh($data['tomatoFresh']);
				if (isset($data['tomatoRotten'])) $result->setTomatoRotten($data['tomatoRotten']);
				if (isset($data['tomatoConsensus'])) $result->setTomatoConsensus($data['tomatoConsensus']);
				if (isset($data['tomatoUserMeter'])) $result->setTomatoUserMeter($data['tomatoUserMeter']);
				if (isset($data['tomatoUserRating'])) $result->setTomatoUserRating($data['tomatoUserRating']);
				if (isset($data['tomatoUserReviews'])) $result->setTomatoUserReviews($data['tomatoUserReviews']);
				if (isset($data['DVD'])) $result->setDvdRelease($data['DVD']);
				if (isset($data['BoxOffice'])) $result->setBoxOffice($data['BoxOffice']);
				if (isset($data['Production'])) $result->setProduction($data['Production']);
				if (isset($data['Website'])) $result->setWebsite($data['Website']);

				$successCallback($result);
			}
		} else {
			$errorCallback('Failed to handle API response. Sorry!');
		}
	}

	public function fetchDataByTitle ($title, $successCallback, $errorCallback) {
		return $this->fetchData(array('title' => $title), $successCallback, $errorCallback);
	}

	public function fetchDataByTitleAndYear ($title, $year, $successCallback, $errorCallback) {
		return $this->fetchData(array('title' => $title, 'year' => $year), $successCallback, $errorCallback);
	}

	public function fetchDataByImdbId ($id, $successCallback, $errorCallback) {
		return $this->fetchData(array('id' => $id), $successCallback, $errorCallback);
	}

	/**
	 * Should we request a short plot?
	 *
	 * @param $requestShortPlotLength
	 */
	public function requestShortPlotLength ($requestShortPlotLength) {
		if ($requestShortPlotLength) {
			$this->plotLength = 'short';
		} else {
			$this->plotLength = 'full';
		}
	}
}