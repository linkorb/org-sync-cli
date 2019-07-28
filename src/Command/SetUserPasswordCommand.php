<?php declare(strict_types=1);

namespace App\Command;

use LinkORB\OrgSync\DTO\User;
use LinkORB\OrgSync\SynchronizationMediator\SynchronizationMediatorInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;

class SetUserPasswordCommand extends Command
{
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
        $this->setName('linkorb:user:set-password')
            ->setDescription('Set password for passed user')
            ->addArgument(
                'username',
                null,
                InputArgument::REQUIRED,
                'The name of user'
            )
            ->addOption(
                'organization',
                null,
                InputOption::VALUE_OPTIONAL,
                'The name of organization config',
                SyncOrganizationCommand::DEFAULT_ORGANIZATION_CONFIG
            )
            ->addOption(
                'targets',
                null,
                InputOption::VALUE_OPTIONAL,
                'The name of targets config',
                SyncOrganizationCommand::DEFAULT_TARGETS_CONFIG
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $organization = $this->synchronizationMediator->initialize(
            $this->getFromYaml($this->configBasePath . '/' . $input->getOption('targets')),
            $this->getFromYaml($this->configBasePath . '/' . $input->getOption('organization'))
        );

        $userToSetPassword = $this->findUserToSetPassword($input->getArgument('username'), $organization->getUsers());

        if (!$userToSetPassword) {
            $output->writeln(
                sprintf('<error>No user with username "%s" found!</error>', $input->getArgument('username'))
            );

            return 1;
        }

        $password = $this->getPassword($input, $output, 'New password: ');

        if ($password !== $this->getPassword($input, $output, 'Repeat new password: ')) {
            $output->writeln('<error>Passwords don`t match!</error>');

            return 1;
        }

        $this->synchronizationMediator->setPassword($userToSetPassword, $password);

        $output->writeln('Password changed successfully');
    }

    protected function getFromYaml(string $path): array
    {
        return Yaml::parseFile($path);
    }

    private function findUserToSetPassword(string $usernameToSetPassword, array $users): ?User
    {
        foreach ($users as $user) {
            if ($user->getUsername() === $usernameToSetPassword) {
                return $user;
            }
        }

        return null;
    }

    private function getPassword(InputInterface $input, OutputInterface $output, string $message): string
    {
        $helper = $this->getHelper('question');

        $question = new Question($message);
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $question->setValidator(function ($password) {
            if (!is_string($password) || empty($password)) {
                throw new RuntimeException(
                    'Password should be non empty string'
                );
            }

            return $password;
        });

        return  $helper->ask($input, $output, $question);
    }
}
