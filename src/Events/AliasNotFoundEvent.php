<?php

namespace Consolidation\SiteAlias\Events;

use Consolidation\SiteAlias\SiteAlias;
use Symfony\Contracts\EventDispatcher\Event;

class AliasNotFoundEvent extends Event
{

    protected $aliasName;

    protected $alias = false;

    const NAME = 'alias-not-found';

    public function __construct($aliasName)
    {
        $this->aliasName = $aliasName;
    }

    public function setAlias(SiteAlias $alias)
    {
        $this->alias = $alias;
    }

    public function hasAlias()
    {
        return $this->alias !== false;
    }

    public function getAlias()
    {
        return $this->alias;
    }
}
