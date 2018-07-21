# AoQueue
Resources for PARALLEL QUEUES with Laravel.

## Installation

#### Add "Screen" in your Ubuntu.
````
$ sudo apt-get install screen
````

#### Add "AoQueue" in you project.
````
$ composer require alex-oliveira/ao-queue
````

#### Add Provider (/config/app.php)
````
'providers' => [
    /*
     * Vendors Service Providers...
     */
    \AoQueue\ServiceProvider::class,
]    
````

#### Publish Vendors
````
$ php artisan vendor:publish --tag="ao-queue" && composer du
````

#### Run Migrations
````
$ php artisan migrate
````

#### Run Seeder
````
$ php artisan db:seed --class=AoQueueSeeder
````

## Utilization

#### Initialize the Master Worker
````
$ php artisan ao-queue:start
````

#### Creating a Worker
````
<?php

namespace App\Workers;

use AoQueue\Workers\TaskWorker;

class MyWorker extends TaskWorker
{
    public function work()
    {
        // TODO: WRITE HERE THE YOUR WORKER CODE 
    }
}
````

#### Add Task to your Worker
````
AoQueue()->add(MyWorker::class, [
    $process_id, $process_2_id, $process_3_id
]);
````
````
AoQueue()->add(MyWorker::class, [
    $process_id => ['other' => 'data']
]);
````
````
AoQueue()->add(MyWorker::class, [
    $process_id => $process
]);
````