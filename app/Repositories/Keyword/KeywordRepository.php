<?php
namespace App\Repositories\Keyword;

use App\Models\Keyword;
use App\Repositories\BaseRepository;
use App\Repositories\Keyword\KeywordRepositoryInterface;

class KeywordRepository extends BaseRepository implements KeywordRepositoryInterface
{
    public function getModel()
    {
        return Keyword::class;
    }

    public function makeTransaction()
    {
        
    }
}