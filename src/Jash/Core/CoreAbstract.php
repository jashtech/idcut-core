<?php
namespace Kickass\Jash\Core;

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

    public function setOAuthProviderBuilder(\Kickass\Jash\OAuth2\Client\Provider\KickassBuilder $OAuthProviderBuilder)
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
    

    public function setConfig(\Kickass\Jash\Config\ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function setCipher(\Kickass\Jash\Cipher\CipherInterface $cipher)
    {
        $this->cipher = $cipher;
    }
    
    public function setApiClient(\Kickass\Jash\APIClient\KickassInterface $apiClient){
        $this->apiClient = $apiClient;
    }
    
    public function getApiClient(){
        return $this->apiClient;
    }

    public function setView(\Kickass\Jash\Template\TemplateInterface $view){
        $this->view = $view;
    }

    public function getView(){
        return $this->view;
    }

    public function view(){
        return $this->getView();
    }


}
