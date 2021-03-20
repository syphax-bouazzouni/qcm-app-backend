<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "user" => $this->user,
            "state" => $this->state,
            "currentTest" => $this->currentTest,
            "tests" => TestSessionResource::collection($this->tests),
            "progression" => $this->progression,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
