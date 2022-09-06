<?php

use App\AboutUs;
use Illuminate\Database\Seeder;

class AboutUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = AboutUs::create([
            'tagline' => 'Who We Are?',
            'info' => 'We are an Educational platform',
            'info_1' => 'We cater to Grades 6 through 12',
            'description' => 'We adapt news content from trusted publishers and provide it to students for learning relevant and relatable material. We take authentic, real-world content and make it classroom-ready for students in school. Each text is published at multiple reading levels, so content is accessible to every learner. Content is personalized to student interests, aligned for instruction, and integrated with activities and reporting that creates a holistic environment for learning. Students will find articles, videos and infographics that compliments school learning as content is aligned to their curriculum and learning outcomes. Our platform will drive student reading engagment, make for more informed learners and improve skills necessary to succeed in this rapidly changing world. Textbooks are aligned to academic standards but are usually outdated, do not address the different learning needs within a single classroom and fail to promote inclusiveness or engage students. The Internet, on the other hand, provides a wealth of engaging content but most of it is unvetted, non-conformist to learning outcomes, and rarely at a reading level that is conducive for each learner. Infakt is a one stop solution to address this problem.',
        ]);
    }
}
