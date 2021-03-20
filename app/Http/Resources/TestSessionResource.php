<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TestSessionResource extends JsonResource
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
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "test" => [
                "id" => $this->test,
                "text" => $this->text,
                "type" => $this->type,
                "source" => $this->source,
            ],
            "text" => $this->text,
            "timer" => $this->timer,
            "state" => $this->state,
            "quizLabel" => $this->quizLabel,
            "questions" => QuestionSessionResource::collection($this->questions)
        ];
    }
}
