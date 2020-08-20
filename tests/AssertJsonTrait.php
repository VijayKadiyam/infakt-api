<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;

trait AssertJsonTrait
{
    public function setUpAssertJsonTrait()
    {
        TestResponse::macro('assertJsonStructureExact', function (array $structure = null, $responseData = null)
        {
            if (is_null($structure)) {
                return $this->assertJson($this->json());
            }

            if (is_null($responseData)) {
                $responseData = $this->decodeResponseJson();
            }

            if (! array_key_exists('*', $structure)) {
                $keys = array_map(function ($value, $key) {
                    return is_array($value) ? $key : $value;
                }, $structure, array_keys($structure));

                PHPUnit::assertEquals($keys, array_keys($responseData));
            }

            foreach ($structure as $key => $value) {
                if (is_array($value) && $key === '*') {
                    PHPUnit::assertInternalType('array', $responseData);

                    foreach ($responseData as $responseDataItem) {
                        $this->assertJsonStructureExact($structure['*'], $responseDataItem);
                    }
                } elseif (is_array($value)) {
                    PHPUnit::assertArrayHasKey($key, $responseData);

                    $this->assertJsonStructureExact($structure[$key], $responseData[$key]);
                } else {
                    PHPUnit::assertArrayHasKey($value, $responseData);
                }
            }

            return $this;
        });
    }
}