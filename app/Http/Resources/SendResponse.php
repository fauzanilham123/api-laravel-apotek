<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SendResponse extends JsonResource
{
    //define properti
    public $status;
    public $message;
    public $resource;
    public $code;

    /**
     * __construct
     *
     * @param  mixed $status
     * @param  mixed $message
     * @param  mixed $resource
     * @param  mixed $code
     * @return void
     */
    public function __construct($message, $resource)
    {
        parent::__construct($resource);
        $this->message = $message;
    }

    /**
     * toArray
     *
     * @param  mixed $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'success'   => true,
            'code'   => 200,
            'message'   => $this->message,
            'data'      => $this->resource
        ];
    }
}
