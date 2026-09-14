<?php

namespace nuenemann\sendrequest\Application\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Email;

class AccountNoticeListController extends AccountNoticeListController_parent
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
			Registry::getUtilsView()->addErrorToDisplay('SENDREQUEST_ERROR_1', false, true);
            return;
        }

        $oUser = $this->getUser();
        if ($oUser) {
			$usermail = $oUser->oxuser__oxusername->value;
			$username = $oUser->oxuser__oxfname->value." ".$oUser->oxuser__oxlname->value;
			$sLogfile = Registry::getConfig()->getLogsDir() .'bn.log';
			file_put_contents($sLogfile, trim(date('Y-m-d H:i:s')." user ".$usermail." ".$username).PHP_EOL,FILE_APPEND);			
        }		
		
		$sLogfile = Registry::getConfig()->getLogsDir() .'bn.log';
		file_put_contents($sLogfile, trim(date('Y-m-d H:i:s')." NoticeList sendRequest message ".$message).PHP_EOL,FILE_APPEND);			
		
		$session = Registry::getSession();
		// $oBasket = $session->getBasket();

        $oEmail = oxNew(Email::class);
		$adata["email"] = $usermail;
		$adata["name"] = $username;
		$adata["message"] = $message;
		$adata["subject"] = "Preis-Anfrage ".$oEmail->getShop()->oxshops__oxname->getRawValue();
		$adata["body"] = $this->buildRequestBody($message);
		
        $x = $oEmail->sendRequestToOwner($adata);
        $y = $oEmail->sendRequestToUser($adata);
		
        // $email->setSubject('Preis-Anfrage');
        // $email->setBody($this->buildRequestBody($message));
        // $email->setRecipient(Registry::getConfig()->getConfigParam('sAdminEmail'));
		// $email->setRecipient('benedikt@nuenemann.net');
        // $email->setFrom(Registry::getConfig()->getConfigParam('sAdminEmail'));
        // $email->send();
        Registry::getUtilsView()->addErrorToDisplay('SENDREQUEST_SUCCESS', false, true);

        // stay on basket, or return another controller class name to redirect
        return;
    }

    protected function buildRequestBody(string $message): string
    {
        $lines   = [];
        $lines[] = "Kommentar:\n{$message}\n";
        $lines[] = "Artikel:";
		
        foreach ($this->getNoticeProductList() as $sKey =>  $oBasketItem) {
            $article = $oBasketItem->getArticle(false);
            if ($article) {
				$sLogfile = Registry::getConfig()->getLogsDir() .'bn.log';
				$line = sprintf('- %s (Art.Nr. %s) x %d',$article->getFieldData('oxtitle'),$article->getFieldData('oxartnum'),$oBasketItem->getAmount());
				file_put_contents($sLogfile, trim(date('Y-m-d H:i:s')." ".$line).PHP_EOL,FILE_APPEND);	
				
                $lines[] = sprintf(
                    '- %s (Art.Nr. %s) ',
                    $article->getFieldData('oxtitle'),
                    $article->getFieldData('oxartnum')
                );
            }
        }

        return implode("\n", $lines);
    }
}
