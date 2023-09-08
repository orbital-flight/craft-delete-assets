<?php
/**
 * @copyright Copyright (c) Orbital Flight
 */

namespace orbitalflight\deleteassets\services;

use Craft;
use craft\base\Component;

class HelpersService extends Component {
    /**
     * prettyFileSize
     * Get a number and returns it a human readable representation of it (B / KB / MB)
     *
     * @param  mixed $file
     * @return string
     */
    public function prettySize(int $size): string {
        if ($size > 1024) {
            if ($size > 1024 * 1024) {
                return number_format($size / 1024 / 1024) . " MB";
            } else {
                return number_format($size / 1024) . " KB";
            }
        } else {
            return $size . " B";
        }
    }
    
    /**
     * getRatio
     * Basic percentage cross multiplication
     *
     * @param  mixed $selected
     * @param  mixed $total
     * @return int
     */
    public function getRatio(int $selected, int $total): int {
        return ($total != 0) ? round($selected * 100 / $total) : 0;
    }
}