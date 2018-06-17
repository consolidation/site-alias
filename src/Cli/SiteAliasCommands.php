<?php

namespace Consolidation\SiteAlias\Cli;

use Consolidation\SiteAlias\SiteAliasFileLoader;
use Consolidation\SiteAlias\Util\YamlDataFileLoader;

class SiteAliasCommands extends \Robo\Tasks
{
    protected $aliasLoader;

    /**
     * List available site aliases
     *
     * @command site:list
     * @format yaml
     * @return array
     */
    public function siteList($dir)
    {
        $this->aliasLoader = new SiteAliasFileLoader();
        $ymlLoader = new YamlDataFileLoader();
        $this->aliasLoader->addLoader('yml', $ymlLoader);

        $this->io()->note("loading from $dir");

        $this->aliasLoader->addSearchLocation($dir);

        $all = $this->aliasLoader->loadAll();

        if (empty($all)) {
            throw new \Exception("No aliases found");
        }

        return $all;
    }
}
