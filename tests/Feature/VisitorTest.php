<?php

namespace Tests\Feature;

use App\Visitor;
use App\VisitorBa;
use App\VisitorNpd;
use App\VisitorStock;
use App\VisitorTester;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VisitorTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);
        $this->user->assignCompany($this->company->id);
        $this->headers['company-id'] = $this->company->id;

        factory(Visitor::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        $this->payload = [
            'user_id' => 1,
            'retailer_id' => 1,
            'visitor_bas' =>  [
                0 =>  [
                    'visitor_id' =>  1,
                ]
            ],
            'visitor_npds' =>  [
                0 =>  [
                    'visitor_id' =>  1,
                ]
            ],
            'visitor_stocks' =>  [
                0 =>  [
                    'visitor_id' =>  1,
                ]
            ],
            'visitor_testers' =>  [
                0 =>  [
                    'visitor_id' =>  1,
                ]
            ],
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/visitors', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"  =>  ["The user id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_visitor()
    {
        $this->disableEH();
        // dd($this->payload);
        $this->json('post', '/api/visitors', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => 1,
                    'retailer_id' => 1,
                    'visitor_bas' =>  [
                        0 =>  [
                            'visitor_id' =>  '2',
                        ]
                    ],
                    'visitor_npds' =>  [
                        0 =>  [
                            'visitor_id' =>  '2',
                        ]
                    ],
                    'visitor_stocks' =>  [
                        0 =>  [
                            'visitor_id' =>  '2',
                        ]
                    ],
                    'visitor_testers' =>  [
                        0 =>  [
                            'visitor_id' =>  '2',
                        ]
                    ],
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'retailer_id',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id',
                    'visitor_bas',
                    'visitor_npds',
                    'visitor_stocks',
                    'visitor_testers',
                ]
            ]);
    }

    /** @test */
    function list_of_visitors()
    {
        $this->disableEH();
        $this->json('GET', '/api/visitors', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'user_id'
                    ]
                ]
            ]);
        $this->assertCount(1, Visitor::all());
    }

    /** @test */
    function show_single_visitor()
    {
        $this->disableEH();
        $this->json('get', "/api/visitors/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id'  =>  1,
                ]
            ]);
    }

    /** @test */
    function update_single_visitor()
    {
        $this->disableEH();

        $visitor = factory(Visitor::class)->create([
            'company_id'  =>  $this->company->id
        ]);
        $visitorBa = factory(VisitorBa::class)->create([
            'visitor_id' =>  $visitor->id
        ]);
        $visitorNpd = factory(VisitorNpd::class)->create([
            'visitor_id' =>  $visitor->id
        ]);
        $visitorStock = factory(VisitorStock::class)->create([
            'visitor_id' =>  $visitor->id
        ]);
        $visitorTester = factory(VisitorTester::class)->create([
            'visitor_id' =>  $visitor->id
        ]);

        // Old Edit + No Delete + 1 New
        $payload = [
            'id'          =>  $visitor->id,
            'user_id' => 2,
            'retailer_id' => 2,
            'visitor_bas' =>  [
                0 =>  [

                    'id'        =>  $visitorBa->id,
                    'visitor_id' =>  '2',
                ],
                1 =>  [

                    'visitor_id' =>  '2',
                ],
            ],
            'visitor_npds' =>  [
                0 =>  [

                    'id'        =>  $visitorNpd->id,
                    'visitor_id' =>  '2',
                ],
                1 =>  [

                    'visitor_id' =>  '2',
                ],
            ],
            'visitor_stocks' =>  [
                0 =>  [

                    'id'        =>  $visitorStock->id,
                    'visitor_id' =>  '2',
                ],
                1 =>  [

                    'visitor_id' =>  '2',
                ],
            ],
            'visitor_testers' =>  [
                0 =>  [

                    'id'        =>  $visitorTester->id,
                    'visitor_id' =>  '2',
                ],
                1 =>  [

                    'visitor_id' =>  '2',
                ],
            ],
        ];

        $this->json('post', '/api/visitors', $payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'    => [
                    'id'          =>  $visitor->id,
                    'user_id' => 2,
                    'retailer_id' => 2,
                    'visitor_bas' =>  [
                        0 =>  [

                            'id'        =>  $visitorBa->id,
                            'visitor_id' =>  '2',
                        ],
                        1 =>  [

                            'visitor_id' =>  '2',
                        ],
                    ],
                    'visitor_npds' =>  [
                        0 =>  [

                            'id'        =>  $visitorNpd->id,
                            'visitor_id' =>  '2',
                        ],
                        1 =>  [

                            'visitor_id' =>  '2',
                        ],
                    ],
                    'visitor_stocks' =>  [
                        0 =>  [

                            'id'        =>  $visitorStock->id,
                            'visitor_id' =>  '2',
                        ],
                        1 =>  [

                            'visitor_id' =>  '2',
                        ],
                    ],
                    'visitor_testers' =>  [
                        0 =>  [

                            'id'        =>  $visitorTester->id,
                            'visitor_id' =>  '2',
                        ],
                        1 =>  [

                            'visitor_id' =>  '2',
                        ],
                    ],
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'user_id',
                    'retailer_id',
                    'name',
                    'description',
                    'created_at',
                    'updated_at',
                    'visitor_bas',
                    'visitor_npds',
                    'visitor_stocks',
                    'visitor_testers',
                ]
            ]);

        // 1 Delete + 1 New
        $payload = [
            'id'          =>  $visitor->id,
            'user_id' => 2,
            'retailer_id' => 2,
            'visitor_bas' =>  [
                0 =>  [

                    'id'        =>  $visitorBa->id,
                    'visitor_id' =>  '2',
                ],

            ],
            'visitor_npds' =>  [
                0 =>  [

                    'id'        =>  $visitorNpd->id,
                    'visitor_id' =>  '2',
                ],

            ],
            'visitor_stocks' =>  [
                0 =>  [

                    'id'        =>  $visitorStock->id,
                    'visitor_id' =>  '2',
                ],

            ],
            'visitor_testers' =>  [
                0 =>  [

                    'id'        =>  $visitorTester->id,
                    'visitor_id' =>  '2',
                ],

            ],
        ];

        $this->json('post', '/api/visitors', $payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'    => [
                    'id'          =>  $visitor->id,
                    'user_id' => 2,
                    'retailer_id' => 2,
                    'visitor_bas' =>  [
                        0 =>  [

                            'id'        =>  $visitorBa->id,
                            'visitor_id' =>  '2',
                        ],

                    ],
                    'visitor_npds' =>  [
                        0 =>  [

                            'id'        =>  $visitorNpd->id,
                            'visitor_id' =>  '2',
                        ],

                    ],
                    'visitor_stocks' =>  [
                        0 =>  [

                            'id'        =>  $visitorStock->id,
                            'visitor_id' =>  '2',
                        ],

                    ],
                    'visitor_testers' =>  [
                        0 =>  [

                            'id'        =>  $visitorTester->id,
                            'visitor_id' =>  '2',
                        ],

                    ],
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'user_id',
                    'retailer_id',
                    'name',
                    'description',
                    'created_at',
                    'updated_at',
                    'visitor_bas',
                    'visitor_npds',
                    'visitor_stocks',
                    'visitor_testers',
                ],
            ]);
    }

    /** @test */
    function delete_visitor()
    {
        $this->json('delete', '/api/visitors/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, Visitor::all());
    }
}
