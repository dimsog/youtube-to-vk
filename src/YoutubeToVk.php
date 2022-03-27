<?php

namespace Dimsog\YoutubeToVk;

use YoutubeDl\YoutubeDl;
use YoutubeDl\Options;
use VK\Client\VKApiClient;

class Transfer
{
    private string $vkAccessToken;


    public function __construct(string $vkAccessToken)
    {
        $this->vkAccessToken = $vkAccessToken;
    }

    public function toGroupVideos(string $youtubeVideoLink, int $groupId)
    {

    }

    public function toGroupWall(string $youtubeVideoLink, int $groupId)
    {

    }

    private function toUser(string $youtubeVideoLink, int $userId)
    {

    }

    public function toUserWall(string $youtubeVideoLink, int $userId)
    {

    }
}