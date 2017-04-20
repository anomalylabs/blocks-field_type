<?php namespace Anomaly\BlocksFieldType\Block;

use Anomaly\Streams\Platform\Entry\EntryCollection;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class BlockCollection
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class BlockCollection extends EntryCollection
{

    use DispatchesJobs;

    /**
     * Render all the blocks.
     *
     * @return string
     */
    public function render()
    {
        return implode(
            "\n\n",
            $this->undecorate()->map(
                function (BlocksModel $block) {
                    return $block->render();
                }
            )->all()
        );
    }

    /**
     * Return the string value.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
