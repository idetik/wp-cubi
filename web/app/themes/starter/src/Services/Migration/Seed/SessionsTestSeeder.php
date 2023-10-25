<?php

namespace MyNamespace\Services\Migration\Seed;

use MyNamespace\Services\Migration\Core\Migration;
use Globalis\WP\Cubi\TransientCache\Cache;

class SessionsTestSeeder extends Migration
{
    /**
     * ?migration-request=sessions-test-seeder
     */
    const MIGRATION_KEY  = 'sessions-test-seeder';

    public function migrate()
    {
        $counter = 0;

        app()->schema(APP_POST_TYPE_FORMATION, 'post')
            ->query()
            ->all()
            ->collection()
            ->each(function ($model) use (&$counter) {
                for ($i = 0; $i < rand(5, 30); $i++) {
                    $session = app()->schema(APP_POST_TYPE_SESSION, 'post')->model();
                    $session->on('saved', fn ($s) => do_action('acf/save_post', $s->id()));

                    $data = $this->sessionDataRandom();
                    foreach ($data as $key => $val) {
                        $session->$key = $val;
                    }
                    $session->formation = $model->id();
                    $session->post_status = 'publish';
                    $session->_acf = [
                        '_session_formation' => 'field_63ea81339b664',
                        '_session_date_start' => 'field_63ebd6f28b2b1',
                        '_session_date_end' => 'field_63ebd6f28b2b1',
                        '_session_date' => 'field_63ebd6f28b2b1',
                        '_session_place' => 'field_63ef86ed428fa',
                    ];
                    $session->save();
                    $counter++;
                }
            });

        $this->log('&#x2714; ' . $counter . ' sessions crées');
    }

    private function sessionDataRandom(): array
    {
        $places = [
            'en_centre',
            'a_distance',
            'inter_entreprises',
            'intra_entreprises',
        ];

        $startOffset = rand(5, 80);

        return [
            'date' => [
                'start' => app()->carbon()->today()->addWeekdays($startOffset)->format('Ymd'),
                'end' => app()->carbon()->today()->addWeekdays($startOffset + rand(2, 10))->format('Ymd'),
            ],
            'place' => $places[array_rand($places)],
        ];
    }
}
