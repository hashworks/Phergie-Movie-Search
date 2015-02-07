<?php

class imdbPlugin extends basePlugin {

	private $disabled;

	/**
	 * Called when plugins are loaded
	 *
	 * @param mixed[]	$config
	 * @param resource 	$socket
	 **/
	public function __construct($config, $socket) {
		parent::__construct($config, $socket);
		$this->disabled = false;
		if (!ini_get('allow_url_fopen')) {
			try {
				ini_set('allow_url_fopen', '1');
			} catch (Exception $e) {
				logMsg("Unable to enable allow_url_fopen, disabling imdbPlugin.");
				$this->disabled = true;
			}
		}
	}

	/**
	 * @return array
	 */
	public function help() {
		if ($this->disabled === true) {
			return array();
		}
		return array(
				array(
						'command'     => 'imdb <title|imdbID> [year]',
						'description' => 'Searches the IMDB and responds with title, year, imdbID, rating, genre, link & plot.'
				),
				array(
						'command'     => 'imdb-<info> <title|imdbID> [year]',
						'description' => 'Searches the IMDB for a specific info. See all available informations at http://is.gd/0Y5mQn'
				)
		);
	}

	/**
	 * Called when messages are posted on the channel
	 * the bot are in, or when somebody talks to it
	 *
	 * @param string $from
	 * @param string $channel
	 * @param string $msg
	 */
	public function onMessage($from, $channel, $msg) {
		if ($this->disabled === true) {
			return;
		}
		if (preg_match('/' . preg_quote($this->config['trigger']) . 'imdb(?:-([a-z]+)){0,1} (.*)/i', $msg, $matches)) {
			$string = $this->omdbApiRequest($matches[2], $matches[1]);
			if ($string === false || empty($string)) {
				$string = "Failed to fetch data from omdbapi...";
			}
			$this->sendMessage($channel, $string, $from);
		}
	}

	/**
	 * @param string $query
	 * @param string $info = ""
	 *
	 * @return boolean|string
	 */
	private function omdbApiRequest($query, $info = "") {
		$query = trim($query);
		$info  = trim(strtolower($info));
		if (!empty($query)) {
			if (preg_match('/tt[0-9]+/', $query)) {
				$data = @file_get_contents("http://www.omdbapi.com/?i=" . $query . "&plot=short&r=json&v=1");
			} else {
				$parts = explode(' ', $query);
				if (preg_match('/[0-9]{4}/', $parts[count($parts)-1])) {
					$year = $parts[count($parts)-1];
					unset($parts[count($parts)-1]);
					$query = implode(' ', $parts);
					$data = @file_get_contents("http://www.omdbapi.com/?t=" . rawurlencode($query) . "&y=" . $year . "&plot=short&r=json&v=1");
				} else {
					$data = @file_get_contents("http://www.omdbapi.com/?t=" . rawurlencode($query) . "&plot=short&r=json&v=1");
				}
			}
			if (!empty($data) && ($data = json_decode($data, true)) !== NULL) {
				$data = array_change_key_case($data, CASE_LOWER);
				if (isset($data['error'])) {
					if (!empty($data['error'])) {
						return $data['error'];
					}
				} else {
					if (empty($info)) {
						// Title (Year) [imdbID], Rating★ - Genre - Link - Plot
						if (isset($data['title'])) {
							$string = $data['title'];
						} else {
							$string = "Unknown title";
						}
						if (isset($data['year']))       $string = $string . ' (' . $data['year'] . ')';
						if (isset($data['imdbid']))     $string = $string . ' [' . $data['imdbid'] . ']';
						if (isset($data['imdbrating'])) $string = $string . ', ' . $data['imdbrating'] . '★';
						if (isset($data['genre']))      $string = $string . ' - ' . $data['genre'];
						if (isset($data['imdbid']))     $string = $string . ' - ' . $this->getShortUrl('http://www.imdb.com/title/' . $data['imdbid']);
						if (isset($data['plot']))       $string = $string . ' - ' . $data['plot'];
						$string = trim($string);
						if (!empty($string)) {
							return $string;
						}
					} elseif ($info == "rating") {
						if (isset($data['imdbrating']) && isset($data['imdbvotes'])) {
							return "Rating: " . $data['imdbrating'] . " with " . $data['imdbvotes'] . " votes.";
						}
					} elseif ($info == "id") {
						if (isset($data['imdbid'])) {
							return "IMDB-ID: " . $data['imdbid'] . ".";
						}
					} else {
						if (isset($data[$info])) {
							return ucwords($info) . ': ' . $data[$info] . '.';
						} else {
							return "No " . ucwords($info) . " found!";
						}
					}
				}
			}
		}
		return false;
	}

	/**
	 * @param string $longUrl
	 *
	 * @return string
	 */
	private function getShortUrl($longUrl) {
		$shortUrl = trim(@file_get_contents("http://is.gd/create.php?format=simple&url=" . $longUrl));
		if (!empty($shortUrl) && strpos($shortUrl, "http") !== false) {
			return $shortUrl;
		}
		return $longUrl;
	}

	/**
	 * @param string $to
	 * @param string $msg
	 * @param string|array $highlight = NULL
	 */
	private function sendMessage($to, $msg, $highlight = NULL) {
		if ($highlight !== NULL) {
			if (is_array($highlight)) {
				$highlight = join(", ", $highlight);
			}
			if ($highlight !== $to) {
				$msg = $highlight . ": " . $msg;
			}
		}
		sendMessage($this->socket, $to, $msg);
	}
}