<?php
namespace Consolidation\SiteAlias;

use Consolidation\Config\ConfigInterface;
use Consolidation\SiteAlias\AliasRecord;
use Consolidation\SiteAlias\AliasRecordInterface;
use Consolidation\SiteAlias\AliasRecordTrait;

/**
 * SiteAliasWithConfig delegates to a site alias, and
 * also combines it with two config stores:
 *
 *   - Runtime config (set on commandline): Options that override site alias contents
 *   - Default config (set from config files): Default options
 */
class SiteAliasWithConfig implements AliasRecordInterface
{
    use AliasRecordTrait;

    protected $runtimeConfig;
    protected $siteAlias;
    protected $defaultConfig;

    public function __construct(ConfigInterface $runtimeConfig, AliasRecordInterface $siteAlias, ConfigInterface $defaultConfig)
    {
        $this->runtimeConfig = $runtimeConfig;
        $this->siteAlias = $siteAlias;
        $this->defaultConfig = $defaultConfig;
    }

    /**
     * @inheritdoc
     */
    public function name()
    {
        return $this->siteAlias->name();
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->changesProhibited();
    }

    /**
     * @inheritdoc
     */
    public function has($key)
    {
        return
            $this->runtimeConfig->has($key) ||
            $this->siteAlias->has($key) ||
            $this->defaultConfig->has($key);
    }

    /**
     * @inheritdoc
     */
    public function get($key, $defaultFallback = null)
    {
        if ($this->runtimeConfig->has($key)) {
            return $this->runtimeConfig->get($key);
        }
        if ($this->siteAlias->has($key)) {
            return $this->siteAlias->get($key);
        }
        if ($this->defaultConfig->has($key)) {
            return $this->defaultConfig->get($key);
        }

        return $defaultFallback;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {
        $this->changesProhibited();
    }

    /**
     * @inheritdoc
     */
    public function import($data)
    {
        $this->changesProhibited();
    }

    /**
     * @inheritdoc
     */
    public function replace($data)
    {
        $this->changesProhibited();
    }

    /**
     * @inheritdoc
     */
    public function combine($data)
    {
        $this->changesProhibited();
    }

    /**
     * Export all configuration as a nested array.
     */
    public function export()
    {
        throw new \Exception('SiteAliasWithConfig::export() not supported.');
    }

    /**
     * @inheritdoc
     */
    public function hasDefault($key)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getDefault($key, $defaultFallback = null)
    {
        return $defaultFallback;
    }

    /**
     * @inheritdoc
     */
    public function setDefault($key, $value)
    {
        $this->changesProhibited();
    }

    /**
     * @inheritdoc
     */
    public function exportConfig()
    {
        throw new \Exception('SiteAliasWithConfig::exportConfig() not supported.');
    }

    protected function changesProhibited()
    {
        throw new \Exception('Changing a SiteAliasWithConfig is not permitted.');
    }
}
