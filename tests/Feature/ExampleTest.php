<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testArrayIn()
    {
        $array1 = [];
        for ($i = 1; $i <= 10; $i++) {
            $array1[] = $i;
        }

        $array2 = [];
        for ($i = 1; $i <= 5; $i++) {
            $array2[] = $i;
        }

        $matchFound = false; // variabel bantuan

        foreach ($array1 as $a1) :
            foreach ($array2 as $a2) :
                if ($a1 == $a2) {
                    echo 'bagian 1 ' . $a1 . PHP_EOL;
                    $matchFound = true;
                    break; // keluar dari loop $array2 jika sudah ditemukan
                }
            endforeach;

            if (!$matchFound) {
                echo 'sisa ' . $a1 . PHP_EOL;
            }

            // reset variabel bantuan
            $matchFound = false;
        endforeach;
    }
}
