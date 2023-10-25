<?php

namespace MyNamespace\Services\Seo\Modules;

use function Globalis\WP\Cubi\str_starts_with;

class Permalinks extends Module
{
    public function hooks()
    {
        \add_filter('rewrite_rules_array', [$this, 'filterRewriteRules'], 10, 1);
        // \add_filter('init', [__CLASS__, 'rewriteRulesDebug'], 99999);
    }

    public function filterRewriteRules($rewrite_rules)
    {
        $remove_patterns = \apply_filters('coretik/services/seo/permalinks/remove_patterns', [
            'author',
            'comments',
            '.*wp-register.php$',
            'category',
            'tag',
            'type',
            'date/',
            'page/',
            '.*wp-app\.php',
        ], $rewrite_rules);

        foreach ($remove_patterns as $remove_pattern) {
            $rewrite_rules = static::removeRewriteRulesStartsWith($rewrite_rules, $remove_pattern);
        }

        $remove_patterns = [
            'feed',
            'attachment',
            'trackback',
            'embed',
            'comment-page',
        ];

        $ignore = [
            '(feed|rdf|rss|rss2|atom)/?$',
        ];

        foreach ($remove_patterns as $remove_pattern) {
            $rewrite_rules = static::removeRewriteRulesContains($rewrite_rules, $remove_pattern, $ignore);
        }

        $remove_patterns = [
            'attachment',
        ];

        foreach ($remove_patterns as $remove_pattern) {
            $rewrite_rules = static::removeRewriteRulesContainsValue($rewrite_rules, $remove_pattern);
        }

        return $rewrite_rules;
    }

    public static function rewriteRulesDebug()
    {
        add_filter('rewrite_rules_array', [__CLASS__, 'rewriteRulesDebugFilter'], 99999);
        \flush_rewrite_rules();
    }

    public static function rewriteRulesDebugFilter($array)
    {
        ini_set('xdebug.var_display_max_depth', 5);
        ini_set('xdebug.var_display_max_children', 1024);
        ini_set('xdebug.var_display_max_data', 1024);
        var_dump($array);
        die;
    }

    protected static function removeRewriteRulesStartsWith($rewrite_rules, $pattern, $ignore = [])
    {
        foreach ($rewrite_rules as $regex => $rewrite) {
            if (str_starts_with($regex, $pattern) && ! \in_array($regex, $ignore)) {
                unset($rewrite_rules[$regex]);
            }
        }
        return $rewrite_rules;
    }

    protected static function removeRewriteRulesContains($rewrite_rules, $pattern, $ignore = [])
    {
        foreach ($rewrite_rules as $regex => $rewrite) {
            if (false !== \strpos($regex, $pattern) && ! \in_array($regex, $ignore)) {
                unset($rewrite_rules[$regex]);
            }
        }
        return $rewrite_rules;
    }

    protected static function removeRewriteRulesContainsValue($rewrite_rules, $pattern)
    {
        foreach ($rewrite_rules as $regex => $rewrite) {
            if (false !== \strpos($rewrite, $pattern)) {
                unset($rewrite_rules[$regex]);
            }
        }
        return $rewrite_rules;
    }
}
