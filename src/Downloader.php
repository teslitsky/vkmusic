<?php

namespace VkUtils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Downloader
{
    /**
     * @param string $link
     * @return string
     */
    public function download($link)
    {
        $url = $title = null;
        $client = new Client();

        try {
            $response = $client->get("https://api.vk.com/method/wall.getById?posts=-{$link}&callback=?");
            $json = $response->json();
            $attachments = $json['response'][0]['attachments'];
            foreach ($attachments as $attachment) {
                if ($attachment['type'] != 'audio') {
                    continue;
                }

                $link = filter_var($attachment['audio']['url'], FILTER_SANITIZE_STRING);
                $artist = html_entity_decode(filter_var($attachment['audio']['artist'], FILTER_SANITIZE_STRING));
                $title = html_entity_decode(filter_var($attachment['audio']['title'], FILTER_SANITIZE_STRING));
                $title = $artist . ' - ' . $title;
            }

            $size = $this->remote_filesize($link);
            $header = 'Content-Disposition: attachment; filename="' . $title . '.mp3"';

            if ($size) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header($header);
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . $size);
                ob_clean();
                flush();
                readfile($link);
                exit;
            }

        } catch (RequestException $e) {
            echo $e->getRequest();
            if ($e->hasResponse()) {
                echo $e->getResponse();
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $link . ' downloaded. ' . PHP_EOL;
    }

    public function remote_filesize($url)
    {
        $regex = '/^Content-Length: *+\K\d++$/im';
        if (!$fp = @fopen($url, 'rb')) {
            return false;
        }

        if (isset($http_response_header) && preg_match($regex, implode("\n", $http_response_header), $matches)
        ) {
            return (int)$matches[0];
        }

        return strlen(stream_get_contents($fp));
    }
}