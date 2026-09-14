<?php

namespace nuenemann\sendrequest\Application\Extend;

use \OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;

class Email extends Email_parent
{
    protected $_sSendRequestEmailTemplateHtml = "@sendrequest/sendrequestEmailHtml.html.twig";
    protected $_sSendRequestEmailTemplatePlain = "@sendrequest/sendrequestEmailPlain.html.twig";

    protected function sendRequestWithComment($adata, $toUser = false)
    {
        $oShop = $this->getShop();
        $this->setMailParams($oShop);

        if ($toUser) {
            $this->setViewData("toUser", true);
            $this->setViewData("toOwner", false);

            $this->setSubject($adata["subject"]);
            $this->setRecipient($adata["email"], $adata["name"]);
            $this->setFrom($oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue());
            if ($sWithdrawalEmail = Registry::getConfig()->getConfigParam("WiderrufEmail")) {
                $this->setReplyTo($sWithdrawalEmail);
            }
        } else {
            $this->setViewData("toUser", false);
            $this->setViewData("toOwner", true);
             $this->setSubject($adata["subject"]);
            $this->setRecipient($oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue());
            $this->setFrom($oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue());
        }

        $this->setViewData("adata", $adata);

        $oUser = Registry::getConfig()->getUser();
        if ($oUser) {

        }
		$this->processViewArray();
		$renderer = $this->getRenderer();// private...
		$this->setBody($renderer->renderTemplate($this->_sSendRequestEmailTemplateHtml, $this->getViewData()));
		$this->setAltBody($renderer->renderTemplate($this->_sSendRequestEmailTemplatePlain, $this->getViewData()));

        return $this->send();
    }
    public function sendRequestToUser($adata)
    {
        return $this->sendRequestWithComment($adata, true);
    }
    public function sendRequestToOwner($adata)
    {
        return $this->sendRequestWithComment($adata, false);
    }
}
