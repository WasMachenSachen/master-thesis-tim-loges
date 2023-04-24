<?php declare(strict_types=1);

namespace Shopware\Core\Content\Mail\Service;

use League\Flysystem\FilesystemInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;

/**
 * @internal
 */
#[Package('system-settings')]
class MailerTransportLoader
{
    private Transport $envBasedTransport;

    private SystemConfigService $configService;

    private MailAttachmentsBuilder $attachmentsBuilder;

    private FilesystemInterface $filesystem;

    private EntityRepositoryInterface $documentRepository;

    /**
     * @internal
     */
    public function __construct(
        Transport $envBasedTransport,
        SystemConfigService $configService,
        MailAttachmentsBuilder $attachmentsBuilder,
        FilesystemInterface $filesystem,
        EntityRepositoryInterface $documentRepository
    ) {
        $this->envBasedTransport = $envBasedTransport;
        $this->configService = $configService;
        $this->attachmentsBuilder = $attachmentsBuilder;
        $this->filesystem = $filesystem;
        $this->documentRepository = $documentRepository;
    }

    public function fromString(string $dsn): TransportInterface
    {
        if (trim($this->configService->getString('core.mailerSettings.emailAgent')) === '') {
            return new MailerTransportDecorator(
                $this->envBasedTransport->fromString($dsn),
                $this->attachmentsBuilder,
                $this->filesystem,
                $this->documentRepository
            );
        }

        return new MailerTransportDecorator(
            $this->create(),
            $this->attachmentsBuilder,
            $this->filesystem,
            $this->documentRepository
        );
    }

    private function create(): TransportInterface
    {
        $emailAgent = $this->configService->getString('core.mailerSettings.emailAgent');

        switch ($emailAgent) {
            case 'smtp':
                return $this->createSmtpTransport($this->configService);
            case 'local':
                return new SendmailTransport($this->getSendMailCommandLineArgument($this->configService));
            default:
                throw new \RuntimeException(sprintf('Invalid mail agent given "%s"', $emailAgent));
        }
    }

    private function createSmtpTransport(SystemConfigService $configService): TransportInterface
    {
        $dsn = new Dsn(
            $this->getEncryption($configService) === 'ssl' ? 'smtps' : 'smtp',
            $configService->getString('core.mailerSettings.host'),
            $configService->getString('core.mailerSettings.username'),
            $configService->getString('core.mailerSettings.password'),
            $configService->getInt('core.mailerSettings.port'),
            $this->getEncryption($configService) !== null ? [] : ['verify_peer' => 0]
        );

        return $this->envBasedTransport->fromDsnObject($dsn);
    }

    private function getEncryption(SystemConfigService $configService): ?string
    {
        $encryption = $configService->getString('core.mailerSettings.encryption');

        switch ($encryption) {
            case 'ssl':
                return 'ssl';
            case 'tls':
                return 'tls';
            default:
                return null;
        }
    }

    private function getSendMailCommandLineArgument(SystemConfigService $configService): string
    {
        $command = '/usr/sbin/sendmail ';

        $option = $configService->getString('core.mailerSettings.sendMailOptions');

        if ($option === '') {
            $option = '-t';
        }

        if ($option !== '-bs' && $option !== '-t') {
            throw new \RuntimeException(sprintf('Given sendmail option "%s" is invalid', $option));
        }

        return $command . $option;
    }
}
