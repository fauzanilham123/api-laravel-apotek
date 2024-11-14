<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;


class SendError extends JsonResource
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
    public function __construct($code, $message, $resource)
    {
        $this->message = $message;
        $this->code = $code;
        $this->resource = $resource;
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
            'success'   => false,
            'code'   => $this->code,
            'message'   => $this->message,
            'result'      => $this->resource
        ];
    }

    public function toResponse($request): JsonResponse
    {
        return response()->json($this->toArray($request), $this->code);
    }
}