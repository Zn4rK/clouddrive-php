<?php
/**
 * @author Alex Phillips <ahp118@gmail.com>
 * Date: 7/11/15
 * Time: 10:04 AM
 */

namespace CloudDrive\Commands;

use CloudDrive\CloudDrive;

class InitCommand extends Command
{
    protected function configure()
    {
        $this->setName('init')
            ->setDescription('Initialize the command line application for use with an Amazon account');
    }

    protected function main()
    {
        if (!file_exists($this->configPath)) {
            mkdir($this->configPath, 0777, true);
        }

        $this->readConfig();

        if (!$this->config['email']) {
            throw new \Exception('Email is required for initialization.');
        }

        if (!$this->config['client-id'] || !$this->config['client-secret']) {
            throw new \Exception('Amazon Cloud Drive API credentials are required for initialization.');
        }

        $this->saveConfig();

        $this->cacheStore = $this->generateCacheStore();
        $this->clouddrive = new CloudDrive(
            $this->config['email'],
            $this->config['client-id'],
            $this->config['client-secret'],
            $this->cacheStore
        );

        $response = $this->clouddrive->getAccount()->authorize();
        if (!$response['success']) {
            $this->output->writeln($response['data']['message']);
            if (isset($response['data']['auth_url'])) {
                $this->output->writeln('Navigate to the following URL and paste in the redirect URL here.');
                $this->output->writeln($response['data']['auth_url']);

                $redirectUrl = readline();

                $response = $this->clouddrive->getAccount()->authorize($redirectUrl);
                if ($response['success']) {
                    $this->output->writeln('<info>Successfully authenticated with Amazon Cloud Drive.</info>');
                    return;
                }

                $this->output->getErrorOutput()->writeln(
                    '<error>Failed to authenticate with Amazon Cloud Drive: ' . json_encode($response['data']) . '</error>'
                );
            }
        } else {
            $this->output->writeln('<error>That user is already authenticated with Amazon Cloud Drive.</error>');
        }
    }
}
