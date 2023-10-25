<?php

use Coretik\Core\Builders\PostType;
// use MyNamespace\Models\FrontPage as FrontPageModel;
// use MyNamespace\Models\Page as PageModel;
use MyNamespace\Models\Contact as ContactModel;
// use MyNamespace\Models\Archive as ArchiveModel;

$wpPage = app()->schema()->get('page');
$wpPage->factory(function ($initializer, $mediator) {
    if (!empty($initializer) && app()->map()->ids()->frontPage() === $initializer) {
        // return new FrontPageModel($initializer);
    } elseif (!empty($initializer) && app()->map()->ids()->contact() === $initializer) {
        return new ContactModel($initializer);
    } else {
        return new PostType($initializer);
    }
});


// Add custom page models in schema viewer
// add_filter('coretik/services/schemaViewer/models', function ($models, $builder, $args) {
//     if ($builder->getName() !== 'page') {
//         return $models;
//     }
//     return array_merge($models, [
//         app()->schema('page', 'post')->model(app()->map()->ids()->frontPage()),
//         app()->schema('page', 'post')->model(app()->map()->ids()->contact()),
//     ]);
// }, 10, 3);

