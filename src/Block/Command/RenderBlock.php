<?php namespace Anomaly\BlocksFieldType\Block\Command;

use Anomaly\BlocksFieldType\Block\BlocksModel;
use Anomaly\BlocksFieldType\Block\BlockExtension;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class BlocksFieldType
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class RenderBlock
{

    use DispatchesJobs;

    /**
     * The block instance.
     *
     * @var BlocksModel
     */
    protected $block;

    /**
     * Create a new RenderBlock instance.
     *
     * @param BlocksModel $block
     */
    public function __construct(BlocksModel $block)
    {
        $this->block = $block;
    }

    /**
     * Handle the command.
     *
     * @param Factory $view
     * @return null|string
     */
    public function handle(Factory $view)
    {
        /* @var BlockExtension $type */
        $type = $this->block->getType();

        $this->block->setContent(
            $view->make(
                $type->getNamespace('block'),
                ['block' => $this->block]
            )->render()
        );

        return $view->make(
            'anomaly.field_type.blocks::block',
            ['block' => $this->block]
        )->render();
    }
}
