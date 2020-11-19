<?php

namespace App\Request;

class GetOneArticleRequest
{
    public $id;
}

class GetAllArticleRequest extends BaseRequest
{
    public $userId;
    public $onlyBookmarked;
    public $onlyShared;
}
