<?php

namespace App\Console;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Office;
use Carbon\Carbon;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Capsule\Manager;
use Slim\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDatabaseCommand extends Command
{
    private App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('db:seed');
        $this->setDescription('Seed the database with initial data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Seeding database...');

        /** @var Manager $database */
        $database = $this->application->getContainer()->get('db');

        $database->getConnection()->statement("SET FOREIGN_KEY_CHECKS=0");
        $database->getConnection()->statement("TRUNCATE `employees`");
        $database->getConnection()->statement("TRUNCATE `offices`");
        $database->getConnection()->statement("TRUNCATE `companies`");
        $database->getConnection()->statement("SET FOREIGN_KEY_CHECKS=1");

        $faker = FakerFactory::create();

        $companyData = [];
        for ($i = 1; $i <= 3; $i++) {
            $companyData[] = [
                'id' => $i,
                'name' => $faker->company,
                'phone' => $faker->phoneNumber,
                'email' => $faker->companyEmail,
                'website' => $faker->url,
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5b/Verisure_information_technology_department_at_Ch%C3%A2tenay-Malabry_-_2019-01-10.jpg/1920px-Verisure_information_technology_department_at_Ch%C3%A2tenay-Malabry_-_2019-01-10.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'head_office_id' => null
            ];
        }
        $database->table('companies')->insert($companyData);

        $officeData = [];
        for ($i = 1; $i <= 3; $i++) {
            $officeData[] = [
                'id' => $i,
                'name' => 'Office ' . $faker->city,
                'address' => $faker->address,
                'city' => $faker->city,
                'zip_code' => $faker->postcode,
                'country' => $faker->country,
                'email' => $faker->companyEmail,
                'phone' => $faker->phoneNumber,
                'company_id' => $i,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }
        $database->table('offices')->insert($officeData);

        $employeeData = [];
        for ($i = 1; $i <= 10; $i++) {
            $employeeData[] = [
                'id' => $i,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'office_id' => $faker->numberBetween(1, 3),
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'job_title' => $faker->jobTitle,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }
        $database->table('employees')->insert($employeeData);

        $database->getConnection()->statement("UPDATE companies SET head_office_id = 1 WHERE id = 1;");
        $database->getConnection()->statement("UPDATE companies SET head_office_id = 2 WHERE id = 2;");
        $database->getConnection()->statement("UPDATE companies SET head_office_id = 3 WHERE id = 3;");

        $output->writeln('Database seeded successfully!');
        return 0;
    }
}