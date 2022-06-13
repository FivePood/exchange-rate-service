<?php

namespace App\Command;

use App\Entity\Currency;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class UpdateCurrenciesCommand extends Command
{
    //php bin/console app:update-currencies

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setName('app:update-currencies')
            ->setDescription('Обновление курсов валют.');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(['Обновление курсов валют...', '']);

        $em = $this->container->get('doctrine');

        $url = 'https://www.cbr.ru/scripts/XML_daily.asp';

        $date = new \DateTime('now', new \DateTimeZone('Europe/Moscow'));

        $loadedCurrencies = simplexml_load_file($url);

        $currencies = $em->getRepository(Currency::class)->findAll();
        foreach ($currencies as $currency) {
            $em->getManager()->remove($currency);
            $em->getManager()->flush();
        }

        foreach ($loadedCurrencies->children() as $loadedCurrency) {
            $name = null;
            $rate = null;
            foreach ($loadedCurrency as $key => $value) {
                if ($key == "Name") {
                    $name = $value;
                }
                if ($key == "Value") {
                    $rateString = str_replace(",", ".", $value);
                    $rate = floatval($rateString);
                }
            }
            $currency = new Currency();
            $currency
                ->setName($name)
                ->setRate($rate)
                ->setCreatedAt($date);
            $em->getManager()->persist($currency);
            $em->getManager()->flush();
        }

        $output->writeln('Обновлено успешно!');

        return 1;
    }
}
