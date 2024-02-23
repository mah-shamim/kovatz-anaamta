<?php

namespace ElementskitVendor\Bamarni\Composer\Bin;

use ElementskitVendor\Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
class CommandProvider implements CommandProviderCapability
{
    /**
     * {@inheritDoc}
     */
    public function getCommands()
    {
        return [new BinCommand()];
    }
}
