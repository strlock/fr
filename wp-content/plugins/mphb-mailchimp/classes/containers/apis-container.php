<?php

namespace MPHB\Addons\MailChimp\Containers;

use MPHB\Addons\MailChimp\API;

class ApisContainer
{
    protected $eddLicenseApi = null;
    protected $mailchimpApi = null;

    /**
     * @return \MPHB\Addons\MailChimp\API\EddLicenseApi
     */
    public function eddLicense()
    {
        if (is_null($this->eddLicenseApi)) {
            $this->eddLicenseApi = new API\EddLicenseApi();
        }

        return $this->eddLicenseApi;
    }

    /**
     * @return \MPHB\Addons\MailChimp\API\MailChimpApi
     */
    public function mailchimp()
    {
        if (is_null($this->mailchimpApi)) {
            $this->mailchimpApi = new API\MailChimpApi(mphbmc()->settings()->getApiKey());
        }

        return $this->mailchimpApi;
    }
}
