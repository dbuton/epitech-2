<?php

namespace App\Command;

use App\Repository\ProductRepository;
use App\Repository\StudentRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:student-list',
    description: 'Liste tout mes étudiants',
)]
class StudentListCommand extends Command
{
    private StudentRepository $studentRepository;

    /**
     * ProductListCommand constructor.
     *
     * @param string|null       $name
     * @param StudentRepository $studentRepository
     */
    public function __construct(StudentRepository $studentRepository, string $name = null)
    {
        $this->studentRepository = $studentRepository;
        parent::__construct($name);
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $students = $this->studentRepository->findAll();
        $progressBar = new ProgressBar($output, count($students));

        $progressBar->start();

        foreach ($students as $student) {
            $output->writeln(
                sprintf(
                    '%s %s, né(e) le %s',
                    $student->getFirstName(),
                    $student->getLastName(),
                    $student->getBirthdate()->format('Y-m-d')
                )
            );
            $output->writeln('');
            sleep(3);
            $progressBar->advance();
        }

        $progressBar->finish();
        return Command::SUCCESS;
    }
}
