<?php
namespace IDcut\Jash\Core;

abstract class CoreAbstract implements CoreInterface {

    private $OAuthProviderBuilder;
    private $config;
    private $cipher;
    private $apiClient;
    private $view;

    public function getOAuthProviderBuilder()
    {
        return $oAuthProviderBuilder;
    }
    
    public function getOAuthProvider(){
        return $this->OAuthProviderBuilder->buildProvider()->getProvider();
    }

    public function setOAuthProviderBuilder(\IDcut\Jash\OAuth2\Client\Provider\IDcutBuilder $OAuthProviderBuilder)
    {
        $this->OAuthProviderBuilder = $OAuthProviderBuilder;
        return $this;
    }
    
    public function getConfig()
    {
        return $this->config;
    }
    
    public function config(){
        return $this->getConfig();
    }
    

    public function setConfig(\IDcut\Jash\Config\ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function setCipher(\IDcut\Jash\Cipher\CipherInterface $cipher)
    {
        $this->cipher = $cipher;
    }

    public function getCipher()
    {
        return $this->cipher;
    }
    
    public function setApiClient(\IDcut\Jash\APIClient\IDcutInterface $apiClient){
        $this->apiClient = $apiClient;
    }
    
    public function getApiClient(){
        return $this->apiClient;
    }

    public function setView(\IDcut\Jash\Template\TemplateInterface $view){
        $this->view = $view;
    }

    public function getView(){
        return $this->view;
    }

    public function view(){
        return $this->getView();
    }


}
