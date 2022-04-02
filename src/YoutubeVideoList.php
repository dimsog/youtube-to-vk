<?php

namespace Dimsog\YoutubeToVk;

use GuzzleHttp\Client;

class YoutubeVideoList
{
    private string $youtubeApiKey;

    private Client $httpClient;

    private array $videos = [];


    public function __construct(string $youtubeApiKey)
    {
        $this->youtubeApiKey = $youtubeApiKey;
        $this->httpClient = new Client();
    }

    public function getVideosFromChannel(string $channelId): array
    {
        return $this->request($this->fetchRealChannelId($channelId));
    }

    private function fetchRealChannelId(string $channelId): string
    {
        $response = $this->httpClient->request('GET', 'https://www.googleapis.com/youtube/v3/search', [
            'query' => [
                'key' => $this->youtubeApiKey,
                'part' => 'snippet',
                'type' => 'channel',
                'maxResults' => 1,
                'q' => $channelId
            ]
        ]);
        $response = json_decode($response->getBody()->getContents());
        if ($response->pageInfo->totalResults == 0) {
            return $channelId;
        }
        return $response->items[0]->id->channelId;
    }

    private function request(string $channelId, ?string $nextPageToken = null): array
    {
        $query = [
            'key' => $this->youtubeApiKey,
            'part' => 'snippet',
            'channelId' => $channelId,
            'maxResults' => 50
        ];
        if ($nextPageToken != null) {
            $query['pageToken'] = $nextPageToken;
        }
        $response = $this->httpClient->request('GET', 'https://www.googleapis.com/youtube/v3/search', [
            'query' => $query
        ]);
        $response = json_decode($response->getBody()->getContents());
        foreach ($response->items as $item) {
            if (empty($item->id->videoId) == false) {
                $this->videos[] = 'https://www.youtube.com/watch?v=' . $item->id->videoId;
            }
        }
        if (empty($response->nextPageToken) == false) {
            $this->request($channelId, $response->nextPageToken);
        }
        return $this->videos;
    }
}