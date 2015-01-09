<?php

/*
 * This file is part of the GGG package.
 *
 * (c) Peter Tilsen <peter.tilsen@glassesgroupglobal.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GGG\Bundle\ODataBundle\Composer;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\Yaml\Parser;
use Composer\Script\CommandEvent;

/**
 * @author Peter Tilsen <peter.tilsen@glassesgroupglobal.com>
 */
class ScriptHandler
{
    /**
     * Flag for modification indication
     *
     * If this flag is set, PHPDataSvcUtil.php has been correspondingly modified
     *
     * @varchar
     */
    const MODIFIED = 'modified';

    /**
     *
     */
    const VENDOR_DIR = 'vendor/xrowgmbh/OData_PHP_SDK/framework/';

    /**
     * Generates OData Entity class
     *
     * <samp>
     * php <path of Client Libary>PHPDataSvcUtil.php /uri=<data service Uri> | /metadata=<service metadata file>  [/out=<output file path>] [/auth=windows|acs /u=username /p=password [/sn=servicenamespace /at=applies_to] ] [/ph=proxy-host /pp=proxy-port [/pu=proxy-user /ppwd=proxy-password] [/ups=yes|no] ]
     * </samp>
     *
     * @param $event CommandEvent A instance
     * @throws \InvalidArgumentException|FileNotFoundException
     */
    public static function buildEntity(CommandEvent $event)
    {
        $options = self::getOptions($event);
        if (!$options['odata-parameters'] || !($file = $options['odata-parameters']['file'])) {
            throw new \InvalidArgumentException('odata-parameters file must not be set');
        }

        $parameters = static::parseParameters($options['odata-parameters']);

        $vendorDir = __DIR__. '/../../../../../' . self::VENDOR_DIR;
        if (!is_dir($vendorDir)) {
            throw new FileNotFoundException('xrowgmbh/OData_PHP_SDK (https://github.com/xrowgmbh/OData_PHP_SDK) vendor is not installed');
        }

        $service = $vendorDir . 'PHPDataSvcUtil.php';
        if (!is_file($service)) {
            throw new FileNotFoundException('xrowgmbh/OData_PHP_SDK (https://github.com/xrowgmbh/OData_PHP_SDK) vendor is not installed or PHPDataSvcUtil.php missing');
        }

        static::prepareFile($service, $vendorDir);
        static::executeCommand($event, $service, $parameters['odata'], $options['process-timeout']);

        $entities = __DIR__. '/../../../../../' . $parameters['odata']['out'];
        static::prepareFile($entities, $vendorDir);
    }



    /**
     * @param $parameters
     * @throws FileNotFoundException
     *
     * @return array
     */
    protected static function parseParameters($parameters)
    {
        if (!is_file($parameters['file'])) {
            throw new FileNotFoundException('The parameter file seems invalid.');
        }

        $yamlParser = new Parser();
        $parameterKey = $parameters['parameter-key'];
        $params = $yamlParser->parse(file_get_contents($parameters['file']));
        if (!isset($params[$parameterKey])) {
            throw new FileNotFoundException('The parameter file seems invalid.');
        }
        return (array) $params[$parameterKey];
    }

    /**
     * get options
     *
     * getting either default or overwritten options. options are set in composer.json
     *
     * @param CommandEvent $event
     *
     * @return array
     */
    protected static function getOptions(CommandEvent $event)
    {
        $options = array_merge(array(
                "odata-parameters" => array(
                    "parameter-key" => "parameters",
                    "file" => "src/GGG/Bundle/OdataBundle/Resources/config/parameters.yml"
                )
            ), $event->getComposer()->getPackage()->getExtra());

        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');

        return $options;
    }

    /**
     * @param CommandEvent $event
     * @param              $service
     * @param              $params
     * @param int          $timeout
     * @throws \RuntimeException
     */
    protected static function executeCommand(CommandEvent $event, $service, $params, $timeout = 300)
    {

        $php = escapeshellarg(self::getPhp());

        $cmd = ($params['uri']) ? ' /uri=' . $params['uri'] : '/metadata=' . $params['metadata'];
        $cmd .=  ' /ups=' . (($params['ups']) ? $params['ups'] : 'no');

        $params = array_diff_key($params, array_flip(array('uri', 'ups')));
        foreach ($params as $param => $value) {
            if ($value) {
                $cmd .= ' /' . $param . '=' . $value;
            }
        }

        $process = new Process($php . ' ' . $service . $cmd, null, null, null, $timeout);
        $process->run(function ($type, $buffer) { echo $buffer; });
        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf('An error occurred when executing the "%s" command.', escapeshellarg($service . $cmd)));
        }
    }


    /**
     * @param      $file
     * @param null $path
     * @throws FileNotFoundException
     */
    protected static function prepareFile($file, $path = null)
    {
        if (!is_file($file)) {
            throw new FileNotFoundException($file . ' is not existent');
        }

        $file = new File($file);
        $splFile = $file->openFile('a+');
        $splFile->flock(LOCK_EX);

        if (strstr($splFile->fgets(), self::MODIFIED)) {
            $splFile->flock(LOCK_UN);
            echo 'File: ' . $file . ' has already been prepared. Nothing to do.';
            return;
        }

        $tmp = new \SplTempFileObject(0);
        $tmp->flock(LOCK_EX);

        $tmp->fwrite(self::getPreparationContent((($path) ? $path : $splFile->getPath())));

        foreach ($splFile as $line) {
            if (trim($line) != '<?php') {
                $tmp->fwrite($line);
            }
        }

        $splFile->ftruncate(0);

        foreach ($tmp as $line) {
            $splFile->fwrite($line);
        }

        $tmp->flock(LOCK_UN);
        $splFile->flock(LOCK_UN);
    }

    /**
     * @param $path
     *
     * @return string
     */
    protected static function getPreparationContent($path)
    {
        return "<?php #" . self::MODIFIED . PHP_EOL
        . "set_include_path(get_include_path() . PATH_SEPARATOR . \"" . $path . "\");" . PHP_EOL
        . "\$GLOBALS['ODataphp_path'] = \"" . $path . "\";" . PHP_EOL;
    }


    /**
     * @return false|null|string
     * @throws \RuntimeException
     */
    protected static function getPhp()
    {
        $phpFinder = new PhpExecutableFinder;
        if (!$phpPath = $phpFinder->find()) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }

        return $phpPath;
    }
}
