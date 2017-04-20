<?php namespace Anomaly\BlocksFieldType\Http\Controller;

use Anomaly\BlocksFieldType\BlocksFieldType;
use Anomaly\BlocksModule\Block\BlockExtension;
use Anomaly\Streams\Platform\Addon\Extension\ExtensionCollection;
use Anomaly\Streams\Platform\Field\Contract\FieldInterface;
use Anomaly\Streams\Platform\Field\Contract\FieldRepositoryInterface;
use Anomaly\Streams\Platform\Http\Controller\PublicController;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;

/**
 * Class BlocksController
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class BlocksController extends PublicController
{

    /**
     * Choose what kind of row to add.
     *
     * @param FieldRepositoryInterface $fields
     * @param                          $field
     * @return \Illuminate\Contracts\View\View|mixed
     */
    public function choose(FieldRepositoryInterface $fields, ExtensionCollection $extensions, $field)
    {
        /* @var FieldInterface $field */
        $field = $fields->find($field);

        /* @var BlocksFieldType $type */
        $type = $field->getType();

        $related = $type->config('related', []);

        //if (!$related) {
        $related = array_map(
            function (BlockExtension $extension) {
                return $extension->getNamespace();
            },
            $extensions->search('anomaly.module.blocks::block.*')->enabled()->all()
        );

        //}

        return $this->view->make(
            'anomaly.field_type.blocks::choose',
            [
                'blocks' => array_map(
                    function ($extension) use ($extensions) {
                        return $extensions->get($extension);
                    },
                    $related
                ),
            ]
        );
    }

    /**
     * Return a form row.
     *
     * @param FieldRepositoryInterface  $fields
     * @param ExtensionCollection       $extensions
     * @param                           $field
     * @param                           $extension
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function form(
        FieldRepositoryInterface $fields,
        ExtensionCollection $extensions,
        $field,
        $extension
    ) {
        /* @var FieldInterface $field */
        /* @var BlockExtension $extension */
        $field     = $fields->find($field);
        $extension = $extensions->get($extension);

        /* @var StreamInterface $stream */
        $stream = $extension->getStream();

        /* @var BlocksFieldType $type */
        $type = $field->getType();

        $type->setPrefix($this->request->get('prefix'));

        return $type
            ->form(
                $field,
                $stream,
                $extension,
                $this->request->get('instance')
            )
            ->addFormData('field_type', $type)
            ->render();
    }
}
