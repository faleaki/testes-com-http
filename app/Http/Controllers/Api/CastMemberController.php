<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BasicCrudController;
use App\Models\CastMember;
use Illuminate\Http\Request;

class CastMemberController extends  BasicCrudController
{
    public function __construct()
    {
      $this->rules = [
        'name' => 'required|max:255',
        'type' => 'required|in:' . implode(',', [CastMember::TYPE_ACTOR, CastMember::TYPE_DIRECTOR])
      ];
    }
}
