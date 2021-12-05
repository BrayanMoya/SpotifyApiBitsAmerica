<?php

namespace Spotify;

class ParsedResponse
{

    /** @var array */
    private $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /** @return array */
    public function getResponse(): array
    {
        return $this->response;
    }

    public function unsetMarkets(): void
    {
        $items = $this->response['albums']['items'];
        $response = [];

        foreach ($items as $item) {
            unset($item['available_markets']);
            $response[] = $item;
        }

        $this->response['albums']['items'] = $response;
    }

}
