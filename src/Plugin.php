<?php

namespace hashworks\Phergie\Plugin\MovieSearch;

use Phergie\Irc\Bot\React\AbstractPlugin;
use \WyriHaximus\Phergie\Plugin\Http\Request;
use Phergie\Irc\Bot\React\EventQueueInterface as Queue;
use Phergie\Irc\Plugin\React\Command\CommandEvent as Event;

/**
 * Plugin class.
 *
 * @category Phergie
 * @package hashworks\Phergie\Plugin\MovieSearch
 */
class Plugin extends AbstractPlugin {

	protected $responseFormat = '%title% (%year%) [%imdb-id%], %imdb-rating%★ - %genre% - %plot%';

		// Used in handleCommand regex and help command
	protected $infos = array(
			'title',
			'year',
			'rated',
			'metascore',
			'release',
			'runtime',
			'genre',
			'director',
			'writer',
			'actors',
			'plot',
			'fullPlot',
			'language',
			'country',
			'awards',
			'poster',
			'dvdRelease',
			'boxOffice',
			'production',
			'website',
			'imdb-id',
			'imdb-rating',
			'imdb-votes',
			'tomato-meter',
			'tomato-image',
			'tomato-rating',
			'tomato-reviews',
			'tomato-fresh',
			'tomato-rotten',
			'tomato-consensus',
			'tomato-userMeter',
			'tomato-userRating',
			'tomato-userReviews'
	);

	public function __construct(array $config = array()) {
		if (isset($config['responseFormat']) && is_string($config['responseFormat'])) {
			$this->responseFormat = $config['responseFormat'];
		}
	}

	/**
	 *
	 *
	 * @return array
	 */
	public function getSubscribedEvents () {
		return array(
				'command.movie'             => 'handleCommand',
				'command.movie-search'      => 'handleSearchCommand',
				'command.movie.help'        => 'handleCommandHelp',
				'command.movie-search.help' => 'handleSearchCommandHelp',
		);
	}

	/**
	 * Sends reply messages.
	 *
	 * @param Event        $event
	 * @param Queue        $queue
	 * @param array|string $messages
	 */
	protected function sendReply (Event $event, Queue $queue, $messages) {
		$method = 'irc' . $event->getCommand();
		if (is_array($messages)) {
			$target = $event->getSource();
			foreach ($messages as $message) {
				$queue->$method($target, $message);
			}
		} else {
			$queue->$method($event->getSource(), $messages);
		}
	}

	public function handleSearchCommand (Event $event, Queue $queue) {
		$query = join(' ', $event->getCustomParams());

		$errorHandler = function($error) use ($event, $queue) {
			$this->sendReply($event, $queue, $error);
		};

		$this->emitter->emit('http.request', [new Request([
				'url'             => 'http://www.imdb.com/xml/find?json=1&jnr=1&tt=on&q=' . rawurlencode($query),
				'resolveCallback' => function ($data, $headers, $code) use ($event, $queue, $query, $errorHandler) {
					if ($code == "302" && isset($headers['Location'])) {
						$this->sendReply($event, $queue, $query . ' [' . explode('/', $headers['Location'])[4] . ']');
					} else {
						$data = json_decode($data, true);
						if ($data !== null) {
							if (isset($data['title_approx'])) {
								$data = $data['title_approx'];
							} elseif (isset($data['title_popular'])) {
								$data = $data['title_popular'];
							} elseif (isset($data['title_exact'])) {
								$data = $data['title_exact'];
							}

								// Remove anything except movies
							for($i = 0; $i < count($data); $i++) {
								if (isset($data[$i]['description']) && preg_match('/(?:tv series|tv docu|video game)/i', $data[$i]['description'])) {
									unset($data[$i]);
								} elseif (isset($data[$i]['title_description']) && preg_match('/(?:tv series|tv docu|video game)/i', $data[$i]['title_description'])) {
									unset($data[$i]);
								}
							}

							$data = array_slice($data, 0, 5);
							if (!empty($data)) {
								foreach ($data as $entry) {
									$this->sendReply($event, $queue, $entry['title'] . ' (http://www.imdb.com/title/' . $entry['id'] . '/)');
								}
								return;
							}
						}
						$errorHandler('Nothing found!');
					}
				},
				'rejectCallback'  => $errorHandler
		])]);
	}

