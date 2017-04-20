<?php namespace Anomaly\BlocksFieldType\Command;

use Anomaly\BlocksFieldType\BlocksFieldType;
use Anomaly\Streams\Platform\Field\Contract\FieldInterface;
use Anomaly\Streams\Platform\Field\Contract\FieldRepositoryInterface;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Anomaly\Streams\Platform\Stream\Contract\StreamRepositoryInterface;
use Anomaly\Streams\Platform\Ui\Form\Multiple\MultipleFormBuilder;
use Illuminate\Http\Request;

/**
 * Class GetMultiformFromPost
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class GetMultiformFromPost
{

    /**
     * The field type instance.
     *
     * @var BlocksFieldType
     */
    protected $fieldType;

    /**
     * Create a new GetMultiformFromPost instance.
     *
     * @param BlocksFieldType $fieldType
     */
    public function __construct(BlocksFieldType $fieldType)
    {
        $this->fieldType = $fieldType;
    }

    /**
     * Handle the command.
     *
     * @param StreamRepositoryInterface $streams
     * @param FieldRepositoryInterface  $fields
     * @param MultipleFormBuilder       $forms
     * @param Request                   $request
     * @return MultipleFormBuilder|null
     */
    public function handle(StreamRepositoryInterface $streams, FieldRepositoryInterface $fields, MultipleFormBuilder $forms, Request $request)
    {
        if (!$request->has($this->fieldType->getFieldName())) {
            return null;
        }

        foreach ($request->get($this->fieldType->getFieldName()) as $item) {

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

            try {
                $form->build();
            } catch(\Exception $e) {
                dd($item);
            }

            $form->setReadOnly($this->fieldType->isReadOnly());

            $forms->addForm($this->fieldType->getFieldName() . '_' . $item['instance'], $form);
        }

        return $forms;
    }
}
