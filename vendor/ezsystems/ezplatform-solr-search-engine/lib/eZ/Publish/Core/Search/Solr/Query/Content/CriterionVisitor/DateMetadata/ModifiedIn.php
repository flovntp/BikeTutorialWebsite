<?php

/**
 * File containing the Content Search handler class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace eZ\Publish\Core\Search\Solr\Query\Content\CriterionVisitor\DateMetadata;

use eZ\Publish\Core\Search\Solr\Query\Content\CriterionVisitor\DateMetadata;
use eZ\Publish\Core\Search\Solr\Query\CriterionVisitor;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;

/**
 * Visits the DateMetadata criterion.
 */
class ModifiedIn extends DateMetadata
{
    /**
     * CHeck if visitor is applicable to current criterion.
     *
     * @param Criterion $criterion
     *
     * @return bool
     */
    public function canVisit(Criterion $criterion)
    {
        return
            $criterion instanceof Criterion\DateMetadata &&
            $criterion->target === 'modified' &&
            (($criterion->operator ?: Operator::IN) === Operator::IN ||
              $criterion->operator === Operator::EQ);
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
        $values = array();
        foreach ($criterion->value as $value) {
            $values[] = 'modified_dt:"' . $this->getSolrTime($value) . '"';
        }

        return '(' . implode(' OR ', $values) . ')';
    }
}
