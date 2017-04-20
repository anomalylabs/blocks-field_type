<?php namespace Anomaly\BlocksFieldType\Command;

use Anomaly\BlocksFieldType\BlocksFieldType;
use Anomaly\Streams\Platform\Entry\EntryCollection;
use Anomaly\Streams\Platform\Field\Contract\FieldInterface;
use Anomaly\Streams\Platform\Field\Contract\FieldRepositoryInterface;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Anomaly\Streams\Platform\Stream\Contract\StreamRepositoryInterface;
use Anomaly\Streams\Platform\Ui\Form\Multiple\MultipleFormBuilder;

/**
 * Class GetMultiformFromData
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class GetMultiformFromData
{

    /**
     * The field type instance.
     *
     * @var BlocksFieldType
     */
    protected $fieldType;

    /**
     * Create a new GetMultiformFromData instance.
     *
     * @param BlocksFieldType $fieldType
     */
    public function __construct(BlocksFieldType $fieldType)
    {
        $this->fieldType = $fieldType;
    }

    /**
     * Get the multiple form builder from the value.
     *
     * @param StreamRepositoryInterface $streams
     * @param FieldRepositoryInterface  $fields
     * @param MultipleFormBuilder       $forms
     * @return MultipleFormBuilder|null
     */
    public function handle(
        StreamRepositoryInterface $streams,
        FieldRepositoryInterface $fields,
        MultipleFormBuilder $forms
    ) {
        /* @var EntryCollection $value */
        if (!$value = $this->fieldType->getValue()) {
            return null;
        }

        foreach ($value as $item) {

            /* @var FieldInterface $field */
            if (!$field = $fields->find($item['field'])) {
                continue;
            }

            /* @var StreamInterface $stream */
            if (!$stream = $streams->find($item['stream'])) {
                continue;
            }

            /* @var BlocksFieldType $type */
            $type = $field->getType();

            $type->setPrefix($this->fieldType->getPrefix());

            $form = $type->form($field, $stream, $item['type'], $item['instance']);

            if ($item['entry']) {
                $form->setEntry($item['entry']);
            }

            $form->setReadOnly($this->fieldType->isReadOnly());

            $forms->addForm($this->fieldType->getFieldName() . '_' . $item['instance'], $form);
        }

        return $forms;
    }
}
