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
$feedURL = 'search/tweets.json?q=%23feup&result_type=recent';


function getConnectionWithAccessToken($oauth_token, $oauth_token_secret) {
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth_token, $oauth_token_secret);
    return $connection;
}

// 
echo "Starting...<br>";
$connection = getConnectionWithAccessToken(OAUTH_TOKEN, OAUTH_SECRET);
$content = $connection->get($feedURL);
$tweets = $content[statuses];
$pos = count($tweets) - 1;

$last_id = file_get_contents($last_tweetfn);
echo "Last retweet ID was: $last_id<br>";

for($i = $pos; $i >= 0; $i--) { // Start with oldest
    if(strcmp($last_id, $tweets[$i][id]) == 0) {
        $pos = $i-1;
        echo "There are $i new tweets.<br>";
        break;
    }
}

// Retweet new tweets
if($pos >= 0) {
    echo "New tweets are:<br>";
    for($i = $pos; $i >= 0; $i--) {
         // Retweet
         $tweet_id = $tweets[$i][id];
         echo "     - ".$tweets[$i][text]." (id = $tweet_id)<br>";
         $connection->post("statuses/retweet/$tweet_id");
    }
}

// Refresh new last tweet
$last = $tweets[0][id];
echo "New last tweet ID is: $last<br>";
file_put_contents($last_tweetfn, $last);

echo "All done!<br>";

?>
