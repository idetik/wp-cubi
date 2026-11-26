<?php

namespace Tests\Integration;

use Coretik\Core\Models\Wp\PostModel;

/**
 * The theme boots with Coretik: to keep green from the first day of the project
 */
class ThemeTest extends IntegrationTestCase
{
    public function testThemeIsActive(): void
    {
        $this->assertSame('starter', \get_stylesheet());
    }

    public function testPagesAreCoretikModels(): void
    {
        $page = self::factory()->post->create(['post_type' => 'page', 'post_title' => 'À propos']);

        $model = \coretik()->schema()->get('page')->model($page);

        $this->assertInstanceOf(PostModel::class, $model);
        $this->assertSame('À propos', $model->title());
    }

    public function testFrontPageRenders(): void
    {
        $page = self::factory()->post->create(['post_type' => 'page', 'post_title' => 'Accueil', 'post_status' => 'publish']);
        \update_option('show_on_front', 'page');
        \update_option('page_on_front', $page);

        $this->go_to(\home_url('/'));
        $this->assertTrue(\is_front_page());

        \ob_start();
        require \get_front_page_template();
        $this->assertNotSame('', trim((string) \ob_get_clean()));
    }
}
