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

    public function toGroupVideos(string $youtubeVideoLink, string $groupLink)
    {

    }

    public function toGroupWall(string $youtubeVideoLink, string $groupLink)
    {

    }

    private function toUser(string $youtubeVideoLink, string $userLink)
    {

    }

    public function toUserWall(string $youtubeVideoLink, string $userLink)
    {

    }
}