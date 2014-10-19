<?php

require_once('twitteroauth/twitteroauth.php');

// Define keys
define("CONSUMER_KEY", "xxxxxxxxxxxxxxxxxxxx");
define("CONSUMER_SECRET", "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
define("OAUTH_TOKEN", "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
define("OAUTH_SECRET", "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
// Last sent tweet is stored here
$last_tweetfn = "lasttweet.txt";
$last_id = file_get_contents($last_tweetfn);
// Pattern to search (#feup)
$feedURL = "search/tweets.json?q=%23feup&since_id=$last_id&result_type=recent";


function getConnectionWithAccessToken($oauth_token, $oauth_token_secret) {
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth_token, $oauth_token_secret);
    return $connection;
}

// 
echo "Starting...<br>";
echo "Last retweet ID was: $last_id<br>";
$connection = getConnectionWithAccessToken(OAUTH_TOKEN, OAUTH_SECRET);
$content = $connection->get($feedURL);
$tweets = $content['statuses'];
$pos = count($tweets) - 1;

// Retweet new tweets
if($pos >= 0) {
    echo "New tweets are:<br>";
    for($i = $pos; $i >= 0; $i--) {
         // Retweet
         $tweet_id = $tweets[$i]['id_str'];
         echo "     - ".$tweets[$i]['text']." (id = $tweet_id)<br>";
         echo $connection->post("statuses/retweet/$tweet_id");
    }

    // Refresh new last tweet
    $last_id = $tweets[0]['id_str'];
}


echo "New last tweet ID is: $last_id<br>";
file_put_contents($last_tweetfn, $last_id);

echo "All done!<br>";

?>
