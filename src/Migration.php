<?php

namespace MX\PhinxMigrations;

use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Setup\Module\DataSetup;
use Magento\Setup\Module\Setup;
use Phinx\Migration\AbstractMigration;
use Zend\Http\PhpEnvironment\Request;

/**
 * @package MX\PhinxMigrations
 * @author  James Halsall <james.halsall@inviqa.com>
 */
abstract class Migration extends AbstractMigration
{
    /** @var ObjectManager */
    protected $objectManager;

    /** @var SchemaSetupInterface */
    protected $schemaSetup;

    /** @var ModuleDataSetupInterface */
    protected $dataSetup;

    /** @var EavSetup */
    protected $eavSetup;

    /**
     * Initialise the migration.
     */
    protected function init()
    {
        $bootstrap = Bootstrap::create(BP, $this->getServerParams());

        $this->objectManager = $bootstrap->getObjectManager();
        $this->schemaSetup = $this->getService(Setup::class);
        $this->dataSetup = $this->getService(DataSetup::class);
        $this->eavSetup = $this->getService(EavSetup::class);
    }

    protected function getSchemaSetup(): SchemaSetupInterface
    {
        return $this->schemaSetup;
    }

    protected function getService(string $name)
    {
        return $this->objectManager->get($name);
    }

    protected function getConnection(): AdapterInterface
    {
        return $this->schemaSetup->getConnection();
    }

    protected function getServerParams(): array
    {
        $request = new Request();

        return $request->getServer()->toArray();
    }
}
