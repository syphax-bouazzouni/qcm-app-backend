<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $propositions_state = $this->propositionsState;

        $propositions = [];
        $propositionsState = [];

        foreach ($propositions_state as $k=>$p){
            $propositions[] = [
                "index" => $k,
                "proposition" => $p->proposition,
                "isResponse" => $p->isResponse,

            ];
            $propositionsState[] = $p->propositionsState;
        }

        return [
            "id" => $this->id,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "question" => [
                "text" => $this->text,
                "type" => $this->type ,
                "propositions" => $propositions,
                "explication" => $this->explication
            ],
            "state" => $this->state,
            "isQrocResponded" => $this->isQrocResponded,
            "propositionsState" => $propositionsState
        ];
    }
}
