<?php

/**
 * MangoPayBundle.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/mangopay-bundle Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\MangoPayBundle\Entity\Interfaces\User;

/**
 * Interface LegalUserInterface.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/mangopay-bundle Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
interface LegalUserInterface extends UserInterface
{
    /**
     * Value for return of getLegalPersonType();.
     */
    const LEGAL_PERSON_TYPE_BUSINESS = 'BUSINESS';
    const LEGAL_PERSON_TYPE_ORGANIZATION = 'ORGANIZATION';

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getLegalPersonType();

    /**
     * @return AddressInterface
     */
    public function getHeadquartersAddress();

    /**
     * @return AddressInterface
     */
    public function getLegalRepresentativeAddress();

    /**
     * @return string
     */
    public function getLegalRepresentativeFirstName();

    /**
     * @return string
     */
    public function getLegalRepresentativeLastName();

    /**
     * @return string
     */
    public function getLegalRepresentativeEmail();

    /**
     * @return \DateTime
     */
    public function getLegalRepresentativeBirthday();

    /**
     * @return string
     */
    public function getLegalRepresentativeNationality();

    /**
     * @return string
     */
    public function getLegalRepresentativeCountryOfResidence();
}
