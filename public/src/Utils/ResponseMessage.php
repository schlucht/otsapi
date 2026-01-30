<?php

declare(strict_types=1);

namespace Ots\API\Utils;

class ResponseMessage
{
    public bool $success;
    public mixed $data;
    public ?string $error;

    public function __construct(bool $success, mixed $data = null, ?string $error = null)
    {
        $this->success = $success;
        $this->data = $data;
        $this->error = $error;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'data'    => $this->data,
            'error'   => $this->error,
        ];
    }
}