<?php

namespace Sun7Pro\Generator\Console;

use Magento\Framework\Filesystem\DriverInterface;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeModule extends BaseCommand
{
    const MODULE_FILES = [
        'etc/module.xml',
        'registration.php',
    ];

    protected $filesystem;

    public function __construct(DriverInterface $filesystem, $name = null)
    {
        $this->filesystem = $filesystem;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('sun:make:module');
        $this->addArgument(
            'module',
            InputArgument::REQUIRED,
            'Module name SomeVendor_SomeModule or path to app/code/SomeVendor/SomeModule'
        );

        $this->setDescription('Make a simple module');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $moduleInput = $input->getArgument('module');
        [$moduleName, $modulePath] = $this->getModuleNameAndPath($moduleInput);

        $this->filesystem->createDirectory($modulePath);

        foreach (self::MODULE_FILES as $filePath) {
            $stubFile = $this->getStubFile($filePath);

            $this->filesystem->filePutContents(
                $this->makeFile($modulePath, $filePath),
                str_replace('{{__MODULE_NAME__}}', $moduleName, $this->filesystem->fileGetContents($stubFile))
            );
        }
    }

    protected function getStubFile($stubFile)
    {
        return __DIR__ . '/../stubs/' . $stubFile;
    }

    protected function makeFile($modulePath, $moduleFilePath)
    {
        $dirname = $this->filesystem->getParentDirectory($moduleFilePath);
        if ($dirname !== '.') {
            $this->filesystem->createDirectory($modulePath . $dirname);
        }

        $this->filesystem->touch($modulePath . $moduleFilePath);

        return $modulePath . $moduleFilePath;
    }

    protected function getModuleNameAndPath($moduleInput)
    {
        if (preg_match('#^app/code/(\w+)/(\w+)(?:/|$)#', $moduleInput, $match)) {
            return [
                sprintf('%s_%s', $match[1], $match[2]),
                sprintf('app/code/%s/%s/', $match[1], $match[2])
            ];
        }

        if (preg_match('#^(\w+)_(\w+)$#', $moduleInput, $match)) {
            return [
                $moduleInput,
                sprintf('app/code/%s/%s/', $match[1], $match[2])
            ];
        }

        throw new InvalidArgumentException('Invalid module name or path');
    }
}
