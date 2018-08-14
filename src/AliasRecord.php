<?php
namespace Consolidation\SiteAlias;

use Consolidation\Config\Config;
use Consolidation\Config\ConfigInterface;
use Consolidation\Config\Util\ArrayUtil;
use Drush\Utils\FsUtils;

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
     */
    public function root()
    {
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
        return !$this->isLocal();
    }

    /**
     * @inheritdoc
     */
    public function isLocal()
    {
        if ($host = $this->remoteHost()) {
            return $host == 'localhost' || $host == '127.0.0.1';
        }
        return true;
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
        if (!$this->isRemote()) {
            return $this->root();
        }

        return false;
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
