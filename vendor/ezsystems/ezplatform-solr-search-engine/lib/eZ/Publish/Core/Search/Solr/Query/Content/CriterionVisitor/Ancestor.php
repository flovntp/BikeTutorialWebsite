<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace eZ\Publish\Core\Search\Solr\Query\Content\CriterionVisitor;

use eZ\Publish\Core\Search\Solr\Query\CriterionVisitor;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Ancestor as AncestorCriterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

/**
 * Visits the Ancestor criterion.
 */
class Ancestor extends CriterionVisitor
{
    /**
     * Check if visitor is applicable to current criterion.
     *
     * @param Criterion $criterion
     *
     * @return bool
     */
    public function canVisit(Criterion $criterion)
    {
        return $criterion instanceof AncestorCriterion;
    }

    /**
     * Map field value to a proper Solr representation.
     *
     * @param Criterion $criterion
     * @param CriterionVisitor $subVisitor
     *
     * @return string
     */
    public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null)
    {
        $idSet = array();
        foreach ($criterion->value as $value) {
            foreach (explode('/', trim($value, '/')) as $id) {
                $idSet[$id] = true;
            }
        }

        return '(' .
            implode(
                ' OR ',
                array_map(
                    function ($value) {
                        return 'location_id_mid:"' . $value . '"';
                    },
                    array_keys($idSet)
                )
            ) .
            ')';
    }
}
