<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2021 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\linklist\helpers;

/**
 * Markdown Helper
 */
class MarkdownHelper
{
    /**
     * @param string $markdown
     * @return array
     */
    public static function grepLinks(string $markdown)
    {
        $pattern_square = '\[([^\]]*)\]';
        $pattern_round = '\(([^)]*)\)';
        $pattern = '/' . $pattern_square . $pattern_round . '/';
        // Replace [ and ] chars
        $markdown = str_replace(['\[', '\]'], ['{', '}'], $markdown);
        preg_match_all($pattern, $markdown, $matches);
        $links = [];
        if (!empty($matches[2][0])) {
            foreach ($matches[2] as $key => $href) {
                if ($href && !empty($matches[1][$key])) {
                    $links[$href] = $matches[1][$key];
                }
            }
        }
        return $links;
    }
}
