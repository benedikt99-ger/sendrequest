<?php

namespace nuenemann\widerruf\Application\Extend;

use \OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;

class Email extends Email_parent
{
    protected $_sWithdrawalEmailTemplateHtml = "@widerruf/widerrufEmailHtml.html.twig";
    protected $_sWithdrawalEmailTemplatePlain = "@widerruf/widerrufEmailPlain.html.twig";

    protected function sendWithdrawalRequest($wdf, $toUser = false)
    {
        $oShop = $this->getShop();
        $this->setMailParams($oShop);

        if ($toUser) {
            $this->setViewData("toUser", true);
            $this->setViewData("toOwner", false);

            $this->setSubject("Widerruf Ihrer Bestellung bei ".$oShop->oxshops__oxname->getRawValue());
            $this->setRecipient($wdf["email"], $wdf["name"]);
            $this->setFrom($oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue());
            if ($sWithdrawalEmail = Registry::getConfig()->getConfigParam("WiderrufEmail")) {
                $this->setReplyTo($sWithdrawalEmail);
            }
        } else {
            $this->setViewData("toUser", false);
            $this->setViewData("toOwner", true);

            $this->setSubject(" Widerruf einer Bestellung bei ".$oShop->oxshops__oxname->getRawValue());
            if ($_recipient = Registry::getConfig()->getConfigParam("WiderrufEmail")) {
                $this->setRecipient($_recipient);
            } else {
                $this->setRecipient($oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue());
            }
            if (!empty(Registry::getConfig()->getConfigParam("WiderrufCC"))) {
                foreach (Registry::getConfig()->getConfigParam("WiderrufCC") as $_ccrecipient) {
                    $this->addOrEnqueueAnAddress('cc', $_ccrecipient, '');
                }
            }
            $this->setFrom($wdf->email, $wdf->name);
        }

        $this->setViewData("wdf", $wdf);

        $oUser = Registry::getConfig()->getUser();
        if ($oUser) {
            $this->setUser($oUser);
            if ($wdf["oxorderid"]) {
                $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
                $oOrder->load($wdf["oxorderid"]);
                $this->setViewData("oOrder", $oOrder);
            }
        }
        $this->setViewData("retoureportal", Registry::getConfig()->getConfigParam("WiderrufRetoureportal"));
        $this->processViewArray();

		// siehe \vendor\oxid-esales\oxideshop-ce\source\Core
		$renderer = $this->getRenderer();// private...
		// $bridge = $this->getContainer()->get(TemplateRendererBridgeInterface::class);
		// $bridge->setEngine($this->_getSmarty());
		// $renderer = $bridge->getTemplateRenderer();

		$this->setBody($renderer->renderTemplate($this->_sWithdrawalEmailTemplateHtml, $this->getViewData()));
		$this->setAltBody($renderer->renderTemplate($this->_sWithdrawalEmailTemplatePlain, $this->getViewData()));





        return $this->send();
    }
    public function sendWithdrawalRequestToUser($wdf)
    {
        return $this->sendWithdrawalRequest($wdf, true);
    }
    public function sendWithdrawalRequestToOwner($wdf)
    {
        return $this->sendWithdrawalRequest($wdf, false);
    }
}
