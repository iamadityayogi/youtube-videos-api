<?php
error_reporting(1);
require_once("db.php");
// Include the Google API PHP Client Library
require_once './vendor/autoload.php';

// Set your API key
$api_key = 'AIzaSyCsm0h4cJCNaAouXeRWAeGZqMeEu3d7b9s';  //radhika
// $api_key = 'AIzaSyAVvLaQ6f3zJhrwm9utn327yBHnGFjTzXE';  //ajay
// $api_key = 'AIzaSyBnc4nv95vzMzycadLoWoerP70qZ1Pj40E';  //aditya
// $api_key = 'AIzaSyCDkdM6r-p0wj1ahhvdBQZPy0q4dhD4alQ';  //pritesh
// $api_key = 'AIzaSyALz0xxCrK4oHfPyiYVT3XdqGy0JL6KhIE';  //ankush

// Set the channel ID
$channel_id = 'UCvRAWnbt3DkuMPyP3S60qwA';

// Set the API client
$client = new Google_Client();
$client->setApplicationName('YouTube Data API PHP');
$client->setDeveloperKey($api_key);

// Create a YouTube service object
$youtube = new Google_Service_YouTube($client);

try {
    // Fetch the uploads playlist for the channel
    $channelsResponse = $youtube->channels->listChannels('contentDetails', array(
        'id' => $channel_id
    ));

    $uploadsPlaylistId = $channelsResponse->getItems()[0]->getContentDetails()->getRelatedPlaylists()->getUploads();

    // Fetch the videos from the uploads playlist
    $videos = array();
    $nextPageToken = null;

    do {
        $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
            'playlistId' => $uploadsPlaylistId,
            'maxResults' => 50, // Set the number of videos per request (maximum: 50)
            // 'order' => 'date', // Sort the videos by date
            'pageToken' => $nextPageToken
        ));

        foreach ($playlistItemsResponse->getItems() as $playlistItem) {
            $videoId = $playlistItem->snippet->resourceId->videoId;

            // Fetch video details
            $videoResponse = $youtube->videos->listVideos('snippet', array(
                'id' => $videoId
            ));

            $video = $videoResponse->getItems()[0];

            $videos[] = array(
                'title' => $video->snippet->title,
                'description' => $video->snippet->description,
                'thumbnail' => $video->snippet->thumbnails->default->url,
                'videoId' => $videoId,
                'publishedAt' => $video->snippet->publishedAt
            );
        }

        $nextPageToken = $playlistItemsResponse->getNextPageToken();
    } while ($nextPageToken);

    // Sort the videos by date
    usort($videos, function ($a, $b) {
        return strtotime($a['publishedAt']) - strtotime($b['publishedAt']);
    });

    // Output the video details
    $xx = 0;
    foreach ($videos as $video) {
        // echo 'count: ' . $xx++ . '<br>';
        // echo 'Title: ' . $video['title'] . '<br>';
        // echo 'Description: ' . $video['description'] . '<br>';
        // echo 'Thumbnail: <img src="' . $video['thumbnail'] . '" alt="Thumbnail"><br>';
        // echo 'Video ID: ' . $video['videoId'] . '<br>';
        // echo 'Published At: ' . $video['publishedAt'] . '<br><br>';
        $title = $video['title'];
        $description = ($video['description']);
        // $description = mb_convert_encoding($video['description'], 'UTF-8', 'UTF-8');
        $thumbnail =$video['thumbnail'];
        $videoId = $video['videoId'];
        $date = date('Y-m-d H:i:s');
        ///aditya
        $sql = "INSERT INTO episodes_new (title,description,image,url) VALUES (?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $title,$description,$thumbnail,$videoId);
        $stmt->execute();
        $xx++;
    }
} catch (Google_Service_Exception $e) {
    echo 'Error: ' . $e->getMessage();
} catch (Google_Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>