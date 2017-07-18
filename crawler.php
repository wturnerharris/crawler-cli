<?php 

if ( php_sapi_name() !== "cli" ) 
{
    exit(1);
}

$url = @getopt("l:");

if ( empty( $url['l'] ) ) 
{
    fwrite(STDERR, "==================\nMissing argument! \n==================
    Usage: php crawl.php -url=<URI>
    \n");
    exit(1);
}
else
{
	$url = $url['l'];
}

require __DIR__ . '/vendor/autoload.php';

class Colors {
	private $foreground_colors = array();
	private $background_colors = array();

	public function __construct() {
		// Set up shell colors
		$this->foreground_colors['black'] = '0;30';
		$this->foreground_colors['dark_gray'] = '1;30';
		$this->foreground_colors['blue'] = '0;34';
		$this->foreground_colors['light_blue'] = '1;34';
		$this->foreground_colors['green'] = '0;32';
		$this->foreground_colors['light_green'] = '1;32';
		$this->foreground_colors['cyan'] = '0;36';
		$this->foreground_colors['light_cyan'] = '1;36';
		$this->foreground_colors['red'] = '0;31';
		$this->foreground_colors['light_red'] = '1;31';
		$this->foreground_colors['purple'] = '0;35';
		$this->foreground_colors['light_purple'] = '1;35';
		$this->foreground_colors['brown'] = '0;33';
		$this->foreground_colors['yellow'] = '1;33';
		$this->foreground_colors['light_gray'] = '0;37';
		$this->foreground_colors['white'] = '1;37';

		$this->background_colors['black'] = '40';
		$this->background_colors['red'] = '41';
		$this->background_colors['green'] = '42';
		$this->background_colors['yellow'] = '43';
		$this->background_colors['blue'] = '44';
		$this->background_colors['magenta'] = '45';
		$this->background_colors['cyan'] = '46';
		$this->background_colors['light_gray'] = '47';
	}

	// Returns colored string
	public function getColoredString($string, $foreground_color = null, $background_color = null) {
		$colored_string = "";

		// Check if given foreground color found
		if (isset($this->foreground_colors[$foreground_color])) {
			$colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
		}
		// Check if given background color found
		if (isset($this->background_colors[$background_color])) {
			$colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
		}

		// Add string and end coloring
		$colored_string .=  $string . "\033[0m";

		return $colored_string;
	}

	// Returns all foreground color names
	public function getForegroundColors() {
		return array_keys($this->foreground_colors);
	}

	// Returns all background color names
	public function getBackgroundColors() {
		return array_keys($this->background_colors);
	}
}

/**
$i = 0;
echo 'Initial URL,Final URL,HTTP Code,Redirected,Check Time'."\n";
foreach( $input_urls as $request_url) :
    $colors = new Colors();
    $i++;

    $data = get_data($request_url);
    echo "\n";
    sleep(0.25);
endforeach;
*/

class CustomCrawlObserver implements \Spatie\Crawler\CrawlObserver
{

	/**
	 * Called when the crawler will crawl the given url.
	 *
	 * @param \Spatie\Crawler\Url $url
	 */
	public function willCrawl(Spatie\Crawler\Url $url)
	{
		echo urldecode($url).",";
	}

	/**
	 * Called when the crawler has crawled the given url.
	 *
	 * @param \Spatie\Crawler\Url $url
	 * @param \Psr\Http\Message\ResponseInterface $response
	 * @param \Spatie\Crawler\Url $foundOn
	 */
	public function hasBeenCrawled(Spatie\Crawler\Url $url, $response, Spatie\Crawler\Url $foundOnUrl = NULL)
	{
		$statusCode = $response->getStatusCode();
		$headers = $response->getHeaders();
		echo "$statusCode\n";
	}

	/**
	 * Called when the crawl has ended.
	 */
	public function finishedCrawling()
	{
		echo "Crawl Complete!\n";
	}

}

echo "URL,HTTP Response Code\n";

\Spatie\Crawler\Crawler::create()
	->setCrawlProfile( new \Spatie\Crawler\CrawlInternalUrls( $url ) )
    ->setCrawlObserver( new CustomCrawlObserver )
    ->setConcurrency(1)
    ->startCrawling( $url );
