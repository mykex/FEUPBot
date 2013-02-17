<?php

require_once('twitteroauth/twitteroauth.php');

// Define keys
define("CONSUMER_KEY", "xxxxxxxxxxxxxxxxxxxx");
define("CONSUMER_SECRET", "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
define("OAUTH_TOKEN", "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
define("OAUTH_SECRET", "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
// Last sent tweet is stored here
$last_tweetfn = "lasttweet.txt";
// Pattern to search (#feup)
$feedURL = 'http://search.twitter.com/search.rss?q=%23feup&src=typd';



// Login to twitter
echo "Logging in...\n";
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
$content = $connection->get('account/verify_credentials');

// Get Feed
echo "Retreiving feed from: $feedURL\n";
$rawFeed = file_get_contents($feedURL);
// Create XML object with RSS contents
echo "Creating XML element...\n";
$xml = new SimpleXmlElement($rawFeed);
// Assign initial position
$pos = count($xml->channel->item);

// Get last tweet sent
$last = file_get_contents($last_tweetfn);
echo "Last tweet sent was: $last\n";

// Start searching for new tweets
echo "Looking for new tweets...\n";
for($counter = count($xml->channel->item); $counter > -1; $counter--) { // Start with oldest
    if(strcmp($last, $xml->channel->item[$counter]->link) == 0) {
         $pos = $counter-1;
         echo "There are $counter new tweets.\n";
         break;
    }
}

// Retweet new tweets
if($pos >= 0) {
    echo "New tweets are:\n";
    for($counter = $pos; $counter > -1; $counter--) {
         // Retweet
         // format is: http://twitter.com/name/statuses/id
         $pieces = explode("/", $xml->channel->item[$counter]->link);
         $tweet_id = end($pieces);
         echo "\t- ". $xml->channel->item[$counter]->link . " (id = $tweet_id)\n";
         $connection->post("statuses/retweet/$tweet_id");
    }
}

// Refresh new last tweet
$last = $xml->channel->item[0]->link;
echo "\nNew last tweet is: $last\n";
file_put_contents($last_tweetfn, $last);

//$message = "Hello World! (".date("i").")";
//$connection->post('statuses/update', array('status' => $message));

?>
