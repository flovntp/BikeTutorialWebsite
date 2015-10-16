<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Data\Role;

use eZ\Publish\API\Repository\Values\User\PolicyDraft;

trait PolicyDataTrait
{
    /**
     * @var PolicyDraft
     */
    protected $policyDraft;

    /**
     * @var \eZ\Publish\API\Repository\Values\User\RoleDraft
     */
    protected $roleDraft;

    /**
     * Role the draft was created from.
     *
     * @var \eZ\Publish\API\Repository\Values\User\RoleDraft
     */
    protected $initialRole;

    /**
     * Combination of module + function as a single string.
     * Example: "content|read".
     *
     * @var string
     */
    public $moduleFunction;

    public function setPolicy(PolicyDraft $policyDraft)
    {
        $this->policy = $policyDraft;
    }

    public function getId()
    {
        return $this->policyDraft ? $this->policyDraft->id : null;
    }
}