	/**
	 * @param Event $event
	 * @param Queue $queue
	 */
	public function handleCommand (Event $event, Queue $queue) {
		if (preg_match("/^(?:(?<id>tt\\d{7})|(?<title>.+?)(?: (?<year>\\d{4}))?)(?: (?<info>" . join('|', array_map('preg_quote', $this->infos)) .  "))?$/", join(' ', $event->getCustomParams()), $matches)) {
			$matches = array_map('trim', $matches);

			$info = '';
			if (isset($matches['info'])) {
				$info = strtolower($matches['info']);
			}
			$sucessCallback = function (OMDB\Result $omdbResult) use ($event, $queue, $info) {
				$this->handleRequestSucess($event, $queue, $info, $omdbResult);
			};

			$errorCallback = function ($error) use ($event, $queue) {
				$this->sendReply($event, $queue, $error);
			};

			$omdbApi = new OMDB\Api($this->emitter);
			if ($info == "fullplot" || (empty($info) && strpos($this->responseFormat, '%fullPlot%') !== false)) {
				$omdbApi->requestShortPlotLength(false);
			}
			if (isset($matches['title']) && !empty($matches['title'])) {
				if (isset($matches['year']) && !empty($matches['year'])) {
					if ($omdbApi->fetchDataByTitleAndYear($matches['title'], $matches['year'], $sucessCallback, $errorCallback)) return;
				} else {
					if ($omdbApi->fetchDataByTitle($matches['title'], $sucessCallback, $errorCallback)) return;
				}
			} elseif (isset($matches['id']) && !empty($matches['id'])) {
				if ($omdbApi->fetchDataByImdbId($matches['id'], $sucessCallback, $errorCallback)) return;
			}
		}

			// Send help if regex didn't match or omdb api returned false
		$this->handleCommandHelp($event, $queue);
	}

	protected function handleRequestSucess(Event $event, Queue $queue, $info, OMDB\Result $omdbResult) {
		switch ($info) {
			case 'title':
				$this->sendReply($event, $queue, 'Title: ' . $omdbResult->getTitle());
				break;
			case 'year':
				$this->sendReply($event, $queue, 'Release year: ' . $omdbResult->getYear());
				break;
			case 'rated':
				$this->sendReply($event, $queue, 'MPAA rating: ' . $omdbResult->getRated());
				break;
			case 'metascore':
				$this->sendReply($event, $queue, 'Metascore: ' . $omdbResult->getMetascore());
				break;
			case 'release':
				$this->sendReply($event, $queue, 'Release date: ' . $omdbResult->getRelease());
				break;
			case 'runtime':
				$this->sendReply($event, $queue, 'Runtime: ' . $omdbResult->getRuntime());
				break;
			case 'genre':
				$this->sendReply($event, $queue, 'Genre: ' . $omdbResult->getGenre());
				break;
			case 'director':
				$this->sendReply($event, $queue, 'Director: ' . $omdbResult->getDirector());
				break;
			case 'writer':
				$this->sendReply($event, $queue, 'Writer: ' . $omdbResult->getWriter());
				break;
			case 'actors':
				$this->sendReply($event, $queue, 'Actors: ' . $omdbResult->getActors());
				break;
			case 'plot':
			case 'fullplot':
				$this->sendReply($event, $queue, 'Plot: ' . $omdbResult->getPlot());
				break;
			case 'language':
				$this->sendReply($event, $queue, 'Language: ' . $omdbResult->getLanguage());
				break;
			case 'country':
				$this->sendReply($event, $queue, 'Country: ' . $omdbResult->getCountry());
				break;
			case 'awards':
				$this->sendReply($event, $queue, 'Awards: ' . $omdbResult->getAwards());
				break;
			case 'poster':
				$this->sendReply($event, $queue, 'Poster: ' . $omdbResult->getPoster());
				break;
			case 'dvdrelease':
				$this->sendReply($event, $queue, 'DVD Release date: ' . $omdbResult->getDvdRelease());
				break;
			case 'boxoffice':
				$this->sendReply($event, $queue, 'Box office profit: ' . $omdbResult->getboxOffice());
				break;
			case 'production':
				$this->sendReply($event, $queue, 'Production studio: ' . $omdbResult->getProduction());
				break;
			case 'website':
				$this->sendReply($event, $queue, 'Website: ' . $omdbResult->getWebsite());
				break;
			case 'imdb-id':
				$this->sendReply($event, $queue, 'IMDB-ID: ' . $omdbResult->getImdbId());
				break;
			case 'imdb-rating':
				$this->sendReply($event, $queue, 'IMDB-Rating: ' . $omdbResult->getImdbRating());
				break;
			case 'imdb-votes':
				$this->sendReply($event, $queue, 'IMDB-Votes: ' . $omdbResult->getImdbVotes());
				break;
			case 'tomato-meter':
				$this->sendReply($event, $queue, 'Tomato Meter: ' . $omdbResult->getTomatoMeter());
				break;
			case 'tomato-image':
				$this->sendReply($event, $queue, 'Tomato Image: ' . $omdbResult->getTomatoImage());
				break;
			case 'tomato-rating':
				$this->sendReply($event, $queue, 'Tomato Rating: ' . $omdbResult->getTomatoRating());
				break;
			case 'tomato-reviews':
				$this->sendReply($event, $queue, 'Tomato review count: ' . $omdbResult->getTomatoReviews());
				break;
			case 'tomato-fresh':
				$this->sendReply($event, $queue, 'Tomato Freshness: ' . $omdbResult->getTomatoFresh());
				break;
			case 'tomato-rotten':
				$this->sendReply($event, $queue, 'Tomato Rottenness: ' . $omdbResult->getTomatoRotten());
				break;
			case 'tomato-consensus':
				$this->sendReply($event, $queue, 'Tomato Consensus: ' . $omdbResult->getTomatoConsensus());
				break;
			case 'tomato-userMeter':
				$this->sendReply($event, $queue, 'Tomato UserMeter: ' . $omdbResult->getTomatoUserMeter());
				break;
			case 'tomato-userRating':
				$this->sendReply($event, $queue, 'Tomato UserRating: ' . $omdbResult->getTomatoUserRating());
				break;
			case 'tomato-userReviews':
				$this->sendReply($event, $queue, 'Tomato user review count: ' . $omdbResult->getTomatoUserReviews());
				break;
			default:
				$replacements = $this->getReplacements($omdbResult);
				$message = str_replace(
						array_keys($replacements),
						array_values($replacements),
						$this->responseFormat
				);
				$this->sendReply($event, $queue, $message);
		}
	}

