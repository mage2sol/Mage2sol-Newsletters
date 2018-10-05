<?php

namespace Mage2sol\Newsletters\Controller\Magento\Newsletter\Subscriber;

use Magento\Customer\Api\AccountManagementInterface as CustomerAccountManagement;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Newsletter\Model\SubscriberFactory;

class NewAction extends \Magento\Newsletter\Controller\Subscriber\NewAction
{
 
     
      /**
     * @var resultJsonFactory
     */
 
     protected $resultJsonFactory;
  
     /**
     * @var CustomerAccountManagement
     */
    protected $customerAccountManagement;

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param SubscriberFactory $subscriberFactory
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param CustomerUrl $customerUrl
     * @param CustomerAccountManagement $customerAccountManagement
     */
    public function __construct(
        Context $context,
        SubscriberFactory $subscriberFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        CustomerUrl $customerUrl,
	\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        CustomerAccountManagement $customerAccountManagement
    ) {
        $this->customerAccountManagement = $customerAccountManagement;
	$this->resultJsonFactory = $resultJsonFactory;
        parent::__construct(
            $context,
            $subscriberFactory,
            $customerSession,
            $storeManager,
            $customerUrl,
	    $customerAccountManagement
        );
    }  

    /**
     * New subscription action
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function execute()
    {

	$response = [];
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $email = (string)$this->getRequest()->getPost('email');

            try {
                $this->validateEmailFormat($email);
                $this->validateGuestSubscription();
                $this->validateEmailAvailable($email);

                $subscriber = $this->_subscriberFactory->create()->loadByEmail($email);
                if ($subscriber->getId()
                    && $subscriber->getSubscriberStatus() == \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED
                ) {
			$response = [
		                'status' => 'error',
		                'msg' => 'This email address is already subscribed.',
		        ];
                }

                $status = $this->_subscriberFactory->create()->subscribe($email);
                if ($status == \Magento\Newsletter\Model\Subscriber::STATUS_NOT_ACTIVE) {
		    $response = [
		          'status' => 'ok',
		          'msg' => 'The confirmation request has been sent.',
		    ];	 
                } else {
		    $response = [
		          'status' => 'ok',
		          'msg' => 'Thank you for your subscription.',
		    ];		
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
		$response = [
	                'status' => 'error',
	                'msg' => 'There was a problem with the subscription: '. $e->getMessage(),
	        ];
            } catch (\Exception $e) {
		$response = [
	                'status' => 'error',
	                'msg' => 'Something went wrong with the subscription.: '. $e->getMessage(),
	        ];
            }
        }
        return $this->resultJsonFactory->create()->setData($response);
    }
	
}
