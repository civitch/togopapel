<?php


namespace App\Command;


use App\Services\Entity\UserPackEntity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeletePackUser extends Command
{
    protected static $defaultName = 'app:delete-pack-user';
    private $userPackEntity;

    public function __construct(UserPackEntity $userPackEntity)
    {
        $this->userPackEntity = $userPackEntity;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Suppression d\'un pack utilisateur!');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->userPackEntity->checkExpire();
        } catch (\Exception $e) {
            $output->writeln('Erreur '.$e->getMessage());
        }
        $output->writeln('Fin de la suppression');
        return 0;
    }
}
