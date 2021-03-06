<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\Doctrine\Tests\Form\ChoiceList;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @group legacy
 */
class LoadedEntityChoiceListCompositeIdTest extends AbstractEntityChoiceListCompositeIdTest
{
    /**
     * @return \Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface
     */
    protected function createChoiceList()
    {
        $list = parent::createChoiceList();

        // load list
        $list->getChoices();

        return $list;
    }
}
