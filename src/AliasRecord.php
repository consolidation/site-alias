<?php
namespace Consolidation\SiteAlias;

use Consolidation\Config\Config;
use Consolidation\Config\ConfigInterface;
use Consolidation\Config\Util\ArrayUtil;
use Consolidation\SiteAlias\Util\FsUtils;

/**
 * An alias record is a configuration record containing well-known items.
 *
 * @see AliasRecordInterface for documentation
 */
class AliasRecord extends Config implements AliasRecordInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @inheritdoc
     */
    public function __construct(array $data = null, $name = '', $env = '')
    {
        parent::__construct($data);
        if (!empty($env)) {
            $name .= ".$env";
        }
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function getConfig(ConfigInterface $config, $key, $default = null)
    {
        if ($this->has($key)) {
            return $this->get($key, $default);
        }
        return $config->get($key, $default);
    }

    /**
     * @inheritdoc
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function hasRoot()
    {
        return $this->has('root');
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception when the alias does not specify a root.
     */
    public function root()
    {
        if (!$this->hasRoot()) {
            throw new \Exception('Site alias ' . $this->name . ' does not specify a root.');
        }
        $root = $this->get('root');
        if ($this->isLocal()) {
            return FsUtils::realpath($root);
        }
        return $root;
    }

    /**
     * @inheritdoc
     */
    public function uri()
    {
        return $this->get('uri');
    }

    /**
     * @inheritdoc
     */
    public function setUri($uri)
    {
        return $this->set('uri', $uri);
    }

    /**
     * @inheritdoc
     */
    public function remoteHostWithUser()
    {
        $result = $this->remoteHost();
        if (!empty($result) && $this->hasRemoteUser()) {
            $result = $this->remoteUser() . '@' . $result;
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function remoteUser()
    {
        return $this->get('user');
    }

    /**
     * @inheritdoc
     */
    public function hasRemoteUser()
    {
        return $this->has('user');
    }

    /**
     * @inheritdoc
     */
    public function remoteHost()
    {
        return $this->get('host');
    }

    /**
     * @inheritdoc
     */
    public function isRemote()
    {
        return $this->has('host');
    }

    /**
     * @inheritdoc
     */
    public function isLocal()
    {
        return !$this->isRemote();
    }

    /**
     * @inheritdoc
     */
    public function isNone()
    {
        return empty($this->root()) && $this->isLocal();
    }

    /**
     * @inheritdoc
     */
    public function localRoot()
    {
        if ($this->isLocal()) {
            return $this->root();
        }

        return false;
    }

    /**
     * os returns the OS that this alias record points to. For local alias
     * records, PHP_OS will be returned. For remote alias records, the
     * value from the `os` element will be returned. If there is no `os`
     * element, then the default assumption is that the remote system is Linux.
     *
     * @return string
     *   Linux
     *   WIN* (e.g. WINNT)
     *   CYGWIN
     *   MINGW* (e.g. MINGW32)
     */
    public function os()
    {
        if ($this->isLocal()) {
            return PHP_OS;
        }
        return $this->get('os', 'Linux');
    }

    /**
     * @inheritdoc
     */
    public function exportConfig()
    {
        return $this->remap($this->export());
    }

    /**
     * Reconfigure data exported from the form it is expected to be in
     * inside an alias record to the form it is expected to be in when
     * inside a configuration file.
     */
    protected function remap($data)
    {
        foreach ($this->remapOptionTable() as $from => $to) {
            if (isset($data[$from])) {
                unset($data[$from]);
            }
            $value = $this->get($from, null);
            if (isset($value)) {
                $data['options'][$to] = $value;
            }
        }

        return new Config($data);
    }

    /**
     * Fetch the parameter-specific options from the 'alias-parameters' section of the alias.
     * @param string $parameterName
     * @return array
     */
    protected function getParameterSpecificOptions($aliasData, $parameterName)
    {
        if (!empty($parameterName) && $this->has("alias-parameters.{$parameterName}")) {
            return $this->get("alias-parameters.{$parameterName}");
        }
        return [];
    }

    /**
     * Convert the data in this record to the layout that was used
     * in the legacy code, for backwards compatiblity.
     */
    public function legacyRecord()
    {
        $result = $this->exportConfig()->get('options', []);

        // Backend invoke needs a couple of critical items in specific locations.
        if ($this->has('paths.drush-script')) {
            $result['path-aliases']['%drush-script'] = $this->get('paths.drush-script');
        }
        if ($this->has('ssh.options')) {
            $result['ssh-options'] = $this->get('ssh.options');
        }
        return $result;
    }

    /**
     * Conversion table from old to new option names. These all implicitly
     * go in `options`, although they can come from different locations.
     */
    protected function remapOptionTable()
    {
        return [
            'user' => 'remote-user',
            'host' => 'remote-host',
            'root' => 'root',
            'uri' => 'uri',
        ];
    }
}
