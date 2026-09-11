<?php

namespace nuenemann\sendrequest\Application\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Email;

class BasketController extends BasketController_parent
{

    /**
     * all basket articles
     *
     * @var object
     */
    protected $_oBasketArticles = null;	
	
    public function sendRequest()
    {
        $request = Registry::getRequest();
        $message = $request->getRequestEscapedParameter('sR_message');

        if (!$message) {
            // nothing entered, just reload the basket page
            return;
        }
		
		$session = Registry::getSession();
		$oBasket = $session->getBasket();

        $email = oxNew(Email::class);
        $email->setSubject('Preis-Anfrage');
        $email->setBody($this->buildRequestBody($message, $oBasket));
        // $email->setRecipient(Registry::getConfig()->getConfigParam('sAdminEmail'));
		$email->setRecipient('benedikt@nuenemann.net');
        $email->setFrom(Registry::getConfig()->getConfigParam('sAdminEmail'));
        $email->send();

        Registry::getUtilsView()->addErrorToDisplay('MESSAGE_SENT', false, true);

        // stay on basket, or return another controller class name to redirect
        return;
    }

    protected function buildRequestBody(string $message, $oBasket): string
    {
        $lines   = [];
        $lines[] = "Message:\n{$message}\n";
        $lines[] = "Basket contents:";
		
		// $aBasketArticles = $this->getBasketArticles();
		// $aBasketArticles = $oBasket->getContents();
		// foreach ($oBasket->getContents() as $sKey => $oBasketItem) {
		
        foreach ($oBasket->getContents() as $sKey =>  $oBasketItem) {
            $article = $oBasketItem->getArticle(false);
            if ($article) {
                $lines[] = sprintf(
                    '- %s (Art.Nr. %s) x %d',
                    $article->getFieldData('oxtitle'),
                    $article->getFieldData('oxartnum'),
                    $oBasketItem->getAmount()
                );
            }
        }

        return implode("\n", $lines);
    }
}
