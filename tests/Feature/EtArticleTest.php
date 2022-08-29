<?php

namespace Tests\Feature;

use App\EtArticle;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EtArticleTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        factory(EtArticle::class)->create([
            'et_xml_id'    => 1,
            'edition_name'  => 'edition_name',
            'story_id'      => 'story_id',
            'story_date'    => 'story_date',
            'headline'      => 'headline',
            'byline'        => 'byline',
            'category'      => 'category',
            'drophead'      => 'drophead',
            'content'       => 'content',
        ]);

        $this->payload = [
            'et_xml_id'    => 1,
            'edition_name'  => 'edition_name',
            'story_id'      => 'story_id',
            'story_date'    => 'story_date',
            'headline'      => 'headline',
            'byline'        => 'byline',
            'category'      => 'category',
            'drophead'      => 'drophead',
            'content'       => 'content',
        ];
    }

    /** @test */


    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/et_articles', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "edition_name"           =>  ["The edition name field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_et_article()
    {
        $this->disableEH();
        $this->json('post', '/api/et_articles', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'et_xml_id'    => 1,
                    'edition_name'  => 'edition_name',
                    'story_id'      => 'story_id',
                    'story_date'    => 'story_date',
                    'headline'      => 'headline',
                    'byline'        => 'byline',
                    'category'      => 'category',
                    'drophead'      => 'drophead',
                    'content'       => 'content',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'et_xml_id',
                    'edition_name',
                    'story_id',
                    'story_date',
                    'headline',
                    'byline',
                    'category',
                    'drophead',
                    'content',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_et_articles()
    {
        $this->disableEH();
        $this->json('GET', '/api/et_articles', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'et_xml_id',
                        'edition_name',
                        'story_id',
                        'story_date',
                        'headline',
                        'byline',
                        'category',
                        'drophead',
                        'content',
                    ]
                ]
            ]);
        $this->assertCount(1, EtArticle::all());
    }

    /** @test */
    function show_et_article()
    {
        $this->disableEH();
        $this->json('get', "/api/et_articles/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'et_xml_id'    => 1,
                    'edition_name'  => 'edition_name',
                    'story_id'      => 'story_id',
                    'story_date'    => 'story_date',
                    'headline'      => 'headline',
                    'byline'        => 'byline',
                    'category'      => 'category',
                    'drophead'      => 'drophead',
                    'content'       => 'content',
                ]
            ]);
    }

    /** @test */
    function update_et_article()
    {
        $this->disableEH();
        $payload = [
            'et_xml_id'    => 1,
            'edition_name'  => 'edition_name',
            'story_id'      => 'story_id',
            'story_date'    => 'story_date',
            'headline'      => 'headline',
            'byline'        => 'byline',
            'category'      => 'category',
            'drophead'      => 'drophead',
            'content'       => 'content',
        ];

        $this->json('patch', '/api/et_articles/1', $payload,  $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'et_xml_id'    => 1,
                    'edition_name'  => 'edition_name',
                    'story_id'      => 'story_id',
                    'story_date'    => 'story_date',
                    'headline'      => 'headline',
                    'byline'        => 'byline',
                    'category'      => 'category',
                    'drophead'      => 'drophead',
                    'content'       => 'content',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'et_xml_id',
                    'edition_name',
                    'story_id',
                    'story_date',
                    'headline',
                    'byline',
                    'category',
                    'drophead',
                    'content',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }
}
