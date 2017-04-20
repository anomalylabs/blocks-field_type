<?php namespace Anomaly\BlocksFieldType\Block;

use Anomaly\BlocksFieldType\Block\Command\RenderBlock;
use Anomaly\Streams\Platform\Addon\Command\GetAddon;
use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Model\EloquentModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class BlocksModel
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class BlocksModel extends EloquentModel
{

    use DispatchesJobs;

    /**
     * The rendered content.
     *
     * @var null|string
     */
    protected $content = null;

    /**
     * No dates.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Fillable properties.
     *
     * @var array
     */
    protected $fillable = [
        'entry_id',
        'entry_type',
        'related_id',
        'block_type',
        'sort_order',
    ];

    /**
     * Return the rendered block.
     *
     * @return string
     */
    public function render()
    {
        return $this->dispatch(new RenderBlock($this));
    }

    /**
     * Get the block type.
     *
     * @return BlockExtension
     */
    public function getType()
    {
        return $this->dispatch(new GetAddon($this->block_type));
    }

    /**
     * Return the related entry.
     *
     * @return EntryInterface
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * Return the entry relation.
     *
     * @return MorphTo
     */
    public function entry()
    {
        return $this->morphTo('entry');
    }

    /**
     * Get the content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the content.
     *
     * @param $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Return a new presenter instance.
     *
     * @return BlocksPresenter
     */
    public function newPresenter()
    {
        return new BlocksPresenter($this);
    }

    /**
     * Return a new collection instance.
     *
     * @param array $items
     * @return BlockCollection
     */
    public function newCollection(array $items = [])
    {
        return new BlockCollection($items);
    }

    /**
     * Define a polymorphic, inverse one-to-one or many relationship.
     *
     * @param  string $name
     * @param  string $type
     * @param  string $id
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function morphTo($name = null, $type = null, $id = null)
    {
        /**
         * Check that the blocks relation still
         * exists. If it does NOT then we send
         * a bogus relation back instead.
         */
        if (!class_exists($this->entry_type)) {
            return new MorphTo(
                $this->newQuery(), $this, -1, null, $type, $name
            );
        }

        return parent::morphTo($name, $type, $id);
    }
}
