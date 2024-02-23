<?php

namespace ElementskitVendor\Bamarni\Composer\Bin;

use ElementskitVendor\Composer\Composer;
use ElementskitVendor\Composer\Console\Application;
use ElementskitVendor\Composer\EventDispatcher\EventSubscriberInterface;
use ElementskitVendor\Composer\IO\IOInterface;
use ElementskitVendor\Composer\Plugin\CommandEvent;
use ElementskitVendor\Composer\Plugin\PluginEvents;
use ElementskitVendor\Composer\Plugin\PluginInterface;
use ElementskitVendor\Composer\Plugin\Capable;
use ElementskitVendor\Symfony\Component\Console\Input\ArrayInput;
use ElementskitVendor\Symfony\Component\Console\Input\InputArgument;
use ElementskitVendor\Symfony\Component\Console\Input\InputDefinition;
class Plugin implements PluginInterface, Capable, EventSubscriberInterface
{
    /**
     * @var Composer
     */
    protected $composer;
    /**
     * @var IOInterface
     */
    protected $io;
    /**
     * @return void
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
    /**
     * @return string[]
     */
    public function getCapabilities()
    {
        return ['ElementskitVendor\\Composer\\Plugin\\Capability\\CommandProvider' => 'ElementskitVendor\\Bamarni\\Composer\\Bin\\CommandProvider'];
    }
    /**
     * @return void
     */
    public function deactivate(Composer $composer, IOInterface $io)
    {
    }
    /**
     * @return void
     */
    public function uninstall(Composer $composer, IOInterface $io)
    {
    }
    /**
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [PluginEvents::COMMAND => 'onCommandEvent'];
    }
    /**
     * @param CommandEvent $event
     * @return bool
     */
    public function onCommandEvent(CommandEvent $event)
    {
        $config = new Config($this->composer);
        if ($config->isCommandForwarded()) {
            switch ($event->getCommandName()) {
                case 'update':
                case 'install':
                    return $this->onCommandEventInstallUpdate($event);
            }
        }
        return \true;
    }
    /**
     * @param CommandEvent $event
     * @return bool
     */
    protected function onCommandEventInstallUpdate(CommandEvent $event)
    {
        $command = new BinCommand();
        $command->setComposer($this->composer);
        $command->setApplication(new Application());
        $arguments = ['command' => $command->getName(), 'namespace' => 'all', 'args' => []];
        foreach (\array_filter($event->getInput()->getArguments()) as $argument) {
            $arguments['args'][] = $argument;
        }
        foreach (\array_keys(\array_filter($event->getInput()->getOptions())) as $option) {
            $arguments['args'][] = '--' . $option;
        }
        $definition = new InputDefinition();
        $definition->addArgument(new InputArgument('command', InputArgument::REQUIRED));
        $definition->addArguments($command->getDefinition()->getArguments());
        $definition->addOptions($command->getDefinition()->getOptions());
        $input = new ArrayInput($arguments, $definition);
        try {
            $returnCode = $command->run($input, $event->getOutput());
        } catch (\Exception $e) {
            return \false;
        }
        return $returnCode === 0;
    }
}
