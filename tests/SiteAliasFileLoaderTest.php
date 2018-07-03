<?php
namespace Consolidation\SiteAlias;

use PHPUnit\Framework\TestCase;
use Consolidation\SiteAlias\Util\YamlDataFileLoader;

class SiteAliasFileLoaderTest extends TestCase
{
    use FixtureFactory;
    use FunctionUtils;

    function setUp()
    {
        $this->sut = new SiteAliasFileLoader();

        $ymlLoader = new YamlDataFileLoader();
        $this->sut->addLoader('yml', $ymlLoader);
    }

    public function testLoadSingleAliasFile()
    {
        $siteAliasFixtures = $this->fixturesDir() . '/sitealiases/single';
        $this->assertTrue(is_dir($siteAliasFixtures));
        $this->assertTrue(is_file($siteAliasFixtures . '/simple.site.yml'));
        $this->assertTrue(is_file($siteAliasFixtures . '/single.site.yml'));

        $this->sut->addSearchLocation($siteAliasFixtures);

        // Add a secondary location
        $siteAliasFixtures = $this->fixturesDir() . '/sitealiases/other';
        $this->assertTrue(is_dir($siteAliasFixtures));
        $this->sut->addSearchLocation($siteAliasFixtures);

        // Look for a simple alias with no environments defined
        $name = new SiteAliasName('simple');
        $this->assertEquals('simple', $name->sitename());
        $result = $this->callProtected('loadSingleAliasFile', [$name]);
        $this->assertTrue($result instanceof AliasRecord);
        $this->assertEquals('/path/to/simple', $result->get('root'));

        // Look for a single alias without an environment specified.
        $name = new SiteAliasName('single');
        $this->assertEquals('single', $name->sitename());
        $result = $this->callProtected('loadSingleAliasFile', [$name]);
        $this->assertTrue($result instanceof AliasRecord);
        $this->assertEquals('/path/to/single', $result->get('root'));
        $this->assertEquals('bar', $result->get('foo'));

        // Same test, but with environment explicitly requested.
        $name = SiteAliasName::parse('@single.alternate');
        $result = $this->callProtected('loadSingleAliasFile', [$name]);
        $this->assertTrue($result instanceof AliasRecord);
        $this->assertEquals('/alternate/path/to/single', $result->get('root'));
        $this->assertEquals('bar', $result->get('foo'));

        // Same test, but with location explicitly filtered.
        $name = SiteAliasName::parse('@other.single.dev');
        $result = $this->callProtected('loadSingleAliasFile', [$name]);
        $this->assertTrue($result instanceof AliasRecord);
        $this->assertEquals('/other/path/to/single', $result->get('root'));
        $this->assertEquals('baz', $result->get('foo'));

        // Try to fetch an alias that does not exist.
        $name = SiteAliasName::parse('@missing');
        $result = $this->callProtected('loadSingleAliasFile', [$name]);
        $this->assertFalse($result);

        // Try to fetch an alias using a missing location
        $name = SiteAliasName::parse('@missing.single.alternate');
        $result = $this->callProtected('loadSingleAliasFile', [$name]);
        $this->assertFalse($result);
    }

    public function testLoadLegacy()
    {
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/legacy');
    }

    public function testLoad()
    {
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/single');

        // Look for a simple alias with no environments defined
        $name = new SiteAliasName('simple');
        $result = $this->sut->load($name);
        $this->assertTrue($result instanceof AliasRecord);
        $this->assertEquals('/path/to/simple', $result->get('root'));

        // Look for a single alias without an environment specified.
        $name = new SiteAliasName('single');
        $result = $this->sut->load($name);
        $this->assertTrue($result instanceof AliasRecord);
        $this->assertEquals('/path/to/single', $result->get('root'));
        $this->assertEquals('bar', $result->get('foo'));

        // Same test, but with environment explicitly requested.
        $name = new SiteAliasName('single', 'alternate');
        $result = $this->sut->load($name);
        $this->assertTrue($result instanceof AliasRecord);
        $this->assertEquals('/alternate/path/to/single', $result->get('root'));
        $this->assertEquals('bar', $result->get('foo'));

        // Try to fetch an alias that does not exist.
        $name = new SiteAliasName('missing');
        $result = $this->sut->load($name);
        $this->assertFalse($result);

        // Try to fetch an alias that does not exist.
        $name = new SiteAliasName('missing');
        $result = $this->sut->load($name);
        $this->assertFalse($result);
    }

    public function testLoadAll()
    {
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/single');
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/other');

        $all = $this->sut->loadAll();
        $this->assertEquals('@single.alternate,@single.common,@single.dev,@single.other', implode(',', array_keys($all)));
    }

    public function testLoadMultiple()
    {
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/single');
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/other');

        $aliases = $this->sut->loadMultiple('single');
        $this->assertEquals('@single.dev,@single.alternate,@single.common', implode(',', array_keys($aliases)));
    }

    public function testLoadLocation()
    {
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/single');
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/other');

        $aliases = $this->sut->loadLocation('other');
        $this->assertEquals('@other.single.dev,@other.single.other,@other.single.common', implode(',', array_keys($aliases)));
    }
}
