<?php

namespace RokkaCli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OrganizationMembershipAddCommand extends BaseRokkaCliCommand
{
    protected function configure()
    {
        $this
            ->setName('organization:membership:add')
            ->setDescription('Add a new membership to an organization')
            ->addArgument('email', InputArgument::REQUIRED, 'The user email')
            ->addArgument('role', InputArgument::REQUIRED, 'The roles for this membership (read, write, admin)')

            ->addOption('organization', null, InputOption::VALUE_REQUIRED, 'The organization to add the membership to (default: current organization)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->displayWarningOverridenAPI($output);
        $organizationName = $this->configuration->getOrganizationName($input->getOption('organization'));

        if (!$this->verifyOrganizationName($organizationName, $output)) {
            return -1;
        }

        if (!$this->verifyOrganizationExists($organizationName, $output)) {
            return -1;
        }

        $role = $input->getArgument('role');
        $email = $input->getArgument('email');

        $client = $this->getUserClient();

        if ($client->createMembership($organizationName, $email, $role)) {
            $membership = $client->getMembership($organizationName, $email);

            $output->writeln('Membership');
            self::outputOrganizationMembershipInfo($membership, $output);
        }

        return 0;
    }
}
