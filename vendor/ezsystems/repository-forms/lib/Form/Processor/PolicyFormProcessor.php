<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Form\Processor;

use eZ\Publish\API\Repository\RoleService;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PolicyFormProcessor implements EventSubscriberInterface
{
    /**
     * @var RoleService
     */
    private $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public static function getSubscribedEvents()
    {
        return [
            RepositoryFormEvents::POLICY_UPDATE => 'processUpdatePolicy',
            RepositoryFormEvents::POLICY_SAVE => 'processSavePolicy',
            RepositoryFormEvents::POLICY_REMOVE_DRAFT => 'processRemoveDraft',
        ];
    }

    public function processUpdatePolicy(FormActionEvent $event)
    {
        // Don't update anything if we just want to cancel the draft.
        if ($event->getClickedButton() === 'removeDraft') {
            return;
        }

        /** @var \EzSystems\RepositoryForms\Data\Role\PolicyCreateData|\EzSystems\RepositoryForms\Data\Role\PolicyUpdateData $data */
        $data = $event->getData();
        if ($data->isNew() && $data->moduleFunction) {
            list($module, $function) = explode('|', $data->moduleFunction);
            $data->module = $module;
            $data->function = $function;
            $this->roleService->addPolicyByRoleDraft($data->roleDraft, $data);
        } else {
            // TODO: Save limitations. It's not possible by design to update policy module/function.
        }
    }

    public function processSavePolicy(FormActionEvent $event)
    {
        /** @var \eZ\Publish\API\Repository\Values\User\RoleDraft $roleDraft */
        $roleDraft = $event->getData()->roleDraft;
        $this->roleService->publishRoleDraft($roleDraft);
    }

    public function processRemoveDraft(FormActionEvent $event)
    {
        /** @var \EzSystems\RepositoryForms\Data\Role\PolicyCreateData|\EzSystems\RepositoryForms\Data\Role\PolicyUpdateData $data */
        $data = $event->getData();
        if (!$data->isNew()) {
            $this->roleService->removePolicyByRoleDraft($data->roleDraft, $data->policyDraft);
        }
        $this->roleService->deleteRoleDraft($data->roleDraft);
    }
}
