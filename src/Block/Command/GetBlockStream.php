<?php namespace Anomaly\BlocksFieldType\Block\Command;

use Anomaly\BlocksFieldType\Block\BlockExtension;
use Anomaly\Streams\Platform\Entry\EntryModel;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;

/**
 * Class BlocksFieldType
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class GetBlockStream
{

    /**
     * The block extension.
     *
     * @var BlockExtension
     */
    protected $extension;

    /**
     * Create a new GetBlockStream instance.
     *
     * @param BlockExtension $extension
     */
    public function __construct(BlockExtension $extension)
    {
        $this->extension = $extension;
    }

    /**
     * Handle the command.
     *
     * @return StreamInterface
     */
    public function handle()
    {
        $namespace = 'Anomaly\Streams\Platform\Model\\' . studly_case($this->extension->getSlug());
        $name      = studly_case($this->extension->getSlug() . '_' . $this->extension->getSlug() . '_entry_model');

        /* @var EntryModel $model */
        $model = app($namespace . '\\' . $name);

        return $model->getStream();
    }
}
