<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\MediaBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @final since sonata-project/media-bundle 3.21.0
 */
class AddMediaCommand extends BaseCommand
{
    /**
     * @var bool
     */
    protected $quiet = false;

    /**
     * NEXT_MAJOR: remove this property.
     *
     * @deprecated This property is deprecated since sonata-project/media-bundle 2.4 and will be removed in 4.0
     */
    protected $output;

    /**
     * {@inheritdoc}
     */
    public function configure(): void
    {
        $this->setName('sonata:media:add')
            ->setDescription('Add a media into the database')
            ->setDefinition([
                new InputArgument('providerName', InputArgument::REQUIRED, 'The provider'),
                new InputArgument('context', InputArgument::REQUIRED, 'The context'),
                new InputArgument('binaryContent', InputArgument::REQUIRED, 'The content'),

                new InputOption('description', null, InputOption::VALUE_OPTIONAL, 'The media description field', null),
                new InputOption('copyright', null, InputOption::VALUE_OPTIONAL, 'The media copyright field', null),
                new InputOption('author', null, InputOption::VALUE_OPTIONAL, 'The media author name field', null),
                new InputOption('enabled', null, InputOption::VALUE_OPTIONAL, 'The media enabled field', true),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $provider = $input->getArgument('providerName');
        $context = $input->getArgument('context');
        $binaryContent = $input->getArgument('binaryContent');

        $output->writeln(sprintf('Add a new media - context: %s, provider: %s, content: %s', $context, $provider, $binaryContent));

        $media = $this->getMediaManager()->create();
        $media->setBinaryContent($binaryContent);

        if ($input->getOption('description')) {
            $media->setDescription($input->getOption('description'));
        }

        if ($input->getOption('copyright')) {
            $media->setCopyright($input->getOption('copyright'));
        }

        if ($input->getOption('author')) {
            $media->setAuthorName($input->getOption('author'));
        }

        if (\in_array($input->getOption('enabled'), [1, true, 'true'], true)) {
            $media->setEnabled(true);
        } else {
            $media->setEnabled(false);
        }

        $this->getMediaManager()->save($media, $context, $provider);

        $output->writeln('done!');

        return 0;
    }
}
