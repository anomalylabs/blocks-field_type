<?php namespace Anomaly\BlocksFieldType;

use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Addon\FieldType\FieldTypeSchema;
use Anomaly\Streams\Platform\Assignment\Contract\AssignmentInterface;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class BlocksFieldTypeSchema
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 * @package       Anomaly\BlocksFieldType
 */
class BlocksFieldTypeSchema extends FieldTypeSchema
{

    /**
     * Add the field type's pivot table.
     *
     * @param Blueprint           $table
     * @param AssignmentInterface $assignment
     */
    public function addColumn(Blueprint $table, AssignmentInterface $assignment)
    {
        $table = $table->getTable() . '_' . $this->fieldType->getField();

        $this->schema->dropIfExists($table);

        $this->schema->create(
            $table,
            function (Blueprint $table) {

                $table->increments('id');
                $table->integer('entry_id');
                $table->string('entry_type');
                $table->string('block_type');
                $table->integer('related_id');
                $table->integer('sort_order')->nullable();

                $table->unique(['related_id', 'entry_id', 'entry_type'], 'unique-relations');
            }
        );
    }

    /**
     * Rename the pivot table.
     *
     * @param Blueprint $table
     * @param FieldType $from
     */
    public function renameColumn(Blueprint $table, FieldType $from)
    {
        $this->schema->rename(
            $table->getTable() . '_' . $from->getField(),
            $table->getTable() . '_' . $this->fieldType->getField()
        );
    }

    /**
     * Drop the pivot table.
     *
     * @param Blueprint $table
     */
    public function dropColumn(Blueprint $table)
    {
        $this->schema->dropIfExists(
            $table->getTable() . '_' . $this->fieldType->getField()
        );
    }
}