	/**
	 * Returns replacements for pattern segments based on data from a given
	 * OMDB result.
	 *
	 * @param OMDB\Result $result
	 * @return array
	 */
	protected function getReplacements(OMDB\Result $result) {
		return array(
				'%title%' => $result->getTitle(),
				'%year%' => $result->getYear(),
				'%rated%' => $result->getRated(),
				'%metascore%' => $result->getMetascore(),
				'%release%' => $result->getRelease(),
				'%runtime%' => $result->getRuntime(),
				'%genre%' => $result->getGenre(),
				'%director%' => $result->getDirector(),
				'%writer%' => $result->getWriter(),
				'%actors%' => $result->getActors(),
				'%plot%' => $result->getPlot(),
				'%fullPlot%' => $result->getPlot(),
				'%language%' => $result->getLanguage(),
				'%country%' => $result->getCountry(),
				'%awards%' => $result->getAwards(),
				'%poster%' => $result->getPoster(),
				'%dvdRelease%' => $result->getDvdRelease(),
				'%boxOffice%' => $result->getBoxOffice(),
				'%production%' => $result->getProduction(),
				'%website%' => $result->getWebsite(),
				'%imdb-id%' => $result->getImdbID(),
				'%imdb-rating%' => $result->getImdbRating(),
				'%imdb-votes%' => $result->getImdbVotes(),
				'%tomato-meter%' => $result->getTomatoMeter(),
				'%tomato-image%' => $result->getTomatoImage(),
				'%tomato-rating%' => $result->getTomatoRating(),
				'%tomato-reviews%' => $result->getTomatoReviews(),
				'%tomato-fresh%' => $result->getTomatoFresh(),
				'%tomato-rotten%' => $result->getTomatoRotten(),
				'%tomato-consensus%' => $result->getTomatoConsensus(),
				'%tomato-userMeter%' => $result->getTomatoUserMeter(),
				'%tomato-userRating%' => $result->getTomatoUserRating(),
				'%tomato-userReviews%' => $result->getTomatoUserReviews()
		);
	}

	/**
	 * Displays help information for the movie command.
	 *
	 * @param Event $event
	 * @param Queue $queue
	 */
	public function handleCommandHelp (Event $event, Queue $queue) {
		$this->sendReply($event, $queue, array(
				'Usage: movie <title [year]|imdbID> [information]',
				'Searches the OMDB for the submitted parameter and returns the requested information about it.',
				'Available informations: ' . join(', ', $this->infos),
		));
	}

	/**
	 * Displays help information for the movie-search command.
	 *
	 * @param Event $event
	 * @param Queue $queue
	 */
	public function handleSearchCommandHelp (Event $event, Queue $queue) {
		$this->sendReply($event, $queue, array(
				'Usage: movie-search <title [(year)]>',
				'Searches the IMDB for the submitted multilingual title & year and returns the results.'
		));
	}
}
