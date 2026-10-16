<?php

declare(strict_types=1);

namespace SharedReportTest;

use Laminas\Mvc\Application;
use Laminas\ServiceManager\ServiceManager;

/**
 * Test bootstrap, for setting up auto loading
 * @method setUpDatabase()
 */
final class Bootstrap
{
    /** @var  ServiceManager */
    protected static ServiceManager $serviceManager;

    /** @var  string This is the root directory where the test is run from which likely the test directory */
    protected static string $dir;

    protected static mixed $application;

    protected static ?self $instance = null;

    protected function __construct()
    {
    }

    /**
     * @return Bootstrap
     */
    public static function getInstance(): Bootstrap
    {
        if (!Bootstrap::$instance) {
            Bootstrap::$instance = new self();
        }

        return Bootstrap::$instance;
    }

    /**
     * @param string $dir
     * @param mixed $testModule
     */
    public function init(string $dir, mixed $testModule = null): void
    {
        Bootstrap::$dir = $dir;

        $this->setPaths();

        $zf2ModulePaths = array(dirname($dir, 2));
        if (false !== ($path = Bootstrap::findParentPath('vendor'))) {
            $zf2ModulePaths[] = $path;
        }
        if (($path = Bootstrap::findParentPath('src')) !== $zf2ModulePaths[0]) {
            $zf2ModulePaths[] = $path;
        }

        $zf2ModulePaths[] = './';

        /** @var mixed $configRaw */
        $configRaw = include $dir . '/../config/application.config.php';
        $config = is_array($configRaw) ? $configRaw : [];
        /** @var array<string,mixed> $config */

        if (!empty($testModule)) {
            foreach ((array)$testModule as $mod) {
                $modules = isset($config['modules']) && is_array($config['modules']) ? $config['modules'] : [];
                if (!in_array($mod, $modules, true)) {
                    $modules[] = $mod;
                    $config['modules'] = $modules;
                }
            }
        }

        include $dir . '/../init_autoloader.php';

        // Application::init requires an array; ensure $config is an array at this point
        $application    = Application::init($config);
        $serviceManager = $application->getServiceManager();

        Bootstrap::$serviceManager = $serviceManager;
        Bootstrap::$application    = $application;
    }

    /**
     * set paths
     */
    protected function setPaths(): void
    {
        $basePath = realpath(Bootstrap::$dir) . '/';

        set_include_path(
            implode(
                PATH_SEPARATOR,
                array($basePath,
                    $basePath . '/vendor',
                    $basePath . '/test',
                    get_include_path(),
                )
            )
        );

        if (file_exists(Bootstrap::$dir . "/autoload_classmap.php")) {
            $classList = include Bootstrap::$dir . "/autoload_classmap.php";
            if (!is_array($classList)) {
                $classList = [];
            }

            spl_autoload_register(
                function ($class) use ($classList) {
                    if (isset($classList[$class])) {
                        include $classList[$class];
                    } else {
                        $filename = str_replace('\\\\', '/', $class) . '.php';
                        if (file_exists($filename)) {
                            require $filename;
                        }
                    }
                }
            );
        }
    }

    /**
     * @param string $path
     *
     * @return boolean|string false if the path cannot be found
     */
    protected function findParentPath(string $path): bool|string
    {
        $srcDir = realpath(Bootstrap::$dir . '/../');

        if ($srcDir === false) {
            return false;
        }

        return $srcDir . '/' . $path;
    }
}
