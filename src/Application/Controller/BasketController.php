<?php

namespace \nuenemann\sendrequest\Application\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Email;

class BasketController extends BasketController_parent
{
    public function sendRequest()
    {
        $request = Registry::getRequest();
        $message = $request->getRequestEscapedParameter('sR_message');

        if (!$message) {
            // nothing entered, just reload the basket page
            return;
        }

        $basket = $this->getBasket();

        $email = oxNew(Email::class);
        $email->setSubject('New basket request');
        $email->setBody($this->buildRequestBody($message, $basket));
        $email->setRecipient(Registry::getConfig()->getConfigParam('sAdminEmail'));
        $email->setFrom(Registry::getConfig()->getConfigParam('sAdminEmail'));
        $email->send();

        Registry::getUtilsView()->addErrorToDisplay('MESSAGE_SENT', false, true);

        // stay on basket, or return another controller class name to redirect
        return;
    }

    protected function buildRequestBody(string $message, $basket): string
    {
        $lines   = [];
        $lines[] = "Message:\n{$message}\n";
        $lines[] = "Basket contents:";

        foreach ($basket->getContents() as $item) {
            $article = $item->getArticle(false);
            if ($article) {
                $lines[] = sprintf(
                    '- %s (Art.Nr. %s) x %d',
                    $article->getFieldData('oxtitle'),
                    $article->getFieldData('oxartnum'),
                    $item->getAmount()
                );
            }
        }

        return implode("\n", $lines);
    }
}
