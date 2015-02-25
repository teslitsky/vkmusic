<?php

namespace VkUtils;

class Downloader
{
    /**
     * Download audio attachment
     * @param Audio $audio
     * @return void
     */
    public function download(Audio $audio)
    {
        $size = $this->remoteFileSize($audio->getLink());
        if ($size) {
            $content = 'Content-Disposition: attachment; filename="' . $audio->getFullTitle() . '.mp3"';
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header($content);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . $size);
            ob_clean();
            flush();
            readfile($audio->getLink());
        }
    }

    /**
     * @param string $url URL to file
     * @return int File size
     */
    public function remoteFileSize($url)
    {
        $regex = '/^Content-Length: *+\K\d++$/im';
        if (!$fp = @fopen($url, 'rb')) {
            return 0;
        }

        if (isset($http_response_header) && preg_match($regex, implode("\n", $http_response_header), $matches)
        ) {
            return (int)$matches[0];
        }

        return strlen(stream_get_contents($fp));
    }
}
