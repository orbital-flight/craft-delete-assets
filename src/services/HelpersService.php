<?php
/**
 * @copyright Copyright (c) Orbital Flight
 */

namespace orbitalflight\deleteassets\services;

use Craft;
use craft\base\Component;
use craft\helpers\DateTimeHelper;

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
     * getAge
     * Returns a readable sentence about the elapsed time since a given date
     *
     * @param  mixed $dateUpdated
     * @return string
     */
    public function getAge(string $dateUpdated): string {
        $now = DateTimeHelper::now();
        $dateObj = DateTimeHelper::toDateTime($dateUpdated);
        $diff = $now->diff($dateObj);

        if ($diff->d >= 1) {
            if ($diff->d > 1) {
                return Craft::t('delete-assets', "{0} days ago", [$diff->d]);
            } else {
                return Craft::t('delete-assets', "yesterday", [$diff->d]);
            }
        } else if ($diff->h >= 1) {
            if ($diff->h > 1) {
                return Craft::t('delete-assets', "{0} hours ago", [$diff->h]);
            } else {
                return Craft::t('delete-assets', "1 hour ago", [$diff->h]);
            }
        } else if ($diff->i >= 1) {
            if ($diff->i > 1) {
                return Craft::t('delete-assets', "{0} minutes ago", [$diff->i]);
            } else {
                return Craft::t('delete-assets', "1 minute ago", [$diff->i]);
            }
        } else if ($diff->s > 6) {
            return Craft::t('delete-assets', "{0} seconds ago", [$diff->s]);
        } else {
            return Craft::t('delete-assets', "just now");
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