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
 * @license     http://teknoo.it/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\MangoPayBundle\Service;

use MangoPay\Libraries\IStorageStrategy;
use MangoPay\MangoPayApi;

/**
 * Class MangoApi
 * @package UniAlteri\MangoPayBundle\Service
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/mangopay-bundle Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @license     http://teknoo.it/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class MangoApiService
{
    /**
     * @var MangoPayApi
     */
    protected $api;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    private $clientPassPhrase;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var bool
     */
    protected $debugMode;

    /**
     * @var IStorageStrategy
     */
    private $storageStrategy;

    /**
     * @param MangoPayApi $api
     * @param string $clientId
     * @param string $clientPassPhrase
     * @param string $baseUrl
     * @param boolean $debugMode
     * @param IStorageStrategy $storageStrategy
     */
    public function __construct(
        MangoPayApi $api,
        $clientId,
        $clientPassPhrase,
        $baseUrl,
        $debugMode,
        IStorageStrategy $storageStrategy
    ) {
        $this->api = $api;
        $this->clientId = $clientId;
        $this->clientPassPhrase = $clientPassPhrase;
        $this->baseUrl = $baseUrl;
        $this->debugMode = $debugMode;
        $this->storageStrategy = $storageStrategy;

        $this->configureSdk();
    }

    /**
     * To configure the mango pay sdk
     * @return $this
     */
    protected function configureSdk()
    {
        $config = $this->api->Config;
        $config->ClientId = $this->clientId;
        $config->ClientPassword = $this->clientPassPhrase;
        $config->BaseUrl = $this->baseUrl;
        $config->DebugMode = $this->debugMode;
        $this->api->OAuthTokenManager->RegisterCustomStorageStrategy($this->storageStrategy);

        return $this;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return boolean
     */
    public function isDebugMode()
    {
        return $this->debugMode;
    }

    /**
     * @return \MangoPay\ApiUsers
     */
    public function getApiUsers()
    {
        return $this->api->Users;
    }

    /**
     * @return \MangoPay\ApiWallets
     */
    public function getApiWallets()
    {
        return $this->api->Wallets;
    }

    /**
     * @return \MangoPay\ApiPayIns
     */
    public function getApiPayIns()
    {
        return $this->api->PayIns;
    }

    /**
     * @return \MangoPay\ApiPayOuts
     */
    public function getApiPayOuts()
    {
        return $this->api->PayOuts;
    }

    /**
     * @return \MangoPay\ApiTransfers
     */
    public function getApiTransferts()
    {
        return $this->api->Transfers;
    }

    /**
     * @return \MangoPay\ApiCards
     */
    public function getApiCards()
    {
        return $this->api->Cards;
    }

    /**
     * @return \MangoPay\ApiCardPreAuthorizations
     */
    public function getApiCardPreAuthorizations()
    {
        return $this->api->CardPreAuthorizations;
    }

    /**
     * @return \MangoPay\ApiCardRegistrations
     */
    public function getApiCardRegistrations()
    {
        return $this->api->CardRegistrations;
    }

    /**
     * @return \MangoPay\ApiRefunds
     */
    public function getApiRefunds()
    {
        return $this->api->Refunds;
    }
}