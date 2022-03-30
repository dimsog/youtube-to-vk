# YoutubeToVk
Библиотека для переноса видео с Youtube в группу или на страницу пользователя.

Эта библиотека использует [youtube-dl-php](https://github.com/norkunas/youtube-dl-php) и [vk-php-sdk](https://github.com/VKCOM/vk-php-sdk).

### Требования
1. PHP 7.4+
2. Python3 (для youtube-dl или yt-dlp)
3. Установленный на сервере [youtube-dl](https://github.com/ytdl-org/youtube-dl) или [yt-dlp](https://github.com/yt-dlp/yt-dlp) (у автора скорость скачивания была выше). Это необходимо для youtube-dl-php.

### Перед использованием
Наличие VK access token обязательно. Вы можете получить ссылку на получение токена с помощью следующего метода:

```php
use Dimsog\YoutubeToVk\YoutubeToVk;
$clientId = 123456;
echo YoutubeToVk::generateVkAccessTokenLink($clientId)
```

### Использование
```php
use Dimsog\YoutubeToVk\YoutubeToVk;

$youtubeToVk = new YoutubeToVk('vk_access_token');

// постинг в группу
$youtubeToVk->toGroup('https://www.youtube.com/watch?v=XXXXXX', 'https://vk.com/group_link_here');

// постинг на страницу пользователя
$youtubeToVk->toUser('https://www.youtube.com/watch?v=XXXXXX');
```

### YoutubeDl
Вам полностью доступен инстанс YoutubeDl. Это особенно полезно, если нужно показывать прогресс скачивания видео с VK (код взят из официальной документации к [youtube-dl-php](https://github.com/norkunas/youtube-dl-php)):
```php
$youtubeToVk->getYoutubeDl()->onProgress(static function (?string $progressTarget, string $percentage, ?string $size, ?string $speed, ?string $eta, ?string $totalTime): void {
    echo date("H:i:s") . ", $percentage; Size: $size";
    if ($speed) {
        echo "; Speed: $speed";
    }
    if ($eta) {
        echo "; ETA: $eta";
    }
    echo "\n";
});
```

Если вы используете yt-dlp, пропишите к ней путь:
```php
$youtubeToVk->getYoutubeDl()->setBinPath('/usr/local/bin/yt-dlp');
```

### Как получить список всех видео?
```php
use Dimsog\YoutubeToVk\YoutubeVideoList;

$youtubeVideoList = new YoutubeVideoList("youtube_api_key");

// можно указывать как id канала, так и его userName
$youtubeVideoList->getVideosFromChannel('EmpathyTest');
$youtubeVideoList->getVideosFromChannel('UCKZC###2ND###t-jY###A#hA');

```