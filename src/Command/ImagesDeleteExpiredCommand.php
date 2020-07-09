<?php

namespace App\Command;

use App\Repository\ImageRepository;
use App\Service\ImageHelper;
use Doctrine\ORM\EntityManagerInterface;
use MyBuilder\Bundle\CronosBundle\Annotation\Cron;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @Cron(minute="0", hour="1")
 */
class ImagesDeleteExpiredCommand extends Command
{
    protected static $defaultName = 'images:delete-expired';

    /**
     * @var ImageRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var ImageHelper
     */
    private $helper;

    public function __construct(ImageRepository $repository,
                                EntityManagerInterface $em,
                                ImageHelper $helper)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->em = $em;
        $this->helper = $helper;
    }

    protected function configure()
    {
        $this
            ->setDescription('Delete all expired images');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $images = $this->repository->findExpired();
        $count = count($images);
        $this->helper->delete($images);

        $io->success(sprintf("%d expired images are deleted", $count));

        return 0;
    }
}
