<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public $success;
    public $message;

    /**
     * __construct
     *
     * @param  mixed $success
     * @param  mixed $message
     * @param  mixed $resource
     * @return void
     */
    public function __construct($success, $message, $resource)
    {
        parent::__construct($resource);
        $this->success  = $success;
        $this->message = $message;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'success'   => $this->success,
            'message'   => $this->message,
            'data'      => $this->resource
        ];
    }
}
