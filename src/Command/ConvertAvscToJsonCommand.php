<?php

declare(strict_types=1);

namespace PhpKafka\AvscJsonConverter\Command;

use PhpKafka\AvscJsonConverter\Avro\Avro;
use PhpKafka\AvscJsonConverter\Converter\ConverterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertAvscToJsonCommand extends Command
{
    private ConverterInterface $converter;

    public function __construct(ConverterInterface $converter, string $name = null)
    {
        $this->converter = $converter;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('convert:avsc-to-json')
            ->setDescription('Convert avsc schema to json schema')
            ->addArgument('schemaDirectory', InputArgument::REQUIRED, 'Schema directory')
            ->addArgument('outputDirectory', InputArgument::REQUIRED, 'Output directory')
            ->addOption('convertOnlyValueSchema', null, InputOption::VALUE_NONE, 'Only convert value schema')
            ->addOption('noDefaultAsRequired', null, InputOption::VALUE_NONE, 'Instead of all fields, only fields with no default are required')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $count = 0;

        /** @var string $schemaDirectory */
        $schemaDirectory = $input->getArgument('schemaDirectory');

        /** @var string $outputDirectory */
        $outputDirectory = $input->getArgument('outputDirectory');

        $onlyValueSchema = (bool) $input->getOption('convertOnlyValueSchema');
        $noDefaultAsRequired = (bool) $input->getOption('noDefaultAsRequired');

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $schemaDirectory,
                \FilesystemIterator::SKIP_DOTS
            )
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (Avro::FILE_EXTENSION !== $file->getExtension()) {
                continue;
            }

            if (true === $onlyValueSchema && false === str_ends_with($file->getFilename(), 'value.avsc')) {
                continue;
            }

            ++$count;
            $avsc = file_get_contents($file->getRealPath());
            $json = $this->converter->convert($avsc, ['markNoDefaultAsRequired' => $noDefaultAsRequired]);

            if (false === file_exists($outputDirectory)) {
                mkdir($outputDirectory);
            }

            $fname = $outputDirectory . DIRECTORY_SEPARATOR . str_replace('.avsc', '.json', $file->getFilename());
            file_put_contents($fname, $json);
        }

        $output->writeln(sprintf('Successfully converted %d schemas', $count));

        return 0;
    }
}