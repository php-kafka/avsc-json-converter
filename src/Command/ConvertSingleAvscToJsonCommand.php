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

class ConvertSingleAvscToJsonCommand extends Command
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
            ->setName('convert:single-avsc-to-json')
            ->setDescription('Convert avsc schema to json schema')
            ->addArgument('avscSchema', InputArgument::REQUIRED, 'Avsc schema file path')
            ->addArgument('jsonSchema', InputArgument::REQUIRED, 'Json schema file path')
            ->addOption(
                'noDefaultAsRequired',
                null,
                InputOption::VALUE_NONE,
                'Instead of all fields, only fields with no default are required'
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): ?int
    {
        /** @var string $avscSchema */
        $avscSchema = $input->getArgument('avscSchema');

        /** @var string $jsonSchema */
        $jsonSchema = $input->getArgument('jsonSchema');
        $outputDirectory = dirname($jsonSchema);

        $noDefaultAsRequired = (bool) $input->getOption('noDefaultAsRequired');

        if (Avro::FILE_EXTENSION !== pathinfo($avscSchema, PATHINFO_EXTENSION)) {
            $output->writeln('<error>Input schema is not of type avsc</error>');
            return -1;
        }

        /** @var string $avsc */
        $avsc = file_get_contents($avscSchema);
        $json = $this->converter->convert($avsc, ['markNoDefaultAsRequired' => $noDefaultAsRequired]);

        if (false === file_exists($outputDirectory)) {
            mkdir($outputDirectory, 0755, true);
        }

        $fname = $jsonSchema;
        file_put_contents($fname, $json);

        $output->writeln('Successfully converted avsc schema');

        return 0;
    }
}
