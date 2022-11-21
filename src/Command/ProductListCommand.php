<?php

namespace App\Command;

use App\Repository\ProductRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:product-list',
    description: 'Liste tout mes produits',
)]
class ProductListCommand extends Command
{
    private ProductRepository $productRepository;

    /**
     * ProductListCommand constructor.
     *
     * @param string|null       $name
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository, string $name = null)
    {
        $this->productRepository = $productRepository;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption('category', 'c', InputOption::VALUE_OPTIONAL, 'filtre sur les catégories')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $parameters = [];

        if ($input->hasOption('category')) {
            $parameters = ['category' => $input->getOption('category')];
        }

        $products = $this->productRepository->findBy($parameters);

        foreach ($products as $product) {
            $output->writeln(
                sprintf('nom %s , categorie %d', $product->getName(), $product->getCategory()->getId())
            );
        }

        return Command::SUCCESS;
    }
}
