<?php declare(strict_types=1);

namespace AiDescription;

use Shopware\Core\Framework\Plugin;

class AiDescription extends Plugin
{
    public function executeComposerCommands(): bool
    {
        return true;
    }
}
