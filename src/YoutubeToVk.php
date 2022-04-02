<?php

namespace Dimsog\YoutubeToVk;

use DomainException;
use VK\OAuth\Scopes\VKOAuthUserScope;
use VK\OAuth\VKOAuth;
use VK\OAuth\VKOAuthDisplay;
use VK\OAuth\VKOAuthResponseType;
use YoutubeDl\Entity\Video;
use YoutubeDl\YoutubeDl;
use YoutubeDl\Options;
use VK\Client\VKApiClient;

class YoutubeToVk
{
    private VKApiClient $vkApiClient;

    private YoutubeDl $youtubeDl;

    private string $vkAccessToken;

    private array $groupsCache = [];


    public function __construct(string $vkAccessToken)
    {
        $this->vkApiClient = new VKApiClient();
        $this->youtubeDl = new YoutubeDl();
        $this->vkAccessToken = $vkAccessToken;
    }

    public static function generateVkAccessTokenLink(int $clientId): string
    {
        $vkAuth = new VKOAuth();
        $scope = [
            VKOAuthUserScope::WALL,
            VKOAuthUserScope::VIDEO,
            VKOAuthUserScope::OFFLINE,
            VKOAuthUserScope::GROUPS
        ];
        return $vkAuth->getAuthorizeUrl(
            VKOAuthResponseType::TOKEN,
            $clientId,
            'https://oauth.vk.com/blank.html',
            VKOAuthDisplay::PAGE,
            $scope
        );
    }

    public function getYoutubeDl(): YoutubeDl
    {
        return $this->youtubeDl;
    }

    public function toGroup(string $youtubeVideoLink, string $groupLink): int
    {
        return $this->uploadVideoToVk($youtubeVideoLink, $this->extractGroupId($groupLink));
    }

    public function toUser(string $youtubeVideoLink): int
    {
        return $this->uploadVideoToVk($youtubeVideoLink);
    }

    private function uploadVideoToVk(string $youtubeVideoLink, ?int $groupId = null): int
    {
        $video = $this->downloadVideo($youtubeVideoLink);
        $vkVideo = $this->vkApiClient->getRequest()->upload(
            $this->getUploadUrl($video, $groupId),
            'video_file',
            $video->getFile()->getPathname()
        );
        @unlink($video->getFile()->getPathname());
        if (empty($vkVideo['video_id'])) {
            throw new DomainException('Не удалось загрузить видео');
        }
        return $vkVideo['video_id'];
    }

    private function downloadVideo(string $youtubeLink): Video
    {
        $collection = $this->youtubeDl->download(
            Options::create()
                ->downloadPath(sys_get_temp_dir())
                ->url($youtubeLink)
        );
        if ($collection->count() === 0) {
            throw new \RuntimeException('Не удалось скачать видео');
        }
        $downloadedVideo = $collection->getVideos()[0];
        if ($downloadedVideo->getError() !== null) {
            throw new \RuntimeException($downloadedVideo->getError());
        }
        return $downloadedVideo;
    }

    private function getUploadUrl(Video $video, ?int $groupId = null): string
    {
        $params = [
            'name' => $video->getTitle()
        ];
        if ($groupId > 0) {
            $params['group_id'] = $groupId;
        }
        $address = $this->vkApiClient->video()->save($this->vkAccessToken, $params);
        return $address['upload_url'];
    }

    private function extractGroupId(string $groupLink): int
    {
        if (empty($this->groupsCache[$groupLink]) == false) {
            return $this->groupsCache[$groupLink];
        }
        if (is_numeric($groupLink)) {
            $this->groupsCache[$groupLink] = abs($groupLink);
            return $this->groupsCache[$groupLink];
        }

        $groupId = $this->removeHostFromLink($groupLink);
        $response = $this->vkApiClient->groups()->getById($this->vkAccessToken, [
            'group_id' => $groupId
        ]);
        $this->groupsCache[$groupLink] = $response[0]['id'];
        return $response[0]['id'];
    }

    private function removeHostFromLink(string $vkLink): string
    {
        return str_replace([
            'https://vk.com/',
            'https://m.vk.com/',
            'http://vk.com/',
            'http://m.vk.com/',
        ], '', $vkLink);
    }
}