<?php declare(strict_types=1);

namespace App\Command;

use LinkORB\OrgSync\SynchronizationMediator\SynchronizationMediatorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class SyncOrganizationCommand extends Command
{
    public const DEFAULT_ORGANIZATION_CONFIG = 'organization.yaml';
    public const DEFAULT_TARGETS_CONFIG = 'targets.yaml';

    /** @var SynchronizationMediatorInterface */
    private $synchronizationMediator;

    /** @var string */
    private $configBasePath;

   public function __construct(SynchronizationMediatorInterface $synchronizationMediator, string $configBasePath)
    {
        $this->synchronizationMediator = $synchronizationMediator;
        $this->configBasePath = $configBasePath;

        parent::__construct();
    }

    public function configure()
    {
        $this->setName('linkorb:organization:sync')
            ->setDescription('Sync organization data with provided config')
            ->addOption(
                'organization',
                null,
                InputOption::VALUE_OPTIONAL,
                'The name of organization config',
                static::DEFAULT_ORGANIZATION_CONFIG
            )
            ->addOption(
                'targets',
                null,
                InputOption::VALUE_OPTIONAL,
                'The name of targets config',
                static::DEFAULT_TARGETS_CONFIG
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $organization = $this->synchronizationMediator->initialize(
            $this->getFromYaml($this->configBasePath . '/' . $input->getOption('targets')),
            $this->getFromYaml($this->configBasePath . '/' . $input->getOption('organization'))
        );

        $this->synchronizationMediator->pushOrganization($organization);

        $output->writeln('Synchronization finished successfully');
    }

    protected function getFromYaml(string $path): array
    {
        return Yaml::parseFile($path);
    }
}
