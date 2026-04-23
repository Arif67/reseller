<?php

namespace App\Traits;

trait Response
{
    private array $response = [
        'success' => null,
        'message' => '',
        'data' => [],
    ];

    private string $errorMessage = 'Something went wrong!';

    protected function response(array $data = []): object
    {
        $this->response['data'] = $data;

        return $this;
    }

    protected function success(string $message = null): array
    {
        $this->response['success'] = true;
        $this->response['message'] = $message ? __($message) : __('');

        return $this->response;
    }

    protected function error(string $message = null): array
    {
        $this->response['success'] = false;
        $this->response['message'] = $message ? __($message) : __($this->errorMessage);

        return $this->response;
    }
}
